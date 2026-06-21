<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ActualiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', format: 'json')]
class AccueilController extends AbstractController
{
    #[Route('/accueil', methods: ['GET'])]
    public function accueil(Request $req, EntityManagerInterface $em, ActualiteRepository $actualiteRepository): JsonResponse
    {
        /** @var User $user */
        $user   = $this->getUser();
        $uid    = $user->getId();
        $year   = $req->query->has('year') && is_numeric($req->query->get('year'))
                    ? (int) $req->query->get('year') : (int) date('Y');

        // Stats annuelles (date range pour éviter YEAR() non supporté en DQL)
        $yearStart = new \DateTimeImmutable("{$year}-01-01");
        $yearEnd   = new \DateTimeImmutable(($year + 1) . '-01-01');
        $yearStats = $em->createQuery(
            'SELECT
                SUM(CASE WHEN m.typeMedia = \'film\' THEN 1 ELSE 0 END) as films,
                SUM(CASE WHEN m.typeMedia = \'série\' THEN 1 ELSE 0 END) as series,
                COUNT(m.id) as total
             FROM App\Entity\Media m
             WHERE m.createdAt >= :yearStart AND m.createdAt < :yearEnd AND m.userId = :uid'
        )->setParameter('yearStart', $yearStart)->setParameter('yearEnd', $yearEnd)->setParameter('uid', $uid)->getSingleResult();

        // Stats par mois via SQL natif (requête avec les 12 mois garantis)
        $conn = $em->getConnection();
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
        $months = $conn->executeQuery($sql, ['year' => $year, 'uid' => $uid])->fetchAllAssociative();

        // Leaderboard films (1 par utilisateur, excl. soi-même, aléatoire)
        $sqlLb = <<<SQL
            SELECT m.title, m.rating, m.image_url, m.created_at, m.commentaire, u.username, m.user_id
            FROM media m
            JOIN users u ON m.user_id = u.id
            WHERE m.user_id != :uid AND m.type_media = :type
            ORDER BY RAND()
            LIMIT 20
        SQL;

        $allFilms   = $conn->executeQuery($sqlLb, ['uid' => $uid, 'type' => 'film'])->fetchAllAssociative();
        $allSeries  = $conn->executeQuery($sqlLb, ['uid' => $uid, 'type' => 'série'])->fetchAllAssociative();

        $leaderboardFilms  = $this->pickOnePerUser($allFilms, 5);
        $leaderboardSeries = $this->pickOnePerUser($allSeries, 5);

        // Actualités
        $actualites = $actualiteRepository->findRecent(5);

        return $this->json([
            'success'            => true,
            'year_stats'         => $yearStats,
            'months'             => $months,
            'leaderboard_films'  => $leaderboardFilms,
            'leaderboard_series' => $leaderboardSeries,
            'actualites'         => $actualites,
        ]);
    }

    private function pickOnePerUser(array $rows, int $max): array
    {
        $result = [];
        $seen   = [];
        foreach ($rows as $row) {
            if (!in_array($row['user_id'], $seen)) {
                $result[] = $row;
                $seen[]   = $row['user_id'];
                if (count($result) >= $max) break;
            }
        }
        return $result;
    }
}
