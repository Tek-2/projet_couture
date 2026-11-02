<?php

namespace App\Tests\Controller;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }


    public function testContactIndexPageIsSuccessful(): void
    {
        $this->client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
    }


    public function testContactShowPageIsSuccessful(): void
    {
        $contact = $this->createContact();

        $this->client->request('GET', '/contact/'.$contact->getId());

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            'body',
            $contact->getSujet()
        );
    }


    public function testContactNewPageIsSuccessful(): void
    {
        $this->client->request('GET', '/contact/new');

        $this->assertResponseIsSuccessful();
    }


    // NOTE: pas de test de soumission du formulaire "new" pour l'instant,
    // le contenu de ContactType.php (noms exacts des champs du formulaire)
    // n'a pas encore été fourni.


    private function createContact(): Contact
    {
        $user = new User();
        $user
            ->setEmail("contact.controller.".uniqid()."@example.com")
            ->setPassword("password")
            ->setRoles(["ROLE_USER"])
            ->setNom("Durand")
            ->setPrenom("Marie")
            ->setTel("0600000002")
            ->setAdresse("3 rue de Nice");

        $contact = new Contact();
        $contact
            ->setSujet("Sujet test ".uniqid())
            ->setMessage("Message de test")
            ->setDate(new \DateTime())
            ->setIdUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        return $contact;
    }
}