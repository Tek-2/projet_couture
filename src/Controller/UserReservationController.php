<?php

namespace App\Controller;

use App\Entity\CommandeLocation;
use App\Repository\CommandeLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\LigneCommandeLocationRepository;

#[Route('/mes-reservations')]
class UserReservationController extends AbstractController
{

    // =========================
    // LISTE DES RESERVATIONS CLIENT
    // =========================

    #[Route('', name: 'app_user_reservations', methods: ['GET'])]
    public function index(
        CommandeLocationRepository $repository
    ): Response {

        $reservations = $repository->findBy(
            [
                'user' => $this->getUser()
            ],
            [
                'date_commande' => 'DESC'
            ]
        );


        return $this->render(
            'reservation/user/index.html.twig',
            [
                'reservations' => $reservations
            ]
        );
    }



    // =========================
    // DETAIL RESERVATION
    // =========================

    #[Route('/{id}', name: 'app_user_reservation_show', methods: ['GET'])]
    public function show(
    CommandeLocation $reservation,
    LigneCommandeLocationRepository $ligneRepository
): Response {


        // Sécurité :
        // un client ne peut voir que ses propres réservations

        if ($reservation->getUser() !== $this->getUser()) {

            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas consulter cette réservation.'
            );
        }

        $lignes = $ligneRepository->findBy([
            'commandeloc' => $reservation
        ]);

        return $this->render(
            'reservation/user/show.html.twig',
            [
                'reservation' => $reservation,
                'lignes' => $lignes
            ]
        );
    }



    // =========================
    // CLIENT ACCEPTE LA PROPOSITION ADMIN
    // =========================

    #[Route('/{id}/accepter', name: 'reservation_accepter', methods: ['POST'])]
    public function accepter(
        CommandeLocation $reservation,
        EntityManagerInterface $em
    ): Response {


        if ($reservation->getUser() !== $this->getUser()) {

            throw $this->createAccessDeniedException();
        }


        // Vérification qu'une proposition existe

        if (!$reservation->getPropositionDebut()) {

            $this->addFlash(
                'danger',
                'Aucun créneau proposé.'
            );

            return $this->redirectToRoute(
                'app_user_reservation_show',
                [
                    'id' => $reservation->getId()
                ]
            );
        }



        // Validation finale

        $reservation->setDateDebutValidee(
            $reservation->getPropositionDebut()
        );

        $reservation->setDateFinValidee(
            $reservation->getPropositionFin()
        );


        $reservation->setStatut('valide');



        // On nettoie la proposition temporaire

        $reservation->setPropositionDebut(null);
        $reservation->setPropositionFin(null);



        $em->flush();



        $this->addFlash(
            'success',
            'Votre rendez-vous a été validé.'
        );


        return $this->redirectToRoute(
            'app_user_reservation_show',
            [
                'id' => $reservation->getId()
            ]
        );
    }



    // =========================
    // CLIENT REFUSE LA PROPOSITION ADMIN
    // =========================

    #[Route('/{id}/refuser', name: 'reservation_refuser', methods: ['POST'])]
    public function refuser(
        CommandeLocation $reservation,
        EntityManagerInterface $em
    ): Response {


        if ($reservation->getUser() !== $this->getUser()) {

            throw $this->createAccessDeniedException();
        }



        $reservation->setStatut('refuse_client');



        // Suppression de la proposition admin

        $reservation->setPropositionDebut(null);
        $reservation->setPropositionFin(null);



        $em->flush();



        $this->addFlash(
            'info',
            'Le créneau proposé a été refusé.'
        );



        return $this->redirectToRoute(
            'app_user_reservation_show',
            [
                'id' => $reservation->getId()
            ]
        );
    }

}