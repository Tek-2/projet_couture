<?php

namespace App\Controller;

use App\Entity\LigneCommandeLocation;
use App\Form\LigneCommandeLocationType;
use App\Repository\LigneCommandeLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ligne_commande_location')]
final class LigneCommandeLocationController extends AbstractController
{
    #[Route(name: 'app_ligne_commande_location_index', methods: ['GET'])]
    public function index(LigneCommandeLocationRepository $ligneCommandeLocationRepository): Response
    {
        return $this->render('ligne_commande_location/index.html.twig', [
            'ligne_commande_locations' => $ligneCommandeLocationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ligne_commande_location_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ligneCommandeLocation = new LigneCommandeLocation();
        $form = $this->createForm(LigneCommandeLocationType::class, $ligneCommandeLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ligneCommandeLocation);
            $entityManager->flush();

            return $this->redirectToRoute('app_ligne_commande_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ligne_commande_location/new.html.twig', [
            'ligne_commande_location' => $ligneCommandeLocation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ligne_commande_location_show', methods: ['GET'])]
    public function show(LigneCommandeLocation $ligneCommandeLocation): Response
    {
        return $this->render('ligne_commande_location/show.html.twig', [
            'ligne_commande_location' => $ligneCommandeLocation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ligne_commande_location_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LigneCommandeLocation $ligneCommandeLocation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LigneCommandeLocationType::class, $ligneCommandeLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ligne_commande_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ligne_commande_location/edit.html.twig', [
            'ligne_commande_location' => $ligneCommandeLocation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ligne_commande_location_delete', methods: ['POST'])]
    public function delete(Request $request, LigneCommandeLocation $ligneCommandeLocation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ligneCommandeLocation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ligneCommandeLocation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ligne_commande_location_index', [], Response::HTTP_SEE_OTHER);
    }
}
