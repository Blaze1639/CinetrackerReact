<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ActualiteRepository;
use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', format: 'json')]
class AccueilController extends AbstractController
{
    #[Route('/accueil', methods: ['GET'])]
    public function accueil(Request $req, MediaRepository $mediaRepo, ActualiteRepository $actualiteRepository): JsonResponse
    {
        /** @var User $user */
        $user   = $this->getUser();
        $uid    = $user->getId();
        $year   = $req->query->has('year') && is_numeric($req->query->get('year'))
                    ? (int) $req->query->get('year') : (int) date('Y');

        // Stats annuelles (date range pour éviter YEAR() non supporté en DQL)
        $yearStart = new \DateTimeImmutable("{$year}-01-01");
        $yearEnd   = new \DateTimeImmutable(($year + 1) . '-01-01');
        $yearStats = $mediaRepo->getYearlyStats($uid, $yearStart, $yearEnd);

        // Stats par mois (requête avec les 12 mois garantis)
        $months = $mediaRepo->getMonthlyStats($uid, $year);

        // Leaderboard films (1 par utilisateur, excl. soi-même, aléatoire)
        $allFilms   = $mediaRepo->getLeaderboard($uid, 'film');
        $allSeries  = $mediaRepo->getLeaderboard($uid, 'série');

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
