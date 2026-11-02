<?php

namespace App\Tests\Controller;

use App\Entity\Couleur;
use App\Entity\Entretien;
use App\Entity\ModeleLocation;
use App\Entity\StockLocation;
use App\Entity\TypeLocation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShowroomControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }


    public function testShowroomIndexIsSuccessfulWhenEmpty(): void
    {
        $this->client->request('GET', '/showroom');

        $this->assertResponseIsSuccessful();
    }


    public function testAddModeleToShowroomThenAppearsInIndex(): void
    {
        $stock = $this->createStockLocation();

        $this->client->request('GET', '/showroom/add/'.$stock->getId());

        $this->assertResponseRedirects('/showroom');

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            'body',
            $stock->getModeleLocation()->getLibelle()
        );
    }


    public function testAddSameModeleTwiceDoesNotCreateDuplicate(): void
    {
        $stock = $this->createStockLocation();

        $this->client->request('GET', '/showroom/add/'.$stock->getId());
        $this->client->request('GET', '/showroom/add/'.$stock->getId());

        $crawler = $this->client->request('GET', '/showroom');

        $this->assertResponseIsSuccessful();

        $occurrences = substr_count(
            $crawler->filter('body')->text(),
            $stock->getModeleLocation()->getLibelle()
        );

        $this->assertSame(1, $occurrences);
    }


    public function testRemoveModeleFromShowroom(): void
    {
        $stock = $this->createStockLocation();

        $this->client->request('GET', '/showroom/add/'.$stock->getId());
        $this->client->request('GET', '/showroom/remove/'.$stock->getId());

        $crawler = $this->client->request('GET', '/showroom');

        $this->assertResponseIsSuccessful();

        $this->assertStringNotContainsString(
            $stock->getModeleLocation()->getLibelle(),
            $crawler->filter('body')->text()
        );
    }


    public function testClearShowroomEmptiesIt(): void
    {
        $stock = $this->createStockLocation();

        $this->client->request('GET', '/showroom/add/'.$stock->getId());
        $this->client->request('GET', '/showroom/clear');

        $crawler = $this->client->request('GET', '/showroom');

        $this->assertResponseIsSuccessful();

        $this->assertStringNotContainsString(
            $stock->getModeleLocation()->getLibelle(),
            $crawler->filter('body')->text()
        );
    }


    private function createStockLocation(): StockLocation
    {
        $entretien = new Entretien();
        $entretien
            ->setLavage("30°C max")
            ->setNettoyageSec("non")
            ->setRepassage("110°C max");

        $type = new TypeLocation();
        $type->setLibelle("Robes "."showroom_".uniqid());

        $modele = new ModeleLocation();
        $modele
            ->setLibelle("Robe showroom ".uniqid())
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
            ->setQuantite(3)
            ->setCouleur($couleur)
            ->setModeleLocation($modele);

        $this->entityManager->persist($entretien);
        $this->entityManager->persist($type);
        $this->entityManager->persist($modele);
        $this->entityManager->persist($couleur);
        $this->entityManager->persist($stock);
        $this->entityManager->flush();

        return $stock;
    }
}