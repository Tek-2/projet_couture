<?php

namespace App\Tests\Controller;

use App\Entity\CommandeLocation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CommandeLocationControllerTest extends WebTestCase
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
        $this->client->request('GET', '/commande_location');

        $this->assertResponseRedirects();
    }


    public function testIndexPageIsSuccessfulForAdmin(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/commande_location');

        $this->assertResponseIsSuccessful();
    }


    public function testIndexIsForbiddenForRegularUser(): void
    {
        $this->loginAsUser();

        $this->client->request('GET', '/commande_location');

        $this->assertResponseStatusCodeSame(403);
    }


    public function testNewPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/commande_location/new');

        $this->assertResponseIsSuccessful();
    }


    public function testCreateCommandeLocationWithPostRequest(): void
    {
        $this->loginAsAdmin();

        $client = $this->createClientUser();

        $crawler = $this->client->request('GET', '/commande_location/new');

        $token = $crawler
            ->filter('input[name="commande_location[_token]"]')
            ->attr('value');

        $this->client->request(
            'POST',
            '/commande_location/new',
            [
                'commande_location' => [
                    'user' => $client->getId(),
                    'statut' => 'en_attente',
                    '_token' => $token,
                ]
            ]
        );

        $this->assertResponseRedirects('/commande_location', 303);
    }


    public function testShowPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $commande = $this->createCommandeLocation();

        $this->client->request('GET', '/commande_location/'.$commande->getId());

        $this->assertResponseIsSuccessful();
    }


    public function testEditPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $commande = $this->createCommandeLocation();

        $this->client->request('GET', '/commande_location/'.$commande->getId().'/edit');

        $this->assertResponseIsSuccessful();
    }


    public function testDeleteCommandeLocation(): void
    {
        $this->loginAsAdmin();

        $commande = $this->createCommandeLocation();

        $crawler = $this->client->request('GET', '/commande_location/'.$commande->getId());

        $token = $crawler
            ->filter('input[name="_token"]')
            ->count()
            ? $crawler
                ->filter('input[name="_token"]')
                ->attr('value')
            : static::getContainer()
                ->get('security.csrf.token_manager')
                ->getToken('delete'.$commande->getId())
                ->getValue();

        $this->client->request(
            'POST',
            '/commande_location/'.$commande->getId(),
            ['_token' => $token]
        );

        $this->assertResponseRedirects('/commande_location', 303);
    }


    private function createClientUser(): User
    {
        $user = new User();
        $user
            ->setEmail("commande.client.".uniqid()."@example.com")
            ->setPassword("password")
            ->setRoles(["ROLE_USER"])
            ->setNom("Petit")
            ->setPrenom("Léa")
            ->setTel("0600000003")
            ->setAdresse("4 rue de Marseille");

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }


    private function createCommandeLocation(): CommandeLocation
    {
        $user = $this->createClientUser();

        $commande = new CommandeLocation();
        $commande->setUser($user);

        $this->entityManager->persist($commande);
        $this->entityManager->flush();

        return $commande;
    }


    private function loginAsAdmin(): void
    {
        $this->loginAs(['ROLE_ADMIN']);
    }


    private function loginAsUser(): void
    {
        $this->loginAs(['ROLE_USER']);
    }


    private function loginAs(array $roles): void
    {
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $email = "commande.controller.".uniqid()."@example.com";

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