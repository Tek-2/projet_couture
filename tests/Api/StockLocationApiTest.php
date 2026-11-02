<?php

namespace App\Tests\Api;

use App\Entity\Couleur;
use App\Entity\Entretien;
use App\Entity\ModeleLocation;
use App\Entity\StockLocation;
use App\Entity\TypeLocation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StockLocationApiTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }






    public function testGetStockLocationsCollection(): void
    {
        $this->client->request(
            'GET',
            '/api/stock_locations'
        );


        $this->assertResponseIsSuccessful();


        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );


        $this->assertArrayHasKey(
            'member',
            $data
        );
    }








    public function testGetOneStockLocation(): void
    {

        $stock = $this->createStockLocation();



        $this->client->request(
            'GET',
            '/api/stock_locations/'.$stock->getId()
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            $stock->getTaille(),
            $data['taille']
        );


        $this->assertSame(
            $stock->getQuantite(),
            $data['quantite']
        );

    }








    public function testCreateStockLocation(): void
    {

        $couleur = $this->createCouleur();

        $modele = $this->createModeleLocation();



        $this->client->request(
            'POST',
            '/api/stock_locations',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'taille' => 'L',

                'quantite' => 10,

                'couleur' => '/api/couleurs/'.$couleur->getId(),

                'modeleLocation' =>
                    '/api/modele_locations/'.$modele->getId()

            ])
        );



        $this->assertResponseStatusCodeSame(201);



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            'L',
            $data['taille']
        );


        $this->assertSame(
            10,
            $data['quantite']
        );

    }









    public function testUpdateStockLocation(): void
    {

        $stock = $this->createStockLocation();



        $this->client->request(
            'PATCH',
            '/api/stock_locations/'.$stock->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'quantite' => 20

            ])
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            20,
            $data['quantite']
        );

    }









    public function testDeleteStockLocation(): void
    {

        $stock = $this->createStockLocation();



        $this->client->request(
            'DELETE',
            '/api/stock_locations/'.$stock->getId()
        );



        $this->assertResponseStatusCodeSame(204);

    }









    public function testStockLocationNotFound(): void
    {

        $this->client->request(
            'GET',
            '/api/stock_locations/999999999'
        );


        $this->assertResponseStatusCodeSame(404);

    }









    private function createStockLocation(): StockLocation
    {

        $stock = new StockLocation();


        $stock
            ->setTaille('M')
            ->setQuantite(5)
            ->setCouleur($this->createCouleur())
            ->setModeleLocation($this->createModeleLocation());



        $this->entityManager->persist($stock);

        $this->entityManager->flush();



        return $stock;

    }









    private function createCouleur(): Couleur
    {

        $couleur = new Couleur();


        $couleur
            ->setNomCouleur('Noir')
            ->setImage('noir.png');



        $this->entityManager->persist($couleur);

        $this->entityManager->flush();



        return $couleur;

    }









    private function createModeleLocation(): ModeleLocation
    {

        $entretien = new Entretien();


        $entretien
            ->setLavage('30 degrés')
            ->setNettoyageSec('Oui')
            ->setRepassage('Doux')
            ->setDescription('Test');


        $this->entityManager->persist($entretien);



        $type = new TypeLocation();


        $type->setLibelle('Mariage');


        $this->entityManager->persist($type);



        $modele = new ModeleLocation();


        $modele
            ->setLibelle('Robe API Test')
            ->setPrixLocation('300.00')
            ->setTissu('Soie')
            ->setSilhouette('Empire')
            ->setLongueur('Longue')
            ->setLongueurManches('Longues')
            ->setFormeCol('Rond')
            ->setDescription('Test API')
            ->setEntretien($entretien)
            ->setType($type);



        $this->entityManager->persist($modele);

        $this->entityManager->flush();



        return $modele;

    }

}