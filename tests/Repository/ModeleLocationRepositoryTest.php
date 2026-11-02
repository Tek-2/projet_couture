<?php

namespace App\Tests\Repository;

use App\Entity\Entretien;
use App\Entity\ModeleLocation;
use App\Entity\TypeLocation;
use App\Repository\ModeleLocationRepository;

class ModeleLocationRepositoryTest extends RepositoryTestCase
{
    private ModeleLocationRepository $modeleRepository;


    protected function setUp(): void
    {
        parent::setUp();

        $this->modeleRepository = static::getContainer()
            ->get(ModeleLocationRepository::class);
    }


    public function testModeleLocationCanBeSavedAndFound(): void
    {
        $entretien = new Entretien();

        $entretien
            ->setLavage("Main")
            ->setNettoyageSec("Oui")
            ->setRepassage("Doux")
            ->setDescription("Entretien délicat");


        $type = new TypeLocation();

        $type->setLibelle("Soirée");


        $modele = new ModeleLocation();

        $modele
            ->setLibelle("Robe Prestige")
            ->setPrixLocation("120")
            ->setTissu("Soie")
            ->setSilhouette("A")
            ->setLongueur("Longue")
            ->setLongueurManches("Courtes")
            ->setFormeCol("Col V")
            ->setDescription("Robe élégante")
            ->setEntretien($entretien)
            ->setType($type);


        $this->entityManager->persist($entretien);
        $this->entityManager->persist($type);
        $this->entityManager->persist($modele);

        $this->entityManager->flush();
        $this->entityManager->clear();


        $result = $this->modeleRepository->findOneBy([
            "libelle" => "Robe Prestige"
        ]);


        $this->assertNotNull($result);
        $this->assertSame(
            "Robe Prestige",
            $result->getLibelle()
        );
    }


    public function testSearchCollectionByName(): void
    {
        $result = $this->modeleRepository->searchCollection(
            "Robe",
            null,
            null,
            1,
            10
        );


        $this->assertArrayHasKey(
            "items",
            $result
        );

        $this->assertArrayHasKey(
            "pagination",
            $result
        );

        $this->assertSame(
            1,
            $result["pagination"]["currentPage"]
        );
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}