<?php

namespace App\Tests\Repository;

use App\Entity\Contact;
use App\Entity\User;
use App\Repository\ContactRepository;

class ContactRepositoryTest extends RepositoryTestCase
{

    private ContactRepository $contactRepository;


    protected function setUp(): void
    {
        parent::setUp();

        $this->contactRepository = static::getContainer()
            ->get(ContactRepository::class);
    }


    public function testContactCanBeSavedAndFound(): void
    {
        $user = new User();

        $user
            ->setEmail("contact.test@example.com")
            ->setPassword("password")
            ->setRoles(["ROLE_USER"])
            ->setNom("Dupont")
            ->setPrenom("Jean")
            ->setTel("0600000000")
            ->setAdresse("1 rue de Paris");


        $contact = new Contact();

        $contact
            ->setSujet("Information location")
            ->setMessage("Je souhaite louer une robe.")
            ->setDate(new \DateTime())
            ->setIdUser($user);


        $this->entityManager->persist($user);
        $this->entityManager->persist($contact);
        $this->entityManager->flush();
        $this->entityManager->clear();


        $result = $this->contactRepository->findOneBy([
            "sujet" => "Information location"
        ]);


        $this->assertNotNull($result);
        $this->assertInstanceOf(Contact::class, $result);

        $this->assertSame(
            "Je souhaite louer une robe.",
            $result->getMessage()
        );

        $this->assertSame(
            "contact.test@example.com",
            $result->getIdUser()->getEmail()
        );
    }


    public function testContactCanBeDeleted(): void
    {
        $contact = new Contact();

        $contact
            ->setSujet("Suppression test")
            ->setMessage("Test suppression")
            ->setDate(new \DateTime());


        $this->entityManager->persist($contact);
        $this->entityManager->flush();


        $id = $contact->getId();


        $this->entityManager->remove($contact);
        $this->entityManager->flush();


        $this->assertNull(
            $this->contactRepository->find($id)
        );
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}