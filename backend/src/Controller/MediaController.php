<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/media', format: 'json')]
class MediaController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $req, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user   = $this->getUser();
        $uid    = $user->getId();

        $id       = (int) $req->query->get('id', 0);
        $filtre   = $req->query->get('type', '');
        $search   = trim($req->query->get('search', ''));
        $year     = $req->query->get('year', '');
        $rating   = $req->query->get('rating', '');
        $page     = max(1, (int) $req->query->get('page', 1));
        $perPage  = 12;
        $offset   = ($page - 1) * $perPage;

        if ($id) {
            $media = $em->getRepository(Media::class)->findBy(['id' => $id, 'userId' => $uid]);
            return $this->json(['success' => true, 'media' => array_map(fn($m) => $m->toArray(), $media), 'total' => count($media), 'pages' => 1, 'page' => 1]);
        }

        $qb = $em->createQueryBuilder()->select('m')->from(Media::class, 'm')->where('m.userId = :uid')->setParameter('uid', $uid);

        if ($filtre === 'film' || $filtre === 'série') {
            $qb->andWhere('m.typeMedia = :type')->setParameter('type', $filtre);
        } elseif ($filtre === 'favorite') {
            $qb->andWhere('m.favorite = true');
        }
        if ($year) {
            $yearInt = (int) $year;
            $qb->andWhere('m.createdAt >= :yearStart AND m.createdAt < :yearEnd')
               ->setParameter('yearStart', new \DateTimeImmutable("{$yearInt}-01-01"))
               ->setParameter('yearEnd', new \DateTimeImmutable(($yearInt + 1) . '-01-01'));
        }
        if ($rating) {
            $qb->andWhere('m.rating = :rating')->setParameter('rating', $rating);
        }
        if ($search) {
            $qb->andWhere('m.title LIKE :search')->setParameter('search', '%' . $search . '%');
        }

        $total = (clone $qb)->select('COUNT(m.id)')->getQuery()->getSingleScalarResult();

        $items = $qb->select('m')->orderBy('m.title', 'ASC')->setFirstResult($offset)->setMaxResults($perPage)->getQuery()->getResult();

        return $this->json([
            'success' => true,
            'media'   => array_map(fn($m) => $m->toArray(), $items),
            'total'   => (int) $total,
            'pages'   => (int) ceil($total / $perPage),
            'page'    => $page,
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function add(Request $req, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($req->getContent(), true) ?? [];

        $title      = trim($data['title'] ?? '');
        $rating     = (float) ($data['rating'] ?? 0);
        $imageUrl   = $data['image_url'] ?? null;
        $typeMedia  = ($data['type_media'] ?? 'film') === 'série' ? 'série' : 'film';
        $commentaire = trim($data['commentaire'] ?? '');

        if (!$title) {
            return $this->json(['success' => false, 'error' => 'Le titre est requis'], 400);
        }
        if ($rating < 1 || $rating > 5) {
            return $this->json(['success' => false, 'error' => 'La note doit être entre 1 et 5'], 400);
        }

        $exists = $em->createQuery('SELECT COUNT(m.id) FROM App\Entity\Media m WHERE LOWER(m.title) = LOWER(:title) AND m.typeMedia = :type AND m.userId = :uid')
            ->setParameter('title', $title)->setParameter('type', $typeMedia)->setParameter('uid', $user->getId())
            ->getSingleScalarResult();

        if ($exists > 0) {
            return $this->json(['success' => false, 'error' => 'Ce média existe déjà dans votre liste'], 400);
        }

        $media = new Media();
        $media->setTitle($title)->setRating((string) $rating)->setImageUrl($imageUrl)
              ->setTypeMedia($typeMedia)->setCommentaire($commentaire ?: null)->setUserId($user->getId());

        $em->persist($media);
        $em->flush();

        return $this->json(['success' => true, 'id' => $media->getId(), 'message' => ucfirst($typeMedia) . ' ajouté avec succès']);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user  = $this->getUser();
        $media = $em->getRepository(Media::class)->findOneBy(['id' => $id, 'userId' => $user->getId()]);

        if (!$media) {
            return $this->json(['success' => false, 'error' => 'Média introuvable'], 404);
        }

        $data = json_decode($req->getContent(), true) ?? [];
        $media->setTitle(trim($data['title'] ?? $media->getTitle()));
        $media->setTypeMedia($data['type_media'] ?? $media->getTypeMedia());
        $media->setImageUrl($data['image_url'] ?? $media->getImageUrl());
        $media->setRating((string) ($data['rating'] ?? $media->getRating()));
        $media->setCommentaire(trim($data['commentaire'] ?? '') ?: null);

        $em->flush();

        return $this->json(['success' => true, 'message' => 'Média modifié']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user  = $this->getUser();
        $media = $em->getRepository(Media::class)->findOneBy(['id' => $id, 'userId' => $user->getId()]);

        if (!$media) {
            return $this->json(['success' => false, 'error' => 'Média introuvable'], 404);
        }

        $em->remove($media);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Média supprimé']);
    }

    #[Route('/{id}/favorite', methods: ['POST'])]
    public function toggleFavorite(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user  = $this->getUser();
        $media = $em->getRepository(Media::class)->findOneBy(['id' => $id, 'userId' => $user->getId()]);

        if (!$media) {
            return $this->json(['success' => false, 'error' => 'Média introuvable'], 404);
        }

        $media->setFavorite(!$media->isFavorite());
        $em->flush();

        return $this->json(['success' => true, 'favorite' => (int) $media->isFavorite()]);
    }

    #[Route('/{id}/increment', methods: ['POST'])]
    public function increment(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user  = $this->getUser();
        $media = $em->getRepository(Media::class)->findOneBy(['id' => $id, 'userId' => $user->getId()]);

        if (!$media) {
            return $this->json(['success' => false, 'error' => 'Média introuvable'], 404);
        }

        $media->setViewCount($media->getViewCount() + 1);
        $em->flush();

        return $this->json(['success' => true, 'view_count' => $media->getViewCount()]);
    }
}
