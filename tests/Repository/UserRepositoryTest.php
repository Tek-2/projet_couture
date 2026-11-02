<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;

class UserRepositoryTest extends RepositoryTestCase
{
    
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function testUserCanBeSavedAndFound(): void
    {
        $user = new User();
        $user->setEmail('jean.dupont@test.fr');
        $user->setPassword('password');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setNom('Dupont');
        $user->setPrenom('Jean');
        $user->setTel('0601020304');
        $user->setAdresse('10 rue de Paris');

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $saved = $this->userRepository->findOneBy([
            'email' => 'jean.dupont@test.fr'
        ]);

        $this->assertNotNull($saved);
        $this->assertSame('Dupont', $saved->getNom());
        $this->assertSame('Jean', $saved->getPrenom());
        $this->assertSame('0601020304', $saved->getTel());
        $this->assertSame('10 rue de Paris', $saved->getAdresse());
        $this->assertContains('ROLE_ADMIN', $saved->getRoles());
        $this->assertContains('ROLE_USER', $saved->getRoles());
    }

    public function testUserCanBeDeleted(): void
    {
        $user = new User();
        $user->setEmail('delete@test.fr');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);
        $user->setNom('Martin');
        $user->setPrenom('Paul');
        $user->setTel('0600000000');
        $user->setAdresse('20 avenue de Lyon');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $id = $user->getId();

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $this->assertNull($this->userRepository->find($id));
    }

    public function testPasswordCanBeUpgraded(): void
    {
        $user = new User();
        $user->setEmail('upgrade@test.fr');
        $user->setPassword('ancien');
        $user->setRoles(['ROLE_USER']);
        $user->setNom('Durand');
        $user->setPrenom('Alice');
        $user->setTel('0611111111');
        $user->setAdresse('5 rue Victor Hugo');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->userRepository->upgradePassword($user, 'nouveau');

        $this->entityManager->clear();

        $saved = $this->userRepository->findOneBy([
            'email' => 'upgrade@test.fr'
        ]);

        $this->assertSame('nouveau', $saved->getPassword());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}