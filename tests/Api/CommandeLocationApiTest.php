<?php

namespace App\Tests\Api;

use App\Entity\CommandeLocation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommandeLocationApiTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }




    public function testGetCommandeLocationsCollection(): void
    {
        $this->client->request(
            'GET',
            '/api/commande_locations'
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






    public function testGetOneCommandeLocation(): void
    {
        $commande = $this->createCommandeLocation();



        $this->client->request(
            'GET',
            '/api/commande_locations/'.$commande->getId()
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            $commande->getStatut(),
            $data['statut']
        );



        $this->assertSame(
            $commande->getCommentaire(),
            $data['commentaire']
        );

    }









    public function testCreateCommandeLocation(): void
    {

        $user = $this->createUser();



        $this->client->request(
            'POST',
            '/api/commande_locations',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'user' => '/api/users/'.$user->getId(),

                'statut' => 'en_attente',

                'creneaux_client' => [
                    [
                        'date' => '2026-08-01',
                        'heure' => '10:00'
                    ]
                ],

                'commentaire' => 'Demande essayage'

            ])
        );



        $this->assertResponseStatusCodeSame(201);



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            'en_attente',
            $data['statut']
        );


        $this->assertSame(
            'Demande essayage',
            $data['commentaire']
        );

    }









    public function testUpdateCommandeLocation(): void
    {

        $commande = $this->createCommandeLocation();



        $this->client->request(
            'PATCH',
            '/api/commande_locations/'.$commande->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'statut' => 'valide'

            ])
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            'valide',
            $data['statut']
        );

    }









    public function testDeleteCommandeLocation(): void
    {

        $commande = $this->createCommandeLocation();



        $this->client->request(
            'DELETE',
            '/api/commande_locations/'.$commande->getId()
        );



        $this->assertResponseStatusCodeSame(204);

    }









    public function testCommandeLocationNotFound(): void
    {

        $this->client->request(
            'GET',
            '/api/commande_locations/999999999'
        );



        $this->assertResponseStatusCodeSame(404);

    }









    private function createCommandeLocation(): CommandeLocation
    {

        $user = $this->createUser();



        $commande = new CommandeLocation();



        $commande
            ->setUser($user)
            ->setStatut('en_attente')
            ->setCreneauxClient([
                [
                    'date' => '2026-08-01',
                    'heure' => '10:00'
                ]
            ])
            ->setCommentaire('Commande test');



        $this->entityManager->persist($commande);

        $this->entityManager->flush();



        return $commande;

    }









    private function createUser(): User
    {

        $user = new User();



        $user
            ->setEmail('test'.uniqid().'@example.com')
            ->setPassword('password')
            ->setNom('Dupont')
            ->setPrenom('Jean')
            ->setTel('0600000000')
            ->setAdresse('Adresse test')
            ->setRoles(['ROLE_USER']);



        $this->entityManager->persist($user);

        $this->entityManager->flush();



        return $user;

    }

}