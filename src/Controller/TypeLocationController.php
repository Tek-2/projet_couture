<?php

namespace App\Controller;

use App\Entity\TypeLocation;
use App\Form\TypeLocationType;
use App\Repository\TypeLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/type_location')]
#[IsGranted('ROLE_ADMIN')]
final class TypeLocationController extends AbstractController
{
    #[Route(name: 'app_type_location_index', methods: ['GET'])]
    public function index(TypeLocationRepository $typeLocationRepository): Response
    {
        return $this->render('type_location/index.html.twig', [
            'type_locations' => $typeLocationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_type_location_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typeLocation = new TypeLocation();
        $form = $this->createForm(TypeLocationType::class, $typeLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typeLocation);
            $entityManager->flush();

            return $this->redirectToRoute('app_type_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_location/new.html.twig', [
            'type_location' => $typeLocation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_location_show', methods: ['GET'])]
    public function show(TypeLocation $typeLocation): Response
    {
        return $this->render('type_location/show.html.twig', [
            'type_location' => $typeLocation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_location_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeLocation $typeLocation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypeLocationType::class, $typeLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_type_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_location/edit.html.twig', [
            'type_location' => $typeLocation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_location_delete', methods: ['POST'])]
    public function delete(Request $request, TypeLocation $typeLocation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeLocation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($typeLocation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_type_location_index', [], Response::HTTP_SEE_OTHER);
    }
}
