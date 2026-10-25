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
        if (mb_strlen($query) < 2) {
            return $this->json(['success' => true, 'results' => []]);
        }

        // Les résultats TMDB changent peu : une recherche déjà faite répond sans appel externe.
        $results = $cache->get('tmdb_search_' . md5(mb_strtolower($query)), function (ItemInterface $item) use ($query) {
            $item->expiresAfter(86400);
            $results = $this->fetchResults($query);
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
    private function fetchResults(string $query): ?array
    {
        $apiKey = $_ENV['TMDB_API_KEY'] ?? $_SERVER['TMDB_API_KEY'] ?? self::DEFAULT_API_KEY;
        $url = 'https://api.themoviedb.org/3/search/multi?' . http_build_query([
            'api_key'       => $apiKey,
            'language'      => 'fr-FR',
            'query'         => $query,
            'include_adult' => 'false',
        ]);

        $context = stream_context_create(['http' => ['timeout' => 8, 'ignore_errors' => true]]);
        $body = @file_get_contents($url, false, $context);
        $data = $body !== false ? json_decode($body, true) : null;

        if (!is_array($data) || !is_array($data['results'] ?? null)) {
            return null;
        }

        $results = [];
        foreach ($data['results'] as $item) {
            $type = $item['media_type'] ?? '';
            if ($type !== 'movie' && $type !== 'tv') {
                continue;
            }

            $date = $type === 'movie' ? ($item['release_date'] ?? '') : ($item['first_air_date'] ?? '');
            $results[] = [
                'id'          => $item['id'] ?? null,
                'media_type'  => $type,
                'title'       => $item['title'] ?? $item['name'] ?? '',
                'year'        => $date ? substr($date, 0, 4) : null,
                'poster_path' => $item['poster_path'] ?? null,
                'overview'    => $item['overview'] ?? '',
            ];
        }

        return $results;
    }
}
