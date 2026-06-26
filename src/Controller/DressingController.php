<?php

namespace App\Controller;

use App\Repository\ModeleLocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\CommandeLocation;
use App\Entity\LigneCommandeLocation;
use App\Repository\StockLocationRepository;


#[Route('/dressing')]
final class DressingController extends AbstractController
{
    #[Route('', name: 'app_dressing_index', methods: ['GET'])]
    public function index(ModeleLocationRepository $modeleLocationRepository): Response
    {
        return $this->render('dressing/index.html.twig', [
            'modele_locations' => $modeleLocationRepository->findAll(),
        ]);
    }

#[Route('/{id}', name: 'app_dressing_show', methods: ['GET'])]
public function show(ModeleLocationRepository $modeleLocationRepository, int $id): Response
{
    $modeleLocation = $modeleLocationRepository->find($id);

    if (!$modeleLocation) {
        throw $this->createNotFoundException('ModeleLocation not found');
    }

    $stocks = $modeleLocation->getStockLocations();

    // ------------------------
    // PHOTOS (ModeleLocation direct)
    // ------------------------
    $photos = $modeleLocation->getStockLocationImages()->toArray();

    usort($photos, fn($a, $b) =>
        ($a->getOrdre() ?? PHP_INT_MAX) <=> ($b->getOrdre() ?? PHP_INT_MAX)
    );

    // ------------------------
    // COULEURS (via StockLocation)
    // ------------------------
    $couleurs = [];

    foreach ($stocks as $stock) {
        $couleur = $stock->getCouleur();

        if ($couleur) {
            $couleurs[$couleur->getId()] = $couleur;
        }
    }

    $couleurs = array_values($couleurs);

    // ------------------------
    // TAILLES
    // ------------------------
    $tailles = [];

    foreach ($stocks as $stock) {
        if ($stock->getTaille()) {
            $tailles[$stock->getTaille()] = $stock->getTaille();
        }
    }

    $tailles = array_values($tailles);

    return $this->render('dressing/show.html.twig', [
        'modele_location' => $modeleLocation,
        'photos' => $photos,
        'couleurs' => $couleurs,
        'tailles' => $tailles,
        'main_photo' => $photos[0] ?? null,
    ]);
}

#[Route('/reservation/create', name: 'app_reservation_create', methods: ['GET', 'POST'])]
public function createReservation(
    Request $request,
    EntityManagerInterface $em,
    StockLocationRepository $stockRepo
): Response {

    // formulaire soumis
    if ($request->isMethod('POST')) {

        $reservation = new CommandeLocation();

        // créneau choisi
        $dateDebut = $request->request->get('date_debut');
        $dateFin = $request->request->get('date_fin');

        $reservation->setDateDebutLoc(new \DateTimeImmutable($dateDebut));

        if ($dateFin) {
            $reservation->setDateFinLoc(new \DateTimeImmutable($dateFin));
        }

        // utilisateur connecté
        $reservation->setUser($this->getUser());

        // statut initial
        $reservation->setStatut('en_attente');

        /**
         * =========================
         * MODE SÉLECTION MODÈLES
         * =========================
         * On peut envoyer une liste de stock IDs
         */
        $stockIds = $request->request->all('stocks');

        if (!empty($stockIds)) {
            foreach ($stockIds as $stockId) {

                $stock = $stockRepo->find($stockId);

                if ($stock) {
                    $ligne = new LigneCommandeLocation();
                    $ligne->setStockloc($stock);
                    $ligne->setQuantite(1);    
            
                }
            }
        }

        $em->persist($reservation);
        $em->flush();

        return $this->redirectToRoute('app_reservation_success');
    }

    // affichage formulaire
    $stocks = $stockRepo->findAll();

    return $this->render('reservation/create.html.twig', [
        'stocks' => $stocks
    ]);
}

#[Route('/reservation/success', name: 'app_reservation_success')]
public function success(): Response
{
    return $this->render('reservation/success.html.twig');
}

}

