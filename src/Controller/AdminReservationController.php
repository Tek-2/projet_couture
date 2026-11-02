<?php

namespace App\Controller;

use App\Entity\CommandeLocation;
use App\Repository\CommandeLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reservation/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminReservationController extends AbstractController
{
    #[Route('', name: 'admin_reservations')]
    public function index(CommandeLocationRepository $repo): Response
    {
        return $this->render('reservation/admin/index.html.twig', [
            'reservations' => $repo->findBy([], ['date_commande' => 'DESC'])
        ]);
    }

    #[Route('/{id}', name: 'admin_reservation_show')]
    public function show(CommandeLocation $reservation): Response
    {
        return $this->render('reservation/admin/show.html.twig', [
            'reservation' => $reservation
        ]);
    }

    // ADMIN VALIDE 1 CRÉNEAU
    #[Route('/{id}/valider-client', name: 'admin_reservation_valider_client', methods: ['POST'])]
public function validerClient(
    Request $request,
    CommandeLocation $reservation,
    EntityManagerInterface $em
): Response {

    $choix = $request->request->get('creneau');

    if (!$choix) {
        $this->addFlash('danger', 'Veuillez choisir un créneau.');

        return $this->redirectToRoute(
            'admin_reservation_show',
            ['id' => $reservation->getId()]
        );
    }


    [$date, $heure] = explode('|', $choix);


    $debut = new \DateTimeImmutable(
        $date.' '.$heure
    );

    $fin = $debut->modify('+30 minutes');


    $reservation->setDateDebutValidee($debut);
    $reservation->setDateFinValidee($fin);

    $reservation->setStatut('valide');


    $em->flush();


    return $this->redirectToRoute(
        'admin_reservation_show',
        ['id' => $reservation->getId()]
    );
}

    // ADMIN PROPOSE 1 CRÉNEAU
    #[Route('/{id}/proposer', name: 'admin_reservation_proposer', methods: ['POST'])]
    public function proposer(
        Request $request,
        CommandeLocation $reservation,
        EntityManagerInterface $em
    ): Response {

        $day = $request->request->get('date');
        $time = $request->request->get('time');

        $start = new \DateTimeImmutable($day . ' ' . $time);

        $reservation->setPropositionDebut($start);
        $reservation->setPropositionFin($start->modify('+30 minutes'));

        $reservation->setStatut('proposition');

        $em->flush();

        return $this->redirectToRoute('admin_reservation_show', [
            'id' => $reservation->getId()
        ]);
    }

    // VALIDATION FINALE ADMIN (optionnel si tu veux bypass client)
    #[Route('/{id}/valider', name: 'admin_reservation_valider', methods: ['POST'])]
    public function valider(
        CommandeLocation $reservation,
        EntityManagerInterface $em
    ): Response {

        $reservation->setDateDebutValidee($reservation->getPropositionDebut());
        $reservation->setDateFinValidee($reservation->getPropositionFin());

        $reservation->setStatut('valide');

        $em->flush();

        return $this->redirectToRoute('admin_reservation_show', [
            'id' => $reservation->getId()
        ]);
    }

    // REFUS CLIENT FINAL = ANNULATION
    #[Route('/{id}/refuser', name: 'admin_reservation_refuser', methods: ['POST'])]
    public function refuser(
        CommandeLocation $reservation,
        EntityManagerInterface $em
    ): Response {

        $reservation->setStatut('annule');

        $reservation->setPropositionDebut(null);
        $reservation->setPropositionFin(null);

        $em->flush();

        return $this->redirectToRoute('admin_reservation_show', [
            'id' => $reservation->getId()
        ]);
    }
}