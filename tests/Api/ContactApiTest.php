<?php

namespace App\Tests\Api;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactApiTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }




    public function testGetContactsCollection(): void
    {
        $this->client->request(
            'GET',
            '/api/contacts'
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






    public function testGetOneContact(): void
    {
        $contact = $this->createContact();



        $this->client->request(
            'GET',
            '/api/contacts/'.$contact->getId()
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            $contact->getSujet(),
            $data['sujet']
        );



        $this->assertSame(
            $contact->getMessage(),
            $data['message']
        );



        $this->assertNotNull(
            $data['date']
        );

    }









    public function testCreateContact(): void
    {

        $this->client->request(
            'POST',
            '/api/contacts',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'sujet' => 'Demande information',

                'message' => 'Je souhaite avoir plus de renseignements.',

                'date' => '2026-07-31T10:00:00'

            ])
        );



        $this->assertResponseStatusCodeSame(201);



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            'Demande information',
            $data['sujet']
        );



        $this->assertSame(
            'Je souhaite avoir plus de renseignements.',
            $data['message']
        );


        $this->assertNotNull(
            $data['date']
        );

    }









    public function testUpdateContact(): void
    {

        $contact = $this->createContact();



        $this->client->request(
            'PATCH',
            '/api/contacts/'.$contact->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([

                'sujet' => 'Sujet modifié'

            ])
        );



        $this->assertResponseIsSuccessful();



        $data = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );



        $this->assertSame(
            'Sujet modifié',
            $data['sujet']
        );

    }









    public function testDeleteContact(): void
    {

        $contact = $this->createContact();



        $this->client->request(
            'DELETE',
            '/api/contacts/'.$contact->getId()
        );



        $this->assertResponseStatusCodeSame(204);

    }








    public function testContactNotFound(): void
    {

        $this->client->request(
            'GET',
            '/api/contacts/999999999'
        );


        $this->assertResponseStatusCodeSame(404);

    }









    private function createContact(): Contact
    {

        $contact = new Contact();



        $contact
            ->setSujet('Sujet test')
            ->setMessage('Message de test')
            ->setDate(new \DateTime());



        $this->entityManager->persist($contact);

        $this->entityManager->flush();



        return $contact;

    }

}