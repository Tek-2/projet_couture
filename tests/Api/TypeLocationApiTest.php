<?php

namespace App\Tests\Api;

use App\Entity\TypeLocation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TypeLocationApiTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }




    public function testGetTypeLocationsCollection(): void
    {
        $this->client->request(
            'GET',
            '/api/type_locations'
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






    public function testGetOneTypeLocation(): void
    {
        $typeLocation = $this->createTypeLocation();



        $this->client->request(
            'GET',
            '/api/type_locations/'.$typeLocation->getId()
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            $typeLocation->getLibelle(),
            $data['libelle']
        );

    }









    public function testCreateTypeLocation(): void
    {

        $this->client->request(
            'POST',
            '/api/type_locations',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'libelle' => 'Mariage'

            ])
        );



        $this->assertResponseStatusCodeSame(201);



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            'Mariage',
            $data['libelle']
        );

    }









    public function testUpdateTypeLocation(): void
    {

        $typeLocation = $this->createTypeLocation();



        $this->client->request(
            'PATCH',
            '/api/type_locations/'.$typeLocation->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'libelle' => 'Soirée'

            ])
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            'Soirée',
            $data['libelle']
        );

    }









    public function testDeleteTypeLocation(): void
    {

        $typeLocation = $this->createTypeLocation();



        $this->client->request(
            'DELETE',
            '/api/type_locations/'.$typeLocation->getId()
        );



        $this->assertResponseStatusCodeSame(204);

    }









    public function testTypeLocationNotFound(): void
    {

        $this->client->request(
            'GET',
            '/api/type_locations/999999999'
        );



        $this->assertResponseStatusCodeSame(404);

    }









    private function createTypeLocation(): TypeLocation
    {

        $typeLocation = new TypeLocation();


        $typeLocation
            ->setLibelle('Mariage');



        $this->entityManager->persist($typeLocation);

        $this->entityManager->flush();



        return $typeLocation;

    }

}