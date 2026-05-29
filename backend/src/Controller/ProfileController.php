<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MediaRepository;
use App\Repository\MediaToWatchRepository;
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
        EntityManagerInterface $em,
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        $stats = $em->createQuery(
            'SELECT
                SUM(CASE WHEN m.typeMedia = \'film\' THEN 1 ELSE 0 END) as films,
                SUM(CASE WHEN m.typeMedia = \'série\' THEN 1 ELSE 0 END) as series,
                COUNT(m.id) as total,
                SUM(CASE WHEN m.favorite = true THEN 1 ELSE 0 END) as favoris
             FROM App\Entity\Media m WHERE m.userId = :uid'
        )->setParameter('uid', $user->getId())->getSingleResult();

        $aVoir = $em->createQuery('SELECT COUNT(w.id) FROM App\Entity\MediaToWatch w WHERE w.userId = :uid')
            ->setParameter('uid', $user->getId())
            ->getSingleScalarResult();

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
    public function delete(EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $uid  = $user->getId();

        $em->createQuery('DELETE FROM App\Entity\Media m WHERE m.userId = :uid')->setParameter('uid', $uid)->execute();
        $em->createQuery('DELETE FROM App\Entity\MediaToWatch w WHERE w.userId = :uid')->setParameter('uid', $uid)->execute();
        $em->createQuery('DELETE FROM App\Entity\Notification n WHERE n.userId = :uid')->setParameter('uid', $uid)->execute();
        $em->remove($em->getRepository(User::class)->find($uid));
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Compte supprimé']);
    }
}
