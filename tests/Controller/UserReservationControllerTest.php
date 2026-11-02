<?php

namespace App\Tests\Controller;

use App\Entity\CommandeLocation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserReservationControllerTest extends WebTestCase
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
        $this->client->request('GET', '/mes-reservations');

        $this->assertResponseRedirects();
    }


    public function testIndexPageIsSuccessfulForLoggedInUser(): void
    {
        $this->loginAsUser();

        $this->client->request('GET', '/mes-reservations');

        $this->assertResponseIsSuccessful();
    }


    public function testShowPageIsSuccessfulForOwnReservation(): void
    {
        $user = $this->loginAsUser();

        $reservation = $this->createReservationForUser($user);

        $this->client->request('GET', '/mes-reservations/'.$reservation->getId());

        $this->assertResponseIsSuccessful();
    }


    public function testShowPageIsForbiddenForSomeoneElsesReservation(): void
    {
        $this->loginAsUser();

        $otherUser = $this->createUser();
        $reservation = $this->createReservationForUser($otherUser);

        $this->client->request('GET', '/mes-reservations/'.$reservation->getId());

        $this->assertResponseStatusCodeSame(403);
    }


    public function testUserCanAccepterUnePropositionAdmin(): void
    {
        $user = $this->loginAsUser();

        $reservation = $this->createReservationForUser($user);
        $reservation->setPropositionDebut(new \DateTimeImmutable('2026-09-15 10:00'));
        $reservation->setPropositionFin(new \DateTimeImmutable('2026-09-15 10:30'));
        $this->entityManager->flush();

        $this->client->request(
            'POST',
            '/mes-reservations/'.$reservation->getId().'/accepter'
        );

        $this->assertResponseRedirects(
            '/mes-reservations/'.$reservation->getId()
        );

        $this->entityManager->clear();

        $updated = $this->entityManager
            ->getRepository(CommandeLocation::class)
            ->find($reservation->getId());

        $this->assertSame('valide', $updated->getStatut());
        $this->assertNull($updated->getPropositionDebut());
    }


    public function testUserCannotAccepterUnePropositionInexistante(): void
    {
        $user = $this->loginAsUser();

        $reservation = $this->createReservationForUser($user);

        $this->client->request(
            'POST',
            '/mes-reservations/'.$reservation->getId().'/accepter'
        );

        $this->assertResponseRedirects(
            '/mes-reservations/'.$reservation->getId()
        );

        $this->entityManager->clear();

        $updated = $this->entityManager
            ->getRepository(CommandeLocation::class)
            ->find($reservation->getId());

        $this->assertNotSame('valide', $updated->getStatut());
    }


    public function testUserCannotAccepterSomeoneElsesReservation(): void
    {
        $this->loginAsUser();

        $otherUser = $this->createUser();
        $reservation = $this->createReservationForUser($otherUser);

        $this->client->request(
            'POST',
            '/mes-reservations/'.$reservation->getId().'/accepter'
        );

        $this->assertResponseStatusCodeSame(403);
    }


    public function testUserCanRefuserUnePropositionAdmin(): void
    {
        $user = $this->loginAsUser();

        $reservation = $this->createReservationForUser($user);
        $reservation->setPropositionDebut(new \DateTimeImmutable('2026-09-15 10:00'));
        $reservation->setPropositionFin(new \DateTimeImmutable('2026-09-15 10:30'));
        $this->entityManager->flush();

        $this->client->request(
            'POST',
            '/mes-reservations/'.$reservation->getId().'/refuser'
        );

        $this->assertResponseRedirects(
            '/mes-reservations/'.$reservation->getId()
        );

        $this->entityManager->clear();

        $updated = $this->entityManager
            ->getRepository(CommandeLocation::class)
            ->find($reservation->getId());

        $this->assertSame('refuse_client', $updated->getStatut());
        $this->assertNull($updated->getPropositionDebut());
    }


    private function createReservationForUser(User $user): CommandeLocation
    {
        // L'objet User peut être détaché si une requête HTTP a eu lieu
        // depuis sa création (l'EntityManager est réinitialisé entre
        // les requêtes en environnement de test). On le re-récupère via
        // son id, qui reste valide, pour repartir d'une instance gérée.
        $user = $this->entityManager
            ->getRepository(User::class)
            ->find($user->getId());

        $reservation = new CommandeLocation();
        $reservation->setUser($user);

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $reservation;
    }


    private function createUser(): User
    {
        $user = new User();
        $user
            ->setEmail("user.reservation.other.".uniqid()."@example.com")
            ->setPassword("password")
            ->setRoles(["ROLE_USER"])
            ->setNom("Autre")
            ->setPrenom("Personne")
            ->setTel("0600000008")
            ->setAdresse("9 rue de Reims");

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }


    private function loginAsUser(): User
    {
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $email = "user.reservation.login.".uniqid()."@example.com";

        $user = new User();
        $user
            ->setEmail($email)
            ->setNom("Test")
            ->setPrenom("Login")
            ->setTel("0600000000")
            ->setAdresse("1 rue de test")
            ->setRoles(['ROLE_USER']);

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

        return $user;
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}