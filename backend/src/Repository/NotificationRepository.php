<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function findAllWithSender(): array
    {
        $sql = <<<SQL
            SELECT n.*, u.username AS sender_username, u.email AS sender_email
            FROM notifications n JOIN users u ON n.user_id = u.id
            ORDER BY n.created_at DESC
        SQL;

        return $this->getEntityManager()->getConnection()
            ->executeQuery($sql)
            ->fetchAllAssociative();
    }

    public function countUnread(): int
    {
        return (int) $this->getEntityManager()->getConnection()
            ->executeQuery("SELECT COUNT(*) FROM notifications WHERE status = 'non_lu'")
            ->fetchOne();
    }

    public function markAsRead(int $id): void
    {
        $this->getEntityManager()->createQuery(
            "UPDATE App\Entity\Notification n SET n.status = 'lu' WHERE n.id = :id"
        )->setParameter('id', $id)->execute();
    }

    public function deleteAllForUser(int $uid): void
    {
        $this->createQueryBuilder('n')
            ->delete()
            ->where('n.userId = :uid')
            ->setParameter('uid', $uid)
            ->getQuery()
            ->execute();
    }
}
