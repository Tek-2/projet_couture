<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            $data = $request->request->all();
        }

        if (
            empty($data['email']) ||
            empty($data['password'])
        ) {
            return $this->json([
                'success' => false,
                'message' => 'Email et mot de passe obligatoires.'
            ], 400);
        }

        // Vérifie si l'utilisateur existe déjà
        $existingUser = $entityManager
            ->getRepository(User::class)
            ->findOneBy([
                'email' => $data['email']
            ]);

        if ($existingUser) {
            return $this->json([
                'success' => false,
                'message' => 'Cette adresse email est déjà utilisée.'
            ], 409);
        }

        $user = new User();

        // Adapter ces setters à ton entité User
        $user->setEmail($data['email']);

        if (!empty($data['nom'])) {
            $user->setNom($data['nom']);
        }

        if (!empty($data['prenom'])) {
            $user->setPrenom($data['prenom']);
        }

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $data['password']
            )
        );

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Compte créé avec succès.',
            'user_id' => $user->getId()
        ], 201);
    }
}
