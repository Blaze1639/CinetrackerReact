<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MediaRepository;
use App\Repository\MediaToWatchRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', format: 'json')]
class ProfileController extends AbstractController
{
    #[Route('/profile', methods: ['GET'])]
    public function profile(
        MediaRepository $mediaRepo,
        MediaToWatchRepository $watchRepo,
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $stats = $mediaRepo->getProfileStats($user->getId());
        $aVoir = $watchRepo->countByUser($user->getId());

        return $this->json([
            'success'       => true,
            'username'      => $user->getUsername(),
            'email'         => $user->getEmail(),
            'role'          => $user->getRole(),
            'created_at'    => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'total_films'   => (int) ($stats['films'] ?? 0),
            'total_series'  => (int) ($stats['series'] ?? 0),
            'total_media'   => (int) ($stats['total'] ?? 0),
            'total_favoris' => (int) ($stats['favoris'] ?? 0),
            'total_a_voir'  => (int) $aVoir,
        ]);
    }

    #[Route('/delete', methods: ['POST'])]
    public function delete(
        MediaRepository $mediaRepo,
        MediaToWatchRepository $watchRepo,
        NotificationRepository $notificationRepo,
        UserRepository $userRepo,
        EntityManagerInterface $em,
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        $uid  = $user->getId();

        $mediaRepo->deleteAllForUser($uid);
        $watchRepo->deleteAllForUser($uid);
        $notificationRepo->deleteAllForUser($uid);
        $em->remove($userRepo->find($uid));
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Compte supprimé']);
    }
}
