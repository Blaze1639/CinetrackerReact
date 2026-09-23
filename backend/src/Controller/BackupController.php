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

#[Route('/api/backup', format: 'json')]
class BackupController extends AbstractController
{
    #[Route('/import', methods: ['POST'])]
    public function import(
        Request $request,
        EntityManagerInterface $em,
        MediaRepository $mediaRepo,
        MediaToWatchRepository $watchRepo,
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (!is_array($data) || !is_array($data['media'] ?? null) || !is_array($data['watchlist'] ?? null)) {
            return $this->json(['success' => false, 'error' => 'Fichier de sauvegarde invalide'], 400);
        }

        $uid = $user->getId();
        $mediaCount = 0;
        $watchlistCount = 0;

        foreach ($data['media'] as $item) {
            if (!is_array($item)) {
                continue;
            }

            $title = trim((string) ($item['title'] ?? ''));
            $type = $this->normalizeType($item['type_media'] ?? 'film');
            if (!$title || !$type) {
                continue;
            }

            $matches = $mediaRepo->findByTitleTypeUser($title, $type, $uid);
            $media = $matches[0] ?? (new Media())->setUserId($uid);
            foreach (array_slice($matches, 1) as $duplicate) {
                $em->remove($duplicate);
            }

            $media->setTitle($title)
                ->setTypeMedia($type)
                ->setImageUrl($item['image_url'] ?? null)
                ->setRating($this->normalizeRating($item['rating'] ?? null))
                ->setCommentaire($this->normalizeNullableString($item['commentaire'] ?? null))
                ->setFavorite((bool) ($item['favorite'] ?? false))
                ->setViewCount(max(0, (int) ($item['view_count'] ?? 0)));

            if (!empty($item['created_at'])) {
                try {
                    $media->setCreatedAt(new \DateTimeImmutable((string) $item['created_at']));
                } catch (\Exception) {
                    // Keep the existing or default date when the backup date is invalid.
                }
            }

            if (!$media->getId()) {
                $em->persist($media);
            }
            $mediaCount++;
        }

        foreach ($data['watchlist'] as $item) {
            if (!is_array($item)) {
                continue;
            }

            $title = trim((string) ($item['title'] ?? ''));
            $type = $this->normalizeType($item['type_media'] ?? 'film');
            if (!$title || !$type) {
                continue;
            }

            $matches = $watchRepo->findByTitleTypeUser($title, $type, $uid);
            $watch = $matches[0] ?? (new MediaToWatch())->setUserId($uid);
            foreach (array_slice($matches, 1) as $duplicate) {
                $em->remove($duplicate);
            }

            $watch->setTitle($title)
                ->setTypeMedia($type)
                ->setImageUrl($item['image_url'] ?? null);

            if (!empty($item['added_date'])) {
                try {
                    $watch->setAddedDate(new \DateTimeImmutable((string) $item['added_date']));
                } catch (\Exception) {
                    // Keep the existing or default date when the backup date is invalid.
                }
            }

            if (!$watch->getId()) {
                $em->persist($watch);
            }
            $watchlistCount++;
        }

        $em->flush();

        return $this->json([
            'success' => true,
            'media_imported' => $mediaCount,
            'watchlist_imported' => $watchlistCount,
            'message' => 'Sauvegarde importée et doublons fusionnés',
        ]);
    }

    private function normalizeType(mixed $type): ?string
    {
        $type = mb_strtolower(trim((string) $type));
        return $type === 'film' ? 'film' : ($type === 'série' || $type === 'serie' ? 'série' : null);
    }

    private function normalizeRating(mixed $rating): ?string
    {
        if ($rating === null || $rating === '') {
            return null;
        }

        $rating = (float) $rating;
        return $rating >= 1 && $rating <= 5 ? (string) $rating : null;
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        return $value === '' ? null : $value;
    }
}