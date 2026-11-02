<?php

namespace App\Controller\Api;

use App\Repository\CommandeLocationRepository;
use App\Repository\LigneCommandeLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/mes-reservations')]
class UserReservationController extends AbstractController
{

    // =====================================
    // LISTE DES RESERVATIONS CLIENT
    // =====================================

    #[Route('', name: 'api_user_reservations', methods: ['GET'])]
    public function index(
        Request $request,
        CommandeLocationRepository $repository
    ): JsonResponse {


        $userId = $request->query->get('userId');


        if (!$userId) {

            return $this->json([
                'success' => false,
                'message' => 'Utilisateur manquant.'
            ],400);

        }



        $reservations = $repository->findBy(
            [
                'user' => $userId
            ],
            [
                'date_commande' => 'DESC'
            ]
        );



        $result = [];


        foreach($reservations as $reservation){


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

            'success'=>true,

            'reservations'=>$result

        ]);

    }



    // =====================================
    // DETAIL RESERVATION
    // =====================================

    #[Route('/{id}', name:'api_user_reservation_show', methods:['GET'])]
    public function show(
        int $id,
        CommandeLocationRepository $repository
    ): JsonResponse {


        $reservation =
            $repository->find($id);



        if(!$reservation){

            return $this->json([
                'success'=>false,
                'message'=>'Réservation introuvable'
            ],404);

        }



        return $this->json([

            'id'=>$reservation->getId(),

            'statut'=>$reservation->getStatut(),

            'commentaire'=>$reservation->getCommentaire(),

            'dateDebutValidee'=>
                $reservation->getDateDebutValidee()
                    ?->format('Y-m-d H:i:s'),

            'dateFinValidee'=>
                $reservation->getDateFinValidee()
                    ?->format('Y-m-d H:i:s')

        ]);

    }

}