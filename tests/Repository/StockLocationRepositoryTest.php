<?php

namespace App\Tests\Repository;

use App\Entity\Couleur;
use App\Entity\Entretien;
use App\Entity\ModeleLocation;
use App\Entity\StockLocation;
use App\Entity\TypeLocation;
use App\Repository\StockLocationRepository;

class StockLocationRepositoryTest extends RepositoryTestCase
{

    private StockLocationRepository $stockLocationRepository;


    protected function setUp(): void
    {
        parent::setUp();

        $this->stockLocationRepository = static::getContainer()
            ->get(StockLocationRepository::class);
    }


    private function createModeleLocation(): ModeleLocation
    {
        $entretien = new Entretien();
        $entretien
            ->setLavage("30°C max")
            ->setNettoyageSec("non")
            ->setRepassage("110°C max");

        $type = new TypeLocation();
        $type->setLibelle("Robes");

        $modele = new ModeleLocation();
        $modele
            ->setLibelle("Robe de soirée")
            ->setPrixLocation("59.90")
            ->setTissu("Soie")
            ->setSilhouette("Ajustée")
            ->setLongueur("Longue")
            ->setLongueurManches("Sans manches")
            ->setFormeCol("Bateau")
            ->setEntretien($entretien)
            ->setType($type);

        return $modele;
    }


    public function testStockLocationCanBeSavedAndFound(): void
    {
        $couleur = new Couleur();
        $couleur
            ->setNomCouleur("Rouge")
            ->setImage("rouge.jpg");

        $modele = $this->createModeleLocation();

        $stock = new StockLocation();
        $stock
            ->setTaille("M")
            ->setQuantite(3)
            ->setCouleur($couleur)
            ->setModeleLocation($modele);

        $this->entityManager->persist($couleur);
        $this->entityManager->persist($modele->getEntretien());
        $this->entityManager->persist($modele->getType());
        $this->entityManager->persist($modele);
        $this->entityManager->persist($stock);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $result = $this->stockLocationRepository->findOneBy([
            "taille" => "M"
        ]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(StockLocation::class, $result);

        $this->assertSame(3, $result->getQuantite());
        $this->assertSame("Rouge", $result->getCouleur()->getNomCouleur());
        $this->assertSame("Robe de soirée", $result->getModeleLocation()->getLibelle());
    }


    public function testStockLocationCanBeDeleted(): void
    {
        $couleur = new Couleur();
        $couleur
            ->setNomCouleur("Bleu")
            ->setImage("bleu.jpg");

        $modele = $this->createModeleLocation();

        $stock = new StockLocation();
        $stock
            ->setTaille("S")
            ->setQuantite(1)
            ->setCouleur($couleur)
            ->setModeleLocation($modele);

        $this->entityManager->persist($couleur);
        $this->entityManager->persist($modele->getEntretien());
        $this->entityManager->persist($modele->getType());
        $this->entityManager->persist($modele);
        $this->entityManager->persist($stock);
        $this->entityManager->flush();

        $id = $stock->getId();

        $this->entityManager->remove($stock);
        $this->entityManager->flush();

        $this->assertNull(
            $this->stockLocationRepository->find($id)
        );
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}