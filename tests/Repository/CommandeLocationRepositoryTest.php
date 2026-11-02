<?php

namespace App\Tests\Repository;

use App\Entity\CommandeLocation;
use App\Entity\User;
use App\Repository\CommandeLocationRepository;

class CommandeLocationRepositoryTest extends RepositoryTestCase
{
    private CommandeLocationRepository $commandeLocationRepository;


    protected function setUp(): void
    {
        parent::setUp();

        $this->commandeLocationRepository = static::getContainer()
            ->get(CommandeLocationRepository::class);
    }


    public function testCommandeLocationCanBeSavedAndFound(): void
    {
        $user = $this->createUser();


        $commandeLocation = new CommandeLocation();

        $commandeLocation
            ->setUser($user)
            ->setStatut('en_attente')
            ->setCommentaire('Test repository')
            ->setCreneauxClient([
                '2026-08-10 09:00',
                '2026-08-10 10:00'
            ]);


        $this->entityManager->persist($commandeLocation);
        $this->entityManager->flush();
        $this->entityManager->clear();


        $savedCommandeLocation = $this->commandeLocationRepository->findOneBy([
            'statut' => 'en_attente'
        ]);


        $this->assertNotNull($savedCommandeLocation);

        $this->assertInstanceOf(
            CommandeLocation::class,
            $savedCommandeLocation
        );


        $this->assertSame(
            'en_attente',
            $savedCommandeLocation->getStatut()
        );


        $this->assertSame(
            'Test repository',
            $savedCommandeLocation->getCommentaire()
        );


        $this->assertSame(
            [
                '2026-08-10 09:00',
                '2026-08-10 10:00'
            ],
            $savedCommandeLocation->getCreneauxClient()
        );
    }


    public function testCommandeLocationCanBeDeleted(): void
    {
        $user = $this->createUser();


        $commandeLocation = new CommandeLocation();

        $commandeLocation
            ->setUser($user)
            ->setStatut('en_attente');


        $this->entityManager->persist($commandeLocation);
        $this->entityManager->flush();


        $id = $commandeLocation->getId();


        $this->entityManager->remove($commandeLocation);
        $this->entityManager->flush();
        $this->entityManager->clear();


        $deletedCommandeLocation = $this->commandeLocationRepository->find($id);


        $this->assertNull(
            $deletedCommandeLocation
        );
    }


    private function createUser(): User
    {
        $user = new User();

        $user
            ->setEmail('test@commande-location.com')
            ->setPassword('password')
            ->setNom('Test')
            ->setPrenom('Utilisateur')
            ->setTel('0600000000')
            ->setAdresse('Adresse test');


        $this->entityManager->persist($user);
        $this->entityManager->flush();


        return $user;
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}