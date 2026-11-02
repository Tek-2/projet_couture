<?php

namespace App\Controller\Api;

use App\Entity\CommandeLocation;
use App\Entity\LigneCommandeLocation;
use App\Repository\ModeleLocationRepository;
use App\Repository\StockLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/dressing')]
final class DressingController extends AbstractController
{
    // =========================
    // LISTE DES MODÈLES
    // =========================
    #[Route('', name: 'api_dressing_index', methods: ['GET'])]
    public function index(
        ModeleLocationRepository $modeleLocationRepository
    ): JsonResponse {

        return $this->json(
            $modeleLocationRepository->findAll(),
            200,
            [],
            ['groups' => ['modele:read']]
        );
    }

    // =========================
    // FICHE MODÈLE
    // =========================
    #[Route('/{id}', name: 'api_dressing_show', methods: ['GET'])]
    public function show(
        ModeleLocationRepository $modeleLocationRepository,
        int $id
    ): JsonResponse {

        $modeleLocation = $modeleLocationRepository->find($id);

        if (!$modeleLocation) {
            return $this->json([
                'message' => 'Modèle introuvable.'
            ], 404);
        }

        $stocks = $modeleLocation->getStockLocations();

        // Photos
        $photos = $modeleLocation->getStockLocationImages()->toArray();

        usort($photos, fn($a, $b) =>
            ($a->getOrdre() ?? PHP_INT_MAX)
            <=>
            ($b->getOrdre() ?? PHP_INT_MAX)
        );

        // Couleurs
        $couleurs = [];

        foreach ($stocks as $stock) {
            $couleur = $stock->getCouleur();

            if ($couleur) {
                $couleurs[$couleur->getId()] = [
                    'id' => $couleur->getId(),
                    'nom' => $couleur->getNomCouleur()
                ];
            }
        }

        // Tailles
        $tailles = [];

        foreach ($stocks as $stock) {
            if ($stock->getTaille()) {
                $tailles[$stock->getTaille()] = $stock->getTaille();
            }
        }

        // Stocks
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

        return $this->json([
            'modele' => $modeleLocation,
            'photos' => $photos,
            'main_photo' => $photos[0] ?? null,
            'couleurs' => array_values($couleurs),
            'tailles' => array_values($tailles),
            'stocks' => $stocksArray
        ], 200, [], [
            'groups' => [
                'modele:read',
                'stock:read',
                'image:read'
            ]
        ]);
    }

    // =========================
    // CREATION D'UNE RESERVATION
    // =========================
    #[Route('/reservation/create', name: 'api_reservation_create', methods: ['POST'])]
    public function createReservation(
        Request $request,
        EntityManagerInterface $em,
        SessionInterface $session,
        StockLocationRepository $stockLocationRepository
    ): JsonResponse {

        $reservation = new CommandeLocation();

        $reservation->setUser($this->getUser());
        $reservation->setStatut('en_attente');

        $reservation->setCreneauxClient(
            $request->request->all('creneaux_client')
        );

        $reservation->setCommentaire(
            $request->request->get('commentaire')
        );

        $em->persist($reservation);

        $showroom = $session->get('showroom', []);

        foreach ($showroom as $stockId) {

            $stock = $stockLocationRepository->find($stockId);

            if (!$stock) {
                continue;
            }

            $ligne = new LigneCommandeLocation();

            $ligne->setCommandeloc($reservation);
            $ligne->setStockloc($stock);

            $ligne->setPrix(
                $stock->getModeleLocation()->getPrixLocation()
            );

            $ligne->setQuantite(1);

            $em->persist($ligne);
        }

        $em->flush();

        $session->remove('showroom');

        return $this->json([
            'success' => true,
            'message' => 'Réservation créée avec succès.',
            'reservation_id' => $reservation->getId()
        ], 201);
    }

    // =========================
    // SUCCESS
    // =========================
    #[Route('/reservation/success', name: 'api_reservation_success', methods: ['GET'])]
    public function success(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'message' => 'Votre réservation a bien été enregistrée.'
        ]);
    }
}