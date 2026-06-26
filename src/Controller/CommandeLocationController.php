<?php
// a faire : adapter la création de commande - a modiified version of the CommandeLocationController.php file that includes the necessary changes to remove the 'location' field from the CommandeLocation entity and form type. The changes also ensure that the controller methods handle the updated entity correctly.

namespace App\Controller;

use App\Entity\CommandeLocation;
use App\Form\CommandeLocationType;
use App\Repository\CommandeLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commande_location')]
final class CommandeLocationController extends AbstractController
{
    #[Route(name: 'app_commande_location_index', methods: ['GET'])]
    public function index(CommandeLocationRepository $commandeLocationRepository): Response
    {
        return $this->render('commande_location/index.html.twig', [
            'commande_locations' => $commandeLocationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commande_location_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commandeLocation = new CommandeLocation();
        $form = $this->createForm(CommandeLocationType::class, $commandeLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commandeLocation);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande_location/new.html.twig', [
            'commande_location' => $commandeLocation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_location_show', methods: ['GET'])]
    public function show(CommandeLocation $commandeLocation): Response
    {
        return $this->render('commande_location/show.html.twig', [
            'commande_location' => $commandeLocation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_location_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CommandeLocation $commandeLocation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeLocationType::class, $commandeLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande_location/edit.html.twig', [
            'commande_location' => $commandeLocation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_location_delete', methods: ['POST'])]
    public function delete(Request $request, CommandeLocation $commandeLocation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commandeLocation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($commandeLocation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_location_index', [], Response::HTTP_SEE_OTHER);
    }
}
