<?php

namespace App\Tests\Api;

use App\Entity\Entretien;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EntretienApiTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }



    public function testGetEntretiensCollection(): void
    {
        $this->client->request(
            'GET',
            '/api/entretiens'
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





    public function testGetOneEntretien(): void
    {
        $entretien = $this->createEntretien();


        $this->client->request(
            'GET',
            '/api/entretiens/'.$entretien->getId()
        );


        $this->assertResponseIsSuccessful();


        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            $entretien->getLavage(),
            $data['lavage']
        );


        $this->assertSame(
            $entretien->getNettoyageSec(),
            $data['nettoyageSec']
        );


        $this->assertSame(
            $entretien->getRepassage(),
            $data['repassage']
        );


        $this->assertSame(
            $entretien->getDescription(),
            $data['description']
        );

    }





    public function testCreateEntretien(): void
    {

        $this->client->request(
            'POST',
            '/api/entretiens',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'lavage' => 'Lavage main',

                'nettoyageSec' => 'Nettoyage à sec',

                'repassage' => 'Repassage doux',

                'description' => 'Créé depuis API'

            ])
        );



        $this->assertResponseStatusCodeSame(201);



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            'Lavage main',
            $data['lavage']
        );


        $this->assertSame(
            'Nettoyage à sec',
            $data['nettoyageSec']
        );


        $this->assertSame(
            'Repassage doux',
            $data['repassage']
        );


        $this->assertSame(
            'Créé depuis API',
            $data['description']
        );

    }






    public function testUpdateEntretien(): void
    {

        $entretien = $this->createEntretien();



        $this->client->request(
            'PATCH',
            '/api/entretiens/'.$entretien->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'lavage' => 'Lavage machine'

            ])
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            'Lavage machine',
            $data['lavage']
        );

    }







    public function testDeleteEntretien(): void
    {

        $entretien = $this->createEntretien();



        $this->client->request(
            'DELETE',
            '/api/entretiens/'.$entretien->getId()
        );



        $this->assertResponseStatusCodeSame(204);

    }







    public function testEntretienNotFound(): void
    {

        $this->client->request(
            'GET',
            '/api/entretiens/999999999'
        );


        $this->assertResponseStatusCodeSame(404);

    }








    private function createEntretien(): Entretien
    {

        $entretien = new Entretien();


        $entretien
            ->setLavage('30 degrés')
            ->setNettoyageSec('Oui')
            ->setRepassage('Doux')
            ->setDescription('Entretien de test');



        $this->entityManager->persist($entretien);

        $this->entityManager->flush();



        return $entretien;

    }

}