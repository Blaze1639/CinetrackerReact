<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/notifications', format: 'json')]
class NotificationController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getRole() !== 'admin') {
            return $this->json(['success' => false, 'error' => 'Non autorisé'], 403);
        }

        $conn = $em->getConnection();
        $notifications = $conn->executeQuery(
            'SELECT n.*, u.username AS sender_username, u.email AS sender_email
             FROM notifications n JOIN users u ON n.user_id = u.id
             ORDER BY n.created_at DESC'
        )->fetchAllAssociative();

        $unread = (int) $conn->executeQuery("SELECT COUNT(*) FROM notifications WHERE status = 'non_lu'")->fetchOne();

        return $this->json(['success' => true, 'notifications' => $notifications, 'unread_count' => $unread]);
    }

    #[Route('/{id}/read', methods: ['GET'])]
    public function markRead(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getRole() !== 'admin') {
            return $this->json(['success' => false, 'error' => 'Non autorisé'], 403);
        }

        $em->createQuery("UPDATE App\Entity\Notification n SET n.status = 'lu' WHERE n.id = :id")
            ->setParameter('id', $id)->execute();

        return $this->json(['success' => true, 'message' => 'Marqué comme lu']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getRole() !== 'admin') {
            return $this->json(['success' => false, 'error' => 'Non autorisé'], 403);
        }

        $notif = $em->getRepository(Notification::class)->find($id);
        if ($notif) {
            $em->remove($notif);
            $em->flush();
        }

        return $this->json(['success' => true, 'message' => 'Notification supprimée']);
    }

    #[Route('', methods: ['POST'])]
    public function send(Request $req, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($req->getContent(), true) ?? [];

        $typeMessage = trim($data['type_message'] ?? '');
        $message     = trim($data['message'] ?? '');

        if (!$typeMessage || !$message) {
            return $this->json(['success' => false, 'error' => 'Champs manquants'], 400);
        }
        if (strlen($message) < 10) {
            return $this->json(['success' => false, 'error' => 'Message trop court (min 10 caractères)'], 400);
        }

        $notif = new Notification();
        $notif->setUserId($user->getId())
              ->setTypeMessage($typeMessage)
              ->setMessage($message)
              ->setUsername($user->getUsername());

        $em->persist($notif);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Message envoyé']);
    }
}
