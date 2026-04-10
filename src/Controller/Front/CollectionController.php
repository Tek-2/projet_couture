<?php

namespace App\Controller\Front;

use App\Entity\ModeleLocation;
use App\Repository\ModeleLocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/collection')]
class CollectionController extends AbstractController
{
    #[Route('', name: 'app_collection_index', methods: ['GET'])]
    public function index(Request $request, ModeleLocationRepository $repo): Response
    {
        // Récupération des filtres
        $search = $request->query->get('search');
        $category = $request->query->get('category');
        $price = $request->query->get('price');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 12;

        // Appel à une méthode custom du repository (à créer)
        $results = $repo->searchCollection($search, $category, $price, $page, $limit);

        return $this->render('collection/index.html.twig', [
            'modele_locations' => $results['items'],
            'pagination' => $results['pagination'],
        ]);
    }

    #[Route('/{id}', name: 'app_collection_show', methods: ['GET'])]
    public function show(ModeleLocation $modele, ModeleLocationRepository $repo): Response
    {
        // Modèles similaires (exemple simple : même silhouette)
        $related = $repo->findBy(
            ['silhouette' => $modele->getSilhouette()],
            limit: 3
        );

        return $this->render('collection/show.html.twig', [
            'modele' => $modele,
            'relatedModels' => $related,
        ]);
    }
}
