<?php

namespace App\Tests\Api;

use App\Entity\Couleur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CouleurApiTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }




    public function testGetCouleursCollection(): void
    {
        $this->client->request(
            'GET',
            '/api/couleurs'
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





    public function testGetOneCouleur(): void
    {
        $couleur = $this->createCouleur();


        $this->client->request(
            'GET',
            '/api/couleurs/'.$couleur->getId()
        );


        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            $couleur->getNomCouleur(),
            $data['nom_couleur']
        );



        $this->assertSame(
            $couleur->getImage(),
            $data['image']
        );
    }







    public function testCreateCouleur(): void
    {

        $this->client->request(
            'POST',
            '/api/couleurs',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'nom_couleur' => 'Rouge',

                'image' => 'rouge.png'

            ])
        );



        $this->assertResponseStatusCodeSame(201);



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            'Rouge',
            $data['nom_couleur']
        );


        $this->assertSame(
            'rouge.png',
            $data['image']
        );

    }









    public function testUpdateCouleur(): void
    {

        $couleur = $this->createCouleur();



        $this->client->request(
            'PATCH',
            '/api/couleurs/'.$couleur->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'nom_couleur' => 'Bleu'

            ])
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            'Bleu',
            $data['nom_couleur']
        );

    }









    public function testDeleteCouleur(): void
    {

        $couleur = $this->createCouleur();



        $this->client->request(
            'DELETE',
            '/api/couleurs/'.$couleur->getId()
        );



        $this->assertResponseStatusCodeSame(204);

    }








    public function testCouleurNotFound(): void
    {

        $this->client->request(
            'GET',
            '/api/couleurs/999999999'
        );



        $this->assertResponseStatusCodeSame(404);

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

}