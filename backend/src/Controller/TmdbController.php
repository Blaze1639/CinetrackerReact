<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/api/tmdb', format: 'json')]
class TmdbController extends AbstractController
{
    // Clé déjà utilisée par le frontend ; TMDB_API_KEY permet de la remplacer sur Render.
    private const DEFAULT_API_KEY = '22eef7e96585baa751a8384b942e4470';

    #[Route('/search', methods: ['GET'])]
    public function search(Request $req, CacheInterface $cache): JsonResponse
    {
        $query = trim((string) $req->query->get('query', ''));
        $type = (string) $req->query->get('type', 'all');
        if (!in_array($type, ['all', 'movie', 'tv'], true)) {
            $type = 'all';
        }

        $year = (string) $req->query->get('year', '');
        // « Dune 2021 » : une année en fin de requête sert de filtre plutôt que de mot-clé.
        if ($year === '' && preg_match('/^(.{2,}?)\s+\(?((?:18|19|20)\d{2})\)?$/u', $query, $m)) {
            $query = trim($m[1]);
            $year = $m[2];
        }
        if (!preg_match('/^(18|19|20)\d{2}$/', $year)) {
            $year = '';
        }

        if (mb_strlen($query) < 2) {
            return $this->json(['success' => true, 'results' => []]);
        }

        // Les résultats TMDB changent peu : une recherche déjà faite répond sans appel externe.
        $cacheKey = 'tmdb_search_v2_' . md5(mb_strtolower($query) . '|' . $type . '|' . $year);
        $results = $cache->get($cacheKey, function (ItemInterface $item) use ($query, $type, $year) {
            $item->expiresAfter(86400);
            $results = $this->fetchResults($query, $type, $year);
            if ($results === null) {
                $item->expiresAfter(0);
            }

            return $results;
        });

        if ($results === null) {
            return $this->json(['success' => false, 'error' => 'La recherche TMDB est indisponible.'], 502);
        }

        return $this->json(['success' => true, 'results' => $results]);
    }

    /**
     * @return array<int, array<string, mixed>>|null null quand TMDB ne répond pas
     */
    private function fetchResults(string $query, string $type, string $year): ?array
    {
        $types = $type === 'all' ? ['movie', 'tv'] : [$type];
        $results = [];
        $failures = 0;

        foreach ($types as $mediaType) {
            $items = $this->fetchType($query, $mediaType, $year);
            if ($items === null) {
                $failures++;
                continue;
            }
            foreach ($items as $item) {
                $isMovie = $mediaType === 'movie';
                $date = $isMovie ? ($item['release_date'] ?? '') : ($item['first_air_date'] ?? '');
                $title = $isMovie ? ($item['title'] ?? '') : ($item['name'] ?? '');
                $original = $isMovie ? ($item['original_title'] ?? '') : ($item['original_name'] ?? '');
                $results[] = [
                    'id'             => $item['id'] ?? null,
                    'media_type'     => $mediaType,
                    'title'          => $title,
                    'original_title' => $original !== $title ? $original : null,
                    'year'           => $date ? substr($date, 0, 4) : null,
                    'poster_path'    => $item['poster_path'] ?? null,
                    'overview'       => mb_substr(trim($item['overview'] ?? ''), 0, 160),
                    'score'          => $this->relevance($query, $title, $original, (float) ($item['popularity'] ?? 0), (int) ($item['vote_count'] ?? 0)),
                ];
            }
        }

        if ($failures === count($types)) {
            return null;
        }

        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_map(function ($r) {
            unset($r['score']);
            return $r;
        }, array_slice($results, 0, 20));
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    private function fetchType(string $query, string $mediaType, string $year): ?array
    {
        $apiKey = $_ENV['TMDB_API_KEY'] ?? $_SERVER['TMDB_API_KEY'] ?? self::DEFAULT_API_KEY;
        $params = [
            'api_key'       => $apiKey,
            'language'      => 'fr-FR',
            'query'         => $query,
            'include_adult' => 'false',
        ];
        if ($year !== '') {
            $params[$mediaType === 'movie' ? 'primary_release_year' : 'first_air_date_year'] = $year;
        }

        $url = 'https://api.themoviedb.org/3/search/' . $mediaType . '?' . http_build_query($params);
        $context = stream_context_create(['http' => ['timeout' => 8, 'ignore_errors' => true]]);
        $body = @file_get_contents($url, false, $context);
        $data = $body !== false ? json_decode($body, true) : null;

        return is_array($data) && is_array($data['results'] ?? null) ? $data['results'] : null;
    }

    /**
     * Classe d'abord les titres qui correspondent à la saisie, la popularité ne sert qu'à départager.
     */
    private function relevance(string $query, string $title, string $original, float $popularity, int $votes): float
    {
        $q = $this->normalize($query);
        $best = 0;
        foreach ([$title, $original] as $candidate) {
            $c = $this->normalize($candidate);
            if ($c === '') {
                continue;
            }
            if ($c === $q) {
                $score = 100;
            } elseif (str_starts_with($c, $q)) {
                $score = 70;
            } elseif (preg_match('/\b' . preg_quote($q, '/') . '\b/u', $c)) {
                $score = 55;
            } elseif (str_contains($c, $q)) {
                $score = 40;
            } else {
                similar_text($q, $c, $percent);
                $score = $percent * 0.35;
            }
            $best = max($best, $score);
        }

        return $best + min(log10(1 + $popularity) * 4, 12) + min(log10(1 + $votes) * 2, 8);
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($ascii !== false) {
            $text = $ascii;
        }
        $text = preg_replace('/[^a-z0-9]+/', ' ', $text);

        return trim(preg_replace('/^(the|le|la|les|l) /', '', trim($text)));
    }
}
