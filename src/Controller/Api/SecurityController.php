<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api')]
class SecurityController extends AbstractController
{
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    #[OA\Post(
        path: '/api/login',
        summary: 'Connexion utilisateur',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        example: 'test@gmail.com'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        example: 'password'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Connexion réussie'
            ),
            new OA\Response(
                response: 401,
                description: 'Identifiants incorrects'
            )
        ]
    )]
    public function login(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse{

        $data = json_decode(
            $request->getContent(),
            true
        );


        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;



        if (!$email || !$password) {

            return $this->json([
                'success' => false,
                'message' => 'Email et mot de passe obligatoires.'
            ], 400);

        }



        $user = $userRepository->findOneBy([
            'email' => $email
        ]);



        if (!$user) {

            return $this->json([
                'success' => false,
                'message' => 'Utilisateur introuvable.'
            ], 401);

        }



        if (!$passwordHasher->isPasswordValid(
            $user,
            $password
        )) {

            return $this->json([
                'success' => false,
                'message' => 'Mot de passe incorrect.'
            ], 401);

        }



        return $this->json([

            'success' => true,

            'message' => 'Connexion réussie.',

            'user' => [

                'id' => $user->getId(),

                'email' => $user->getEmail(),

                'nom' => $user->getNom(),

                'prenom' => $user->getPrenom(),

                'tel' => $user->getTel(),

                'adresse' => $user->getAdresse()

            ]

        ]);

    }


    #[Route('/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'message' => 'Déconnexion réussie.'
        ]);
    }
}