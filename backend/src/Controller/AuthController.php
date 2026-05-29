<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/auth', format: 'json')]
class AuthController extends AbstractController
{
    #[Route('/login', methods: ['POST'])]
    public function login(
        Request $request,
        UserRepository $userRepo,
        UserPasswordHasherInterface $hasher,
        JWTTokenManagerInterface $jwtManager,
    ): JsonResponse {
        $data       = json_decode($request->getContent(), true) ?? [];
        $email      = trim($data['email'] ?? '');
        $motdepasse = $data['motdepasse'] ?? '';

        if (!$email || !$motdepasse) {
            return $this->json(['success' => false, 'error' => 'Champs manquants'], 400);
        }

        $user = $userRepo->findOneBy(['email' => $email]);

        if (!$user || !$hasher->isPasswordValid($user, $motdepasse)) {
            return $this->json(['success' => false, 'error' => 'Email ou mot de passe incorrect'], 401);
        }

        $token = $jwtManager->create($user);

        return $this->json([
            'success'  => true,
            'token'    => $token,
            'user_id'  => $user->getId(),
            'username' => $user->getUsername(),
            'role'     => $user->getRole(),
        ]);
    }

    #[Route('/me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['success' => true, 'user_id' => null]);
        }
        return $this->json([
            'success'  => true,
            'user_id'  => $user->getId(),
            'username' => $user->getUsername(),
            'role'     => $user->getRole(),
        ]);
    }

    #[Route('/register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em,
        UserRepository $userRepo,
        JWTTokenManagerInterface $jwtManager,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];

        $pseudo     = trim($data['pseudo'] ?? '');
        $email      = trim($data['email'] ?? '');
        $motdepasse = $data['motdepasse'] ?? '';
        $confirmer  = $data['confirmer_motdepasse'] ?? '';

        if (!$pseudo || !$email || !$motdepasse) {
            return $this->json(['success' => false, 'error' => 'Tous les champs sont obligatoires'], 400);
        }
        if ($motdepasse !== $confirmer) {
            return $this->json(['success' => false, 'error' => 'Les mots de passe ne correspondent pas'], 400);
        }
        if (strlen($motdepasse) < 6) {
            return $this->json(['success' => false, 'error' => 'Le mot de passe doit contenir au moins 6 caractères'], 400);
        }

        $existing = $em->createQuery('SELECT u FROM App\Entity\User u WHERE u.email = :email OR u.username = :pseudo')
            ->setParameter('email', $email)
            ->setParameter('pseudo', $pseudo)
            ->getOneOrNullResult();

        if ($existing) {
            return $this->json(['success' => false, 'error' => 'Ce pseudo ou cet email existe déjà'], 400);
        }

        $user = new User();
        $user->setUsername($pseudo);
        $user->setEmail($email);
        $user->setPassword($hasher->hashPassword($user, $motdepasse));
        $user->setRole('utilisateur');

        $em->persist($user);
        $em->flush();

        $token = $jwtManager->create($user);

        return $this->json([
            'success'  => true,
            'token'    => $token,
            'user_id'  => $user->getId(),
            'username' => $user->getUsername(),
            'role'     => $user->getRole(),
        ]);
    }

    #[Route('/logout', methods: ['GET', 'POST'])]
    public function logout(): JsonResponse
    {
        return $this->json(['success' => true, 'message' => 'Déconnecté']);
    }
}
