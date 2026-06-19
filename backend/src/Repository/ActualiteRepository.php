<?php

namespace App\Repository;

use App\Entity\Actualite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ActualiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Actualite::class);
    }

    public function findRecent(int $limit = 5): array
    {
        $sql = <<<SQL
            SELECT a.*, u.username AS admin_username
            FROM actualite a
            JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT :limit
        SQL;

        return $this->getEntityManager()->getConnection()
            ->executeQuery($sql, ['limit' => $limit], ['limit' => \PDO::PARAM_INT])
            ->fetchAllAssociative();
    }
}
