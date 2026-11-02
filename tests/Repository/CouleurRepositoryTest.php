<?php

namespace App\Tests\Repository;

use App\Entity\Couleur;
use App\Repository\CouleurRepository;

class CouleurRepositoryTest extends RepositoryTestCase
{
    private CouleurRepository $couleurRepository;


    protected function setUp(): void
    {
        parent::setUp();

        $this->couleurRepository = static::getContainer()
            ->get(CouleurRepository::class);
    }


    public function testCouleurCanBeSavedAndFound(): void
    {
        $couleur = new Couleur();

        $couleur
            ->setNomCouleur("Rouge Bordeaux")
            ->setImage("rouge-bordeaux.jpg");


        $this->entityManager->persist($couleur);
        $this->entityManager->flush();
        $this->entityManager->clear();


        $savedCouleur = $this->couleurRepository->findOneBy([
            'nom_couleur' => "Rouge Bordeaux"
        ]);


        $this->assertNotNull($savedCouleur);
        $this->assertInstanceOf(Couleur::class, $savedCouleur);
        $this->assertSame(
            "Rouge Bordeaux",
            $savedCouleur->getNomCouleur()
        );
        $this->assertSame(
            "rouge-bordeaux.jpg",
            $savedCouleur->getImage()
        );
    }


    public function testCouleurCanBeDeleted(): void
    {
        $couleur = new Couleur();

        $couleur
            ->setNomCouleur("Suppression")
            ->setImage("suppression.jpg");


        $this->entityManager->persist($couleur);
        $this->entityManager->flush();


        $id = $couleur->getId();


        $this->entityManager->remove($couleur);
        $this->entityManager->flush();
        $this->entityManager->clear();


        $deletedCouleur = $this->couleurRepository->find($id);


        $this->assertNull($deletedCouleur);
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}