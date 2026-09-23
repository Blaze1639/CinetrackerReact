<?php

namespace App\Repository;

use App\Entity\MediaToWatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MediaToWatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaToWatch::class);
    }

    /**
     * @return MediaToWatch[]
     */
    public function search(int $uid, string $filtre, string $search): array
    {
        $qb = $this->createQueryBuilder('w')
            ->where('w.userId = :uid')
            ->setParameter('uid', $uid);

        if ($filtre === 'film' || $filtre === 'série') {
            $qb->andWhere('w.typeMedia = :type')->setParameter('type', $filtre);
        }
        if ($search) {
            $qb->andWhere('w.title LIKE :search')->setParameter('search', '%' . $search . '%');
        }

        return $qb->orderBy('w.addedDate', 'DESC')->getQuery()->getResult();
    }

    public function countByUser(int $uid): int
    {
        return (int) $this->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->where('w.userId = :uid')
            ->setParameter('uid', $uid)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return MediaToWatch[]
     */
    public function findByTitleTypeUser(string $title, string $type, int $uid): array
    {
        return $this->createQueryBuilder('w')
            ->where('LOWER(w.title) = LOWER(:title) AND w.typeMedia = :type AND w.userId = :uid')
            ->setParameter('title', $title)
            ->setParameter('type', $type)
            ->setParameter('uid', $uid)
            ->orderBy('w.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function deleteAllForUser(int $uid): void
    {
        $this->createQueryBuilder('w')
            ->delete()
            ->where('w.userId = :uid')
            ->setParameter('uid', $uid)
            ->getQuery()
            ->execute();
    }
}
