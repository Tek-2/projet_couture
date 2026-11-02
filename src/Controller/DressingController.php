<?php

namespace App\Controller;

use App\Entity\CommandeLocation;
use App\Repository\ModeleLocationRepository;
use App\Repository\StockLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\LigneCommandeLocation;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dressing')]
final class DressingController extends AbstractController
{
    // =========================
    // LISTE DES MODÈLES
    // =========================
    #[Route('', name: 'app_dressing_index', methods: ['GET'])]
    public function index(ModeleLocationRepository $modeleLocationRepository): Response
    {
        return $this->render('dressing/index.html.twig', [
            'modele_locations' => $modeleLocationRepository->findAll(),
        ]);
    }

    // =========================
    // FICHE MODÈLE
    // =========================
    #[Route('/{id}', name: 'app_dressing_show', methods: ['GET'])]
    public function show(
        ModeleLocationRepository $modeleLocationRepository,
        int $id
    ): Response {

        $modeleLocation = $modeleLocationRepository->find($id);

        if (!$modeleLocation) {
            throw $this->createNotFoundException('ModeleLocation not found');
        }

        $stocks = $modeleLocation->getStockLocations();

        // PHOTOS
        $photos = $modeleLocation->getStockLocationImages()->toArray();

        usort($photos, fn($a, $b) =>
            ($a->getOrdre() ?? PHP_INT_MAX) <=> ($b->getOrdre() ?? PHP_INT_MAX)
        );

        // COULEURS
        $couleurs = [];
        foreach ($stocks as $stock) {
            $couleur = $stock->getCouleur();
            if ($couleur) {
                $couleurs[$couleur->getId()] = $couleur;
            }
        }
        $couleurs = array_values($couleurs);

        // TAILLES
        $tailles = [];
        foreach ($stocks as $stock) {
            if ($stock->getTaille()) {
                $tailles[$stock->getTaille()] = $stock->getTaille();
            }
        }
        $tailles = array_values($tailles);

        // STOCK JS
        $stocksArray = [];
        foreach ($stocks as $stock) {
            $stocksArray[] = [
                'id' => $stock->getId(),
                'taille' => $stock->getTaille(),
                'quantite' => $stock->getQuantite(),
                'couleur' => [
                    'id' => $stock->getCouleur()?->getId(),
                    'nom' => $stock->getCouleur()?->getNomCouleur(),
                ]
            ];
        }

        return $this->render('dressing/show.html.twig', [
            'modele_location' => $modeleLocation,
            'photos' => $photos,
            'main_photo' => $photos[0] ?? null,
            'couleurs' => $couleurs,
            'tailles' => $tailles,
            'stocks' => $stocks,
            'stocks_js' => $stocksArray,
        ]);
    }

    // =========================
    // CRÉATION RÉSERVATION
    // =========================
  #[Route('/reservation/create', name: 'app_reservation_create', methods: ['GET','POST'])]
  #[IsGranted('ROLE_USER')]
  public function createReservation(
    Request $request,
    EntityManagerInterface $em,
    SessionInterface $session,
    StockLocationRepository $stockLocationRepository
): Response
{

    if ($request->isMethod('POST')) {


        $reservation = new CommandeLocation();

        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }
        $reservation->setUser($user);

        $reservation->setStatut('en_attente');


        // ==========================
        // CRENEAUX CLIENT
        // ==========================

        $reservation->setCreneauxClient(
            $request->request->all('creneaux_client')
        );


        $reservation->setCommentaire(
            $request->request->get('commentaire')
        );



        $em->persist($reservation);



        // ==========================
        // MODELES DU SHOWROOM
        // ==========================

        $showroom = $session->get('showroom', []);



        foreach ($showroom as $stockId) {


            $stock = $stockLocationRepository->find($stockId);


            if (!$stock) {
                continue;
            }



            $ligne = new LigneCommandeLocation();


            $ligne->setCommandeloc($reservation);

            $ligne->setStockloc($stock);


            // prix actuel du modèle
            $ligne->setPrix(
                $stock->getModeleLocation()->getPrixLocation()
            );


            // quantité essayée
            $ligne->setQuantite(1);



            $em->persist($ligne);

        }



        $em->flush();



        // ==========================
        // NETTOYAGE SHOWROOM
        // ==========================

        $session->remove('showroom');



        return $this->redirectToRoute(
            'app_reservation_success'
        );

    }



    return $this->render(
        'reservation/create.html.twig'
    );
}

    // =========================
    // SUCCESS PAGE
    // =========================
    #[Route('/reservation/success', name: 'app_reservation_success')]
    public function success(): Response
    {
        return $this->render('reservation/success.html.twig');
    }
}