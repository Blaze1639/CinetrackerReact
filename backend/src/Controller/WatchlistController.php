<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\MediaToWatch;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/watchlist', format: 'json')]
class WatchlistController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $req, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user   = $this->getUser();
        $filtre = $req->query->get('type', '');
        $search = trim($req->query->get('search', ''));

        $qb = $em->createQueryBuilder()->select('w')->from(MediaToWatch::class, 'w')
            ->where('w.userId = :uid')->setParameter('uid', $user->getId());

        if ($filtre === 'film' || $filtre === 'série') {
            $qb->andWhere('w.typeMedia = :type')->setParameter('type', $filtre);
        }
        if ($search) {
            $qb->andWhere('w.title LIKE :search')->setParameter('search', '%' . $search . '%');
        }

        $items = $qb->orderBy('w.addedDate', 'DESC')->getQuery()->getResult();

        return $this->json(['success' => true, 'items' => array_map(fn($w) => $w->toArray(), $items)]);
    }

    #[Route('', methods: ['POST'])]
    public function add(Request $req, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user  = $this->getUser();
        $data  = json_decode($req->getContent(), true) ?? [];
        $title = trim($data['title'] ?? '');

        if (!$title) {
            return $this->json(['success' => false, 'error' => 'Titre requis'], 400);
        }

        $item = new MediaToWatch();
        $item->setTitle($title)
             ->setTypeMedia(($data['type_media'] ?? 'film') === 'série' ? 'série' : 'film')
             ->setImageUrl($data['image_url'] ?? null)
             ->setUserId($user->getId());

        $em->persist($item);
        $em->flush();

        return $this->json(['success' => true, 'id' => $item->getId(), 'message' => 'Ajouté à la liste']);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $item = $em->getRepository(MediaToWatch::class)->findOneBy(['id' => $id, 'userId' => $user->getId()]);

        if (!$item) {
            return $this->json(['success' => false, 'error' => 'Introuvable'], 404);
        }

        $data = json_decode($req->getContent(), true) ?? [];
        $item->setTitle(trim($data['title'] ?? $item->getTitle()));
        $item->setTypeMedia($data['type_media'] ?? $item->getTypeMedia());
        $item->setImageUrl($data['image_url'] ?? $item->getImageUrl());

        $em->flush();

        return $this->json(['success' => true, 'message' => 'Modifié']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $item = $em->getRepository(MediaToWatch::class)->findOneBy(['id' => $id, 'userId' => $user->getId()]);

        if (!$item) {
            return $this->json(['success' => false, 'error' => 'Introuvable'], 404);
        }

        $em->remove($item);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Supprimé']);
    }

    #[Route('/{id}/move', methods: ['POST'])]
    public function move(int $id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $item = $em->getRepository(MediaToWatch::class)->findOneBy(['id' => $id, 'userId' => $user->getId()]);

        if (!$item) {
            return $this->json(['success' => false, 'error' => 'Introuvable'], 404);
        }

        $data    = json_decode($req->getContent(), true) ?? [];
        $rating  = (float) ($data['rating'] ?? 0);

        if ($rating < 1 || $rating > 5) {
            return $this->json(['success' => false, 'error' => 'Note invalide (1-5)'], 400);
        }

        $media = new Media();
        $media->setTitle(trim($data['title'] ?? $item->getTitle()))
              ->setTypeMedia($data['type_media'] ?? $item->getTypeMedia())
              ->setImageUrl($data['image_url'] ?? $item->getImageUrl())
              ->setRating((string) $rating)
              ->setCommentaire(trim($data['commentaire'] ?? '') ?: null)
              ->setUserId($user->getId());

        $em->persist($media);
        $em->remove($item);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Déplacé vers votre liste']);
    }
}
