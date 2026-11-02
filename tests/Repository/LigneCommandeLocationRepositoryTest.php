<?php

namespace App\Tests\Repository;

use App\Entity\CommandeLocation;
use App\Entity\Couleur;
use App\Entity\Entretien;
use App\Entity\LigneCommandeLocation;
use App\Entity\ModeleLocation;
use App\Entity\StockLocation;
use App\Entity\TypeLocation;
use App\Entity\User;
use App\Repository\LigneCommandeLocationRepository;

class LigneCommandeLocationRepositoryTest extends RepositoryTestCase
{

    private LigneCommandeLocationRepository $ligneCommandeLocationRepository;


    protected function setUp(): void
    {
        parent::setUp();

        $this->ligneCommandeLocationRepository = static::getContainer()
            ->get(LigneCommandeLocationRepository::class);
    }


    private function createStockLocation(): StockLocation
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

        $couleur = new Couleur();
        $couleur
            ->setNomCouleur("Rouge")
            ->setImage("rouge.jpg");

        $stock = new StockLocation();
        $stock
            ->setTaille("M")
            ->setQuantite(5)
            ->setCouleur($couleur)
            ->setModeleLocation($modele);

        $this->entityManager->persist($entretien);
        $this->entityManager->persist($type);
        $this->entityManager->persist($modele);
        $this->entityManager->persist($couleur);
        $this->entityManager->persist($stock);

        return $stock;
    }


    private function createCommandeLocation(): CommandeLocation
    {
        $user = new User();
        $user
            ->setEmail("client.ligne@example.com")
            ->setPassword("password")
            ->setRoles(["ROLE_USER"])
            ->setNom("Martin")
            ->setPrenom("Alice")
            ->setTel("0600000001")
            ->setAdresse("2 rue de Lyon");

        $commande = new CommandeLocation();
        $commande->setUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($commande);

        return $commande;
    }


    public function testLigneCommandeLocationCanBeSavedAndFound(): void
    {
        $stock = $this->createStockLocation();
        $commande = $this->createCommandeLocation();

        $ligne = new LigneCommandeLocation();
        $ligne
            ->setCommandeloc($commande)
            ->setStockloc($stock)
            ->setPrix("59.90")
            ->setQuantite(2);

        $this->entityManager->persist($ligne);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $result = $this->ligneCommandeLocationRepository->findOneBy([
            "prix" => "59.90"
        ]);

        $this->assertNotNull($result);
        $this->assertInstanceOf(LigneCommandeLocation::class, $result);

        $this->assertSame(2, $result->getQuantite());
        $this->assertSame("client.ligne@example.com", $result->getCommandeloc()->getUser()->getEmail());
        $this->assertSame("M", $result->getStockloc()->getTaille());
    }


    public function testLigneCommandeLocationCanBeDeleted(): void
    {
        $stock = $this->createStockLocation();
        $commande = $this->createCommandeLocation();

        $ligne = new LigneCommandeLocation();
        $ligne
            ->setCommandeloc($commande)
            ->setStockloc($stock)
            ->setPrix("39.90")
            ->setQuantite(1);

        $this->entityManager->persist($ligne);
        $this->entityManager->flush();

        $id = $ligne->getId();

        $this->entityManager->remove($ligne);
        $this->entityManager->flush();

        $this->assertNull(
            $this->ligneCommandeLocationRepository->find($id)
        );
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}