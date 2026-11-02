<?php

namespace App\Tests\Repository;

use App\Entity\Entretien;
use App\Entity\ModeleLocation;
use App\Entity\StockLocationImage;
use App\Entity\TypeLocation;
use App\Repository\StockLocationImageRepository;

class StockLocationImageRepositoryTest extends RepositoryTestCase
{

    private StockLocationImageRepository $stockLocationImageRepository;


    protected function setUp(): void
    {
        parent::setUp();

        $this->stockLocationImageRepository = static::getContainer()
            ->get(StockLocationImageRepository::class);
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

        $this->entityManager->persist($entretien);
        $this->entityManager->persist($type);
        $this->entityManager->persist($modele);

        return $modele;
    }


    public function testStockLocationImageCanBeSavedAndFound(): void
    {
        $modele = $this->createModeleLocation();

        $image = new StockLocationImage();
        $image
            ->setModeleLocation($modele)
            ->setUrl("https://example.com/images/robe-rouge.jpg")
            ->setOrdre(1);

        $this->entityManager->persist($image);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $result = $this->stockLocationImageRepository->findOneBy([
            "url" => "https://example.com/images/robe-rouge.jpg"
        ]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(StockLocationImage::class, $result);

        $this->assertSame(1, $result->getOrdre());
        $this->assertSame("Robe de soirée", $result->getModeleLocation()->getLibelle());
    }


    public function testStockLocationImageCanBeDeleted(): void
    {
        $modele = $this->createModeleLocation();

        $image = new StockLocationImage();
        $image
            ->setModeleLocation($modele)
            ->setUrl("https://example.com/images/robe-bleue.jpg")
            ->setOrdre(2);

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        $id = $image->getId();

        $this->entityManager->remove($image);
        $this->entityManager->flush();

        $this->assertNull(
            $this->stockLocationImageRepository->find($id)
        );
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}