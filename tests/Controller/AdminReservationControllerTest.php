<?php

namespace App\Tests\Controller;

use App\Entity\CommandeLocation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminReservationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }


    public function testIndexRedirectsAnonymousUserToLogin(): void
    {
        $this->client->request('GET', '/reservation/admin');

        $this->assertResponseRedirects();
    }


    public function testIndexIsForbiddenForRegularUser(): void
    {
        $this->loginAs(['ROLE_USER']);

        $this->client->request('GET', '/reservation/admin');

        $this->assertResponseStatusCodeSame(403);
    }


    public function testIndexPageIsSuccessfulForAdmin(): void
    {
        $this->loginAs(['ROLE_ADMIN']);

        $this->client->request('GET', '/reservation/admin');

        $this->assertResponseIsSuccessful();
    }


    public function testShowPageIsSuccessful(): void
    {
        $this->loginAs(['ROLE_ADMIN']);

        $reservation = $this->createReservation();

        $this->client->request('GET', '/reservation/admin/'.$reservation->getId());

        $this->assertResponseIsSuccessful();
    }


    public function testAdminCanProposerUnCreneau(): void
    {
        $this->loginAs(['ROLE_ADMIN']);

        $reservation = $this->createReservation();

        $this->client->request(
            'POST',
            '/reservation/admin/'.$reservation->getId().'/proposer',
            [
                'date' => '2026-09-15',
                'time' => '14:00',
            ]
        );

        $this->assertResponseRedirects(
            '/reservation/admin/'.$reservation->getId()
        );

        $this->entityManager->clear();

        $updated = $this->entityManager
            ->getRepository(CommandeLocation::class)
            ->find($reservation->getId());

        $this->assertSame('proposition', $updated->getStatut());
        $this->assertNotNull($updated->getPropositionDebut());
        $this->assertSame(
            '14:30',
            $updated->getPropositionFin()->format('H:i')
        );
    }


    public function testAdminCanValiderClientAvecCreneauChoisi(): void
    {
        $this->loginAs(['ROLE_ADMIN']);

        $reservation = $this->createReservation();

        $this->client->request(
            'POST',
            '/reservation/admin/'.$reservation->getId().'/valider-client',
            [
                'creneau' => '2026-09-15|10:00',
            ]
        );

        $this->assertResponseRedirects(
            '/reservation/admin/'.$reservation->getId()
        );

        $this->entityManager->clear();

        $updated = $this->entityManager
            ->getRepository(CommandeLocation::class)
            ->find($reservation->getId());

        $this->assertSame('valide', $updated->getStatut());
        $this->assertNotNull($updated->getDateDebutValidee());
    }


    public function testAdminValiderClientSansChoixRedirectsWithError(): void
    {
        $this->loginAs(['ROLE_ADMIN']);

        $reservation = $this->createReservation();

        $this->client->request(
            'POST',
            '/reservation/admin/'.$reservation->getId().'/valider-client',
            []
        );

        $this->assertResponseRedirects(
            '/reservation/admin/'.$reservation->getId()
        );

        $this->entityManager->clear();

        $updated = $this->entityManager
            ->getRepository(CommandeLocation::class)
            ->find($reservation->getId());

        $this->assertNotSame('valide', $updated->getStatut());
    }


    public function testAdminCanValiderLaPropositionExistante(): void
    {
        $this->loginAs(['ROLE_ADMIN']);

        $reservation = $this->createReservation();
        $reservation->setPropositionDebut(new \DateTimeImmutable('2026-09-15 10:00'));
        $reservation->setPropositionFin(new \DateTimeImmutable('2026-09-15 10:30'));
        $this->entityManager->flush();

        $this->client->request(
            'POST',
            '/reservation/admin/'.$reservation->getId().'/valider'
        );

        $this->assertResponseRedirects(
            '/reservation/admin/'.$reservation->getId()
        );

        $this->entityManager->clear();

        $updated = $this->entityManager
            ->getRepository(CommandeLocation::class)
            ->find($reservation->getId());

        $this->assertSame('valide', $updated->getStatut());
    }


    public function testAdminCanRefuserUneReservation(): void
    {
        $this->loginAs(['ROLE_ADMIN']);

        $reservation = $this->createReservation();

        $this->client->request(
            'POST',
            '/reservation/admin/'.$reservation->getId().'/refuser'
        );

        $this->assertResponseRedirects(
            '/reservation/admin/'.$reservation->getId()
        );

        $this->entityManager->clear();

        $updated = $this->entityManager
            ->getRepository(CommandeLocation::class)
            ->find($reservation->getId());

        $this->assertSame('annule', $updated->getStatut());
        $this->assertNull($updated->getPropositionDebut());
    }


    private function createReservation(): CommandeLocation
    {
        $user = new User();
        $user
            ->setEmail("admin.reservation.client.".uniqid()."@example.com")
            ->setPassword("password")
            ->setRoles(["ROLE_USER"])
            ->setNom("Blanc")
            ->setPrenom("Sophie")
            ->setTel("0600000007")
            ->setAdresse("8 rue de Lille");

        $reservation = new CommandeLocation();
        $reservation->setUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $reservation;
    }


    private function loginAs(array $roles): void
    {
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $email = "admin.reservation.login.".uniqid()."@example.com";

        $user = new User();
        $user
            ->setEmail($email)
            ->setNom("Test")
            ->setPrenom("Login")
            ->setTel("0600000000")
            ->setAdresse("1 rue de test")
            ->setRoles($roles);

        $user->setPassword(
            $passwordHasher->hashPassword($user, 'Password123!')
        );

        $entityManager->persist($user);
        $entityManager->flush();

        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form();

        $this->client->submit($form, [
            'email' => $email,
            'password' => 'Password123!',
        ]);
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}