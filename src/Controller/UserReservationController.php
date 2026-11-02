<?php

namespace App\Controller;

use App\Repository\CommandeLocationRepository;
use App\Repository\LigneCommandeLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/mes-reservations')]
#[IsGranted('ROLE_USER')]
class UserReservationController extends AbstractController
{

    // =====================================
    // LISTE DES RESERVATIONS CLIENT
    // =====================================

    #[Route('', name: 'user_reservations', methods: ['GET'])]
    public function index(
        CommandeLocationRepository $repository
    ): JsonResponse {

        $reservations = $repository->findBy(
            [
                'user' => $this->getUser()
            ],
            [
                'date_commande' => 'DESC'
            ]
        );

        $result = [];

        foreach ($reservations as $reservation) {

            $result[] = [

                'id' => $reservation->getId(),

                'statut' =>
                    $reservation->getStatut(),

                'dateCommande' =>
                    $reservation->getDateCommande()
                        ?->format('Y-m-d H:i:s'),

                'commentaire' =>
                    $reservation->getCommentaire(),

                'dateDebutValidee' =>
                    $reservation->getDateDebutValidee()
                        ?->format('Y-m-d H:i:s'),

                'dateFinValidee' =>
                    $reservation->getDateFinValidee()
                        ?->format('Y-m-d H:i:s'),

            ];

        }

        return $this->json([

            'success' => true,

            'reservations' => $result

        ]);

    }



    // =====================================
    // DETAIL RESERVATION
    // =====================================

    #[Route('/{id}', name: 'user_reservation_show', methods: ['GET'])]
    public function show(
        int $id,
        CommandeLocationRepository $repository
    ): JsonResponse {

        $reservation =
            $repository->find($id);

        if (!$reservation) {

            return $this->json([
                'success' => false,
                'message' => 'Réservation introuvable'
            ], 404);

        }

        // Sécurité :
        // une cliente ne peut consulter que ses propres réservations

        if ($reservation->getUser() !== $this->getUser()) {

            return $this->json([
                'success' => false,
                'message' => 'Accès refusé.'
            ], 403);

        }

        return $this->json([

            'id' => $reservation->getId(),

            'statut' => $reservation->getStatut(),

            'commentaire' => $reservation->getCommentaire(),

            'dateDebutValidee' =>
                $reservation->getDateDebutValidee()
                    ?->format('Y-m-d H:i:s'),

            'dateFinValidee' =>
                $reservation->getDateFinValidee()
                    ?->format('Y-m-d H:i:s')

        ]);

    }

}