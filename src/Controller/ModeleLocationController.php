<?php
// a faire : supprimer la gestion de prixAchat 

namespace App\Controller;

use App\Entity\ModeleLocation;
use App\Form\ModeleLocationType;
use App\Repository\ModeleLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/modele_location')]
final class ModeleLocationController extends AbstractController
{
    #[Route(name: 'app_modele_location_index', methods: ['GET'])]
    public function index(ModeleLocationRepository $modeleLocationRepository): Response
    {
        return $this->render('modele_location/index.html.twig', [
            'modele_locations' => $modeleLocationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_modele_location_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $modeleLocation = new ModeleLocation();
        $form = $this->createForm(ModeleLocationType::class, $modeleLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($modeleLocation);
            $entityManager->flush();

            return $this->redirectToRoute('app_modele_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('modele_location/new.html.twig', [
            'modele_location' => $modeleLocation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_modele_location_show', methods: ['GET'])]
    public function show(ModeleLocation $modeleLocation): Response
    {
        return $this->render('modele_location/show.html.twig', [
            'modele_location' => $modeleLocation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_modele_location_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ModeleLocation $modeleLocation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ModeleLocationType::class, $modeleLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_modele_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('modele_location/edit.html.twig', [
            'modele_location' => $modeleLocation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_modele_location_delete', methods: ['POST'])]
    public function delete(Request $request, ModeleLocation $modeleLocation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$modeleLocation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($modeleLocation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_modele_location_index', [], Response::HTTP_SEE_OTHER);
    }
}
