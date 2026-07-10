<?php

namespace App\Controller;

use App\Repository\StockLocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/showroom')]
class ShowroomController extends AbstractController
{
    // =========================
    // AFFICHER MON SHOWROOM
    // =========================
    #[Route('', name: 'app_showroom')]
    public function index(
        SessionInterface $session,
        StockLocationRepository $stockRepo
    ): Response {

        // récupération des IDs stockés en session
        $ids = $session->get('showroom', []);

        // récupération des objets Doctrine
        $stocks = [];

        if (!empty($ids)) {
            $stocks = $stockRepo->findBy(['id' => $ids]);
        }

        return $this->render('showroom/index.html.twig', [
            'stocks' => $stocks
        ]);
    }

    // =========================
    // AJOUTER UN MODELE
    // =========================
    #[Route('/add/{id}', name: 'app_showroom_add')]
    public function add(
        int $id,
        SessionInterface $session
    ): Response {

        $showroom = $session->get('showroom', []);

        // évite doublon
        if (!in_array($id, $showroom)) {
            $showroom[] = $id;
        }

        $session->set('showroom', $showroom);

        return $this->redirectToRoute('app_showroom');
    }

    // =========================
    // RETIRER UN MODELE
    // =========================
    #[Route('/remove/{id}', name: 'app_showroom_remove')]
    public function remove(
        int $id,
        SessionInterface $session
    ): Response {

        $showroom = $session->get('showroom', []);

        // suppression propre
        $showroom = array_filter(
            $showroom,
            fn($item) => $item != $id
        );

        $session->set('showroom', $showroom);

        return $this->redirectToRoute('app_showroom');
    }

    // =========================
    // VIDER SHOWROOM
    // =========================
    #[Route('/clear', name: 'app_showroom_clear')]
    public function clear(SessionInterface $session): Response
    {
        $session->remove('showroom');

        return $this->redirectToRoute('app_showroom');
    }
}