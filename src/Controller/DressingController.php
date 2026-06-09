<?php

namespace App\Controller;

use App\Repository\ModeleLocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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

        return $this->render('dressing/show.html.twig', [
            'modele_location' => $modeleLocation,
        ]);
    }
}
