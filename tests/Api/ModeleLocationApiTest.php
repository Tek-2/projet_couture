<?php

namespace App\Tests\Api;

use App\Entity\Entretien;
use App\Entity\ModeleLocation;
use App\Entity\TypeLocation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModeleLocationApiTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }


    public function testGetModeleLocationsCollection(): void
    {
        $this->client->request(
            'GET',
            '/api/modele_locations'
        );

        $this->assertResponseIsSuccessful();

        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );

        $this->assertArrayHasKey('member', $data);
    }


    public function testGetOneModeleLocation(): void
    {
        $modeleLocation = $this->createModeleLocation();


        $this->client->request(
            'GET',
            '/api/modele_locations/'.$modeleLocation->getId()
        );


        $this->assertResponseIsSuccessful();


        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );


        $this->assertSame(
            $modeleLocation->getLibelle(),
            $data['libelle']
        );


        $this->assertSame(
            $modeleLocation->getPrixLocation(),
            $data['prixLocation']
        );


        $this->assertSame(
            $modeleLocation->getTissu(),
            $data['tissu']
        );


        $this->assertSame(
            $modeleLocation->getSilhouette(),
            $data['silhouette']
        );


        $this->assertSame(
            $modeleLocation->getLongueur(),
            $data['longueur']
        );


        $this->assertSame(
            $modeleLocation->getLongueurManches(),
            $data['longueurManches']
        );


        $this->assertSame(
            $modeleLocation->getFormeCol(),
            $data['formeCol']
        );
    }



    public function testCreateModeleLocation(): void
    {

        $entretien = $this->createEntretien();

        $type = $this->createTypeLocation();



        $this->client->request(
            'POST',
            '/api/modele_locations',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'libelle' => 'Robe API Test',

                'prixLocation' => '50.00',

                'tissu' => 'Soie',

                'silhouette' => 'Empire',

                'longueur' => 'Longue',

                'longueurManches' => 'Courtes',

                'formeCol' => 'Col rond',

                'description' => 'Création depuis API',

                'entretien' => '/api/entretiens/'.$entretien->getId(),

                'type' => '/api/type_locations/'.$type->getId()

            ])
        );



        $this->assertResponseStatusCodeSame(201);



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );


        $this->assertSame(
            'Robe API Test',
            $data['libelle']
        );


        $this->assertSame(
            '50.00',
            $data['prixLocation']
        );

    }



    public function testUpdateModeleLocation(): void
    {

        $modeleLocation = $this->createModeleLocation();


        $this->client->request(
            'PATCH',
            '/api/modele_locations/'.$modeleLocation->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'libelle' => 'Robe Modifiée',

                'prixLocation' => '99.99'

            ])
        );


        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );


        $this->assertSame(
            'Robe Modifiée',
            $data['libelle']
        );


        $this->assertSame(
            '99.99',
            $data['prixLocation']
        );

    }



    public function testDeleteModeleLocation(): void
    {

        $modeleLocation = $this->createModeleLocation();


        $this->client->request(
            'DELETE',
            '/api/modele_locations/'.$modeleLocation->getId()
        );


        $this->assertResponseStatusCodeSame(204);

    }



    public function testModeleLocationNotFound(): void
    {

        $this->client->request(
            'GET',
            '/api/modele_locations/999999999'
        );


        $this->assertResponseStatusCodeSame(404);

    }




    private function createModeleLocation(): ModeleLocation
    {

        $entretien = $this->createEntretien();


        $type = $this->createTypeLocation();



        $modeleLocation = new ModeleLocation();


        $modeleLocation
            ->setLibelle('Robe Test '.uniqid())
            ->setPrixLocation('29.99')
            ->setTissu('Satin')
            ->setSilhouette('Princesse')
            ->setLongueur('Longue')
            ->setLongueurManches('Longues')
            ->setFormeCol('Col V')
            ->setDescription('Description test')
            ->setEntretien($entretien)
            ->setType($type);



        $this->entityManager->persist($modeleLocation);

        $this->entityManager->flush();


        return $modeleLocation;

    }




    private function createEntretien(): Entretien
    {

        $entretien = new Entretien();


        $entretien
            ->setLavage('30 degrés')
            ->setNettoyageSec('Oui')
            ->setRepassage('Doux')
            ->setDescription('Entretien test');



        $this->entityManager->persist($entretien);

        $this->entityManager->flush();


        return $entretien;

    }





    private function createTypeLocation(): TypeLocation
    {

        $type = new TypeLocation();


        $type->setLibelle('Mariage');



        $this->entityManager->persist($type);

        $this->entityManager->flush();



        return $type;

    }

}