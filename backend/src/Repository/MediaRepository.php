<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    public function getYearlyStats(int $uid, \DateTimeImmutable $yearStart, \DateTimeImmutable $yearEnd): array
    {
        return $this->createQueryBuilder('m')
            ->select(
                "SUM(CASE WHEN m.typeMedia = 'film' THEN 1 ELSE 0 END) as films",
                "SUM(CASE WHEN m.typeMedia = 'série' THEN 1 ELSE 0 END) as series",
                'COUNT(m.id) as total'
            )
            ->where('m.createdAt >= :yearStart AND m.createdAt < :yearEnd AND m.userId = :uid')
            ->setParameter('yearStart', $yearStart)
            ->setParameter('yearEnd', $yearEnd)
            ->setParameter('uid', $uid)
            ->getQuery()
            ->getSingleResult();
    }

    public function getProfileStats(int $uid): array
    {
        return $this->createQueryBuilder('m')
            ->select(
                "SUM(CASE WHEN m.typeMedia = 'film' THEN 1 ELSE 0 END) as films",
                "SUM(CASE WHEN m.typeMedia = 'série' THEN 1 ELSE 0 END) as series",
                'COUNT(m.id) as total',
                'SUM(CASE WHEN m.favorite = true THEN 1 ELSE 0 END) as favoris'
            )
            ->where('m.userId = :uid')
            ->setParameter('uid', $uid)
            ->getQuery()
            ->getSingleResult();
    }

    public function getMonthlyStats(int $uid, int $year): array
    {
        $sql = <<<SQL
            SELECT
                months.mois,
                COALESCE(SUM(m.type_media = 'film'), 0)  AS films,
                COALESCE(SUM(m.type_media = 'série'), 0) AS series,
                COALESCE(COUNT(m.id), 0)                 AS total
            FROM (
                SELECT 1 AS mois UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
                UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8
                UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12
            ) months
            LEFT JOIN media m
                ON MONTH(m.created_at) = months.mois
               AND YEAR(m.created_at) = :year
               AND m.user_id = :uid
            GROUP BY months.mois
            ORDER BY months.mois
        SQL;

        return $this->getEntityManager()->getConnection()
            ->executeQuery($sql, ['year' => $year, 'uid' => $uid])
            ->fetchAllAssociative();
    }

    public function getLeaderboard(int $uid, string $type, int $limit = 20): array
    {
        $sql = <<<SQL
            SELECT m.title, m.rating, m.image_url, m.created_at, m.commentaire, u.username, m.user_id
            FROM media m
            JOIN users u ON m.user_id = u.id
            WHERE m.user_id != :uid AND m.type_media = :type
            ORDER BY RAND()
            LIMIT :limit
        SQL;

        return $this->getEntityManager()->getConnection()
            ->executeQuery(
                $sql,
                ['uid' => $uid, 'type' => $type, 'limit' => $limit],
                ['limit' => ParameterType::INTEGER]
            )
            ->fetchAllAssociative();
    }

    public function existsByTitleTypeUser(string $title, string $type, int $uid): bool
    {
        $count = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('LOWER(m.title) = LOWER(:title) AND m.typeMedia = :type AND m.userId = :uid')
            ->setParameter('title', $title)
            ->setParameter('type', $type)
            ->setParameter('uid', $uid)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    /**
     * @return array{items: Media[], total: int}
     */
    public function search(int $uid, string $filtre, string $search, string $year, string $rating, int $page, int $perPage): array
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.userId = :uid')
            ->setParameter('uid', $uid);

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

        $items = $qb->select('m')
            ->orderBy('m.title', 'ASC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

        return ['items' => $items, 'total' => (int) $total];
    }

    public function deleteAllForUser(int $uid): void
    {
        $this->createQueryBuilder('m')
            ->delete()
            ->where('m.userId = :uid')
            ->setParameter('uid', $uid)
            ->getQuery()
            ->execute();
    }
}
