<?php

namespace App\Tests\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserApiTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }






    public function testGetUsersCollection(): void
    {
        $this->client->request(
            'GET',
            '/api/users'
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








    public function testGetOneUser(): void
    {

        $user = $this->createUser();



        $this->client->request(
            'GET',
            '/api/users/'.$user->getId()
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            $user->getEmail(),
            $data['email']
        );



        $this->assertSame(
            $user->getNom(),
            $data['nom']
        );



        $this->assertSame(
            $user->getPrenom(),
            $data['prenom']
        );

    }









    public function testCreateUser(): void
    {

        $email = 'api'.uniqid().'@example.com';



        $this->client->request(
            'POST',
            '/api/users',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'email' => $email,

                'password' => 'password123',

                'roles' => [
                    'ROLE_USER'
                ],

                'nom' => 'Martin',

                'prenom' => 'Paul',

                'tel' => '0611111111',

                'adresse' => 'Adresse API'


            ])
        );



        $this->assertResponseStatusCodeSame(201);



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            $email,
            $data['email']
        );



        $this->assertSame(
            'Martin',
            $data['nom']
        );



        $this->assertSame(
            'Paul',
            $data['prenom']
        );

    }









    public function testUpdateUser(): void
    {

        $user = $this->createUser();



        $this->client->request(
            'PATCH',
            '/api/users/'.$user->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'nom' => 'Dupont'

            ])
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            'Dupont',
            $data['nom']
        );

    }









    public function testDeleteUser(): void
    {

        $user = $this->createUser();



        $this->client->request(
            'DELETE',
            '/api/users/'.$user->getId()
        );



        $this->assertResponseStatusCodeSame(204);

    }









    public function testUserNotFound(): void
    {

        $this->client->request(
            'GET',
            '/api/users/999999999'
        );



        $this->assertResponseStatusCodeSame(404);

    }









    private function createUser(): User
    {

        $user = new User();



        $user
            ->setEmail('test'.uniqid().'@example.com')
            ->setPassword('password123')
            ->setRoles(['ROLE_USER'])
            ->setNom('Test')
            ->setPrenom('Utilisateur')
            ->setTel('0600000000')
            ->setAdresse('Adresse test');



        $this->entityManager->persist($user);

        $this->entityManager->flush();



        return $user;

    }

}