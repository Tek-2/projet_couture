<?php

namespace App\Tests\Api;

use App\Entity\CommandeLocation;
use App\Entity\Couleur;
use App\Entity\Entretien;
use App\Entity\LigneCommandeLocation;
use App\Entity\ModeleLocation;
use App\Entity\StockLocation;
use App\Entity\TypeLocation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LigneCommandeLocationApiTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }






    public function testGetLignesCommandeLocationCollection(): void
    {
        $this->client->request(
            'GET',
            '/api/ligne_commande_locations'
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








    public function testGetOneLigneCommandeLocation(): void
    {
        $ligne = $this->createLigneCommandeLocation();



        $this->client->request(
            'GET',
            '/api/ligne_commande_locations/'.$ligne->getId()
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            $ligne->getPrix(),
            $data['prix']
        );


        $this->assertSame(
            $ligne->getQuantite(),
            $data['quantite']
        );
    }










    public function testCreateLigneCommandeLocation(): void
    {
        $commande = $this->createCommandeLocation();

        $stock = $this->createStockLocation();



        $this->client->request(
            'POST',
            '/api/ligne_commande_locations',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                "commandeloc" =>
                    "/api/commande_locations/".$commande->getId(),

                "stockloc" =>
                    "/api/stock_locations/".$stock->getId(),

                "prix" => "250.00",

                "quantite" => 1

            ])
        );



        $this->assertResponseStatusCodeSame(201);



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            "250.00",
            $data['prix']
        );


        $this->assertSame(
            1,
            $data['quantite']
        );
    }









    public function testUpdateLigneCommandeLocation(): void
    {
        $ligne = $this->createLigneCommandeLocation();



        $this->client->request(
            'PATCH',
            '/api/ligne_commande_locations/'.$ligne->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                "prix" => "300.00"

            ])
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            "300.00",
            $data['prix']
        );
    }









    public function testDeleteLigneCommandeLocation(): void
    {
        $ligne = $this->createLigneCommandeLocation();



        $this->client->request(
            'DELETE',
            '/api/ligne_commande_locations/'.$ligne->getId()
        );



        $this->assertResponseStatusCodeSame(204);
    }









    public function testLigneCommandeLocationNotFound(): void
    {
        $this->client->request(
            'GET',
            '/api/ligne_commande_locations/999999999'
        );


        $this->assertResponseStatusCodeSame(404);
    }









    private function createLigneCommandeLocation(): LigneCommandeLocation
    {
        $ligne = new LigneCommandeLocation();



        $ligne
            ->setCommandeloc($this->createCommandeLocation())
            ->setStockloc($this->createStockLocation())
            ->setPrix("250.00")
            ->setQuantite(1);



        $this->entityManager->persist($ligne);

        $this->entityManager->flush();



        return $ligne;
    }









    private function createCommandeLocation(): CommandeLocation
    {
        $commande = new CommandeLocation();


        $commande
            ->setUser($this->createUser())
            ->setStatut("en_attente")
            ->setCommentaire("Test API");


        $this->entityManager->persist($commande);

        $this->entityManager->flush();



        return $commande;
    }









    private function createStockLocation(): StockLocation
    {
        $stock = new StockLocation();


        $stock
            ->setTaille("M")
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
            ->setNomCouleur("Noir")
            ->setImage("noir.png");



        $this->entityManager->persist($couleur);

        $this->entityManager->flush();



        return $couleur;
    }









    private function createModeleLocation(): ModeleLocation
    {
        $entretien = new Entretien();


        $entretien
            ->setLavage("30 degrés")
            ->setNettoyageSec("Oui")
            ->setRepassage("Doux")
            ->setDescription("Test");


        $this->entityManager->persist($entretien);



        $type = new TypeLocation();


        $type->setLibelle("Mariage");


        $this->entityManager->persist($type);



        $modele = new ModeleLocation();


        $modele
            ->setLibelle("Robe test")
            ->setPrixLocation("500.00")
            ->setTissu("Soie")
            ->setSilhouette("Empire")
            ->setLongueur("Longue")
            ->setLongueurManches("Longues")
            ->setFormeCol("Rond")
            ->setDescription("Test")
            ->setEntretien($entretien)
            ->setType($type);



        $this->entityManager->persist($modele);

        $this->entityManager->flush();



        return $modele;
    }









    private function createUser(): User
    {
        $user = new User();



        $user
            ->setEmail('test'.uniqid().'@example.com')
            ->setPassword('password')
            ->setRoles(['ROLE_USER'])
            ->setNom('Test')
            ->setPrenom('User')
            ->setTel('0600000000')
            ->setAdresse('Adresse test');



        $this->entityManager->persist($user);

        $this->entityManager->flush();



        return $user;
    }
}