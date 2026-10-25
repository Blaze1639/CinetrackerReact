<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\MediaToWatch;
use App\Entity\User;
use App\Repository\MediaRepository;
use App\Repository\MediaToWatchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/watchlist', format: 'json')]
class WatchlistController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $req, MediaToWatchRepository $watchRepo): JsonResponse
    {
        /** @var User $user */
        $user   = $this->getUser();
        $filtre = $req->query->get('type', '');
        $search = trim($req->query->get('search', ''));

        $items = $watchRepo->search($user->getId(), $filtre, $search);

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
    public function update(int $id, Request $req, EntityManagerInterface $em, MediaToWatchRepository $watchRepo): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $item = $watchRepo->findOneBy(['id' => $id, 'userId' => $user->getId()]);

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
    public function delete(int $id, EntityManagerInterface $em, MediaToWatchRepository $watchRepo): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $item = $watchRepo->findOneBy(['id' => $id, 'userId' => $user->getId()]);

        if (!$item) {
            return $this->json(['success' => false, 'error' => 'Introuvable'], 404);
        }

        $em->remove($item);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Supprimé']);
    }

    #[Route('/{id}/move', methods: ['POST'])]
    public function move(int $id, Request $req, EntityManagerInterface $em, MediaToWatchRepository $watchRepo, MediaRepository $mediaRepo): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $item = $watchRepo->findOneBy(['id' => $id, 'userId' => $user->getId()]);

        if (!$item) {
            return $this->json(['success' => false, 'error' => 'Introuvable'], 404);
        }

        $data    = json_decode($req->getContent(), true) ?? [];
        $rating  = (float) ($data['rating'] ?? 0);

        $title     = trim($data['title'] ?? $item->getTitle());
        $typeMedia = ($data['type_media'] ?? $item->getTypeMedia()) === 'série' ? 'série' : 'film';

        // Déjà vu : on ne crée pas de doublon, comme pour l'ajout classique.
        $existingMedia = $mediaRepo->findByTitleTypeUser($title, $typeMedia, $user->getId());
        if ($existingMedia) {
            if (!($data['increment_if_exists'] ?? false)) {
                return $this->json([
                    'success'   => false,
                    'duplicate' => true,
                    'error'     => 'Ce média existe déjà dans votre liste',
                ], 409);
            }

            $media = $existingMedia[0];
            $media->setViewCount($media->getViewCount() + 1);
            $em->remove($item);
            $em->flush();

            return $this->json([
                'success'    => true,
                'view_count' => $media->getViewCount(),
                'message'    => 'Compteur de visionnage incrémenté',
            ]);
        }

        if ($rating < 1 || $rating > 5) {
            return $this->json(['success' => false, 'error' => 'Note invalide (1-5)'], 400);
        }

        $media = new Media();
        $media->setTitle($title)
              ->setTypeMedia($typeMedia)
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
