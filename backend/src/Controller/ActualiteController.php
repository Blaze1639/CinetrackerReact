<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/actualites', format: 'json')]
class ActualiteController extends AbstractController
{
    #[Route('', methods: ['POST'])]
    public function add(Request $req, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getRole() !== 'admin') {
            return $this->json(['success' => false, 'error' => 'Non autorisé'], 403);
        }

        $data    = json_decode($req->getContent(), true) ?? [];
        $titre   = trim($data['titre'] ?? '');
        $contenu = trim($data['contenu'] ?? '');

        if (!$titre || !$contenu) {
            return $this->json(['success' => false, 'error' => 'Champs manquants'], 400);
        }

        $actu = new Actualite();
        $actu->setTitre($titre)->setContenu($contenu)->setUserId($user->getId());

        $em->persist($actu);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Actualité publiée']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getRole() !== 'admin') {
            return $this->json(['success' => false, 'error' => 'Non autorisé'], 403);
        }

        $actu = $em->getRepository(Actualite::class)->find($id);
        if ($actu) {
            $em->remove($actu);
            $em->flush();
        }

        return $this->json(['success' => true, 'message' => 'Actualité supprimée']);
    }
}
