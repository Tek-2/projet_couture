<?php

namespace App\Controller\Api;

use App\Repository\StockLocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/showroom')]
class ShowroomController extends AbstractController
{
    // =========================
    // AFFICHER LE SHOWROOM
    // =========================
    #[Route('', name: 'api_showroom_index', methods: ['GET'])]
    public function index(
        SessionInterface $session,
        StockLocationRepository $stockRepository
    ): JsonResponse {

        $ids = $session->get('showroom', []);

        $stocks = [];

        if (!empty($ids)) {
            $stocks = $stockRepository->findBy(['id' => $ids]);
        }

        return $this->json(
            $stocks,
            200,
            [],
            ['groups' => ['stock:read']]
        );
    }

    // =========================
    // AJOUTER AU SHOWROOM
    // =========================
    #[Route('/add/{id}', name: 'api_showroom_add', methods: ['POST'])]
    public function add(
        int $id,
        SessionInterface $session
    ): JsonResponse {

        $showroom = $session->get('showroom', []);

        if (!in_array($id, $showroom)) {
            $showroom[] = $id;
        }

        $session->set('showroom', $showroom);

        return $this->json([
            'success' => true,
            'message' => 'Modèle ajouté au showroom.',
            'showroom' => $showroom
        ]);
    }

    // =========================
    // SUPPRIMER DU SHOWROOM
    // =========================
    #[Route('/remove/{id}', name: 'api_showroom_remove', methods: ['DELETE'])]
    public function remove(
        int $id,
        SessionInterface $session
    ): JsonResponse {

        $showroom = $session->get('showroom', []);

        $showroom = array_values(array_filter(
            $showroom,
            fn ($item) => $item != $id
        ));

        $session->set('showroom', $showroom);

        return $this->json([
            'success' => true,
            'message' => 'Modèle supprimé du showroom.',
            'showroom' => $showroom
        ]);
    }

    // =========================
    // VIDER LE SHOWROOM
    // =========================
    #[Route('/clear', name: 'api_showroom_clear', methods: ['DELETE'])]
    public function clear(
        SessionInterface $session
    ): JsonResponse {

        $session->remove('showroom');

        return $this->json([
            'success' => true,
            'message' => 'Showroom vidé.'
        ]);
    }
}