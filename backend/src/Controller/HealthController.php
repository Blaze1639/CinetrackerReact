<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

// Route publique très légère : le frontend l'appelle au chargement pour réveiller Render.
#[Route('/api/health', format: 'json')]
class HealthController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function health(): JsonResponse
    {
        return $this->json(['success' => true]);
    }
}
