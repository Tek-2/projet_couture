<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends WebTestCase
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
        $this->client->request('GET', '/user');

        $this->assertResponseRedirects();
    }


    public function testIndexPageIsSuccessfulForAdmin(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/user');

        $this->assertResponseIsSuccessful();
    }


    public function testNewPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/user/new');

        $this->assertResponseIsSuccessful();
    }


    public function testCreateUserWithPostRequest(): void
    {
        $this->loginAsAdmin();

        $crawler = $this->client->request('GET', '/user/new');

        $token = $crawler
            ->filter('input[name="user[_token]"]')
            ->attr('value');

        $this->client->request(
            'POST',
            '/user/new',
            [
                'user' => [
                    'email' => 'nouvel.utilisateur.'.uniqid().'@example.com',
                    'password' => 'Password123!',
                    'nom' => 'Nouveau',
                    'prenom' => 'Utilisateur',
                    'tel' => '0600000005',
                    'adresse' => '6 rue de Toulouse',
                    '_token' => $token,
                ]
            ]
        );

        $this->assertResponseRedirects('/user', 303);
    }


    public function testShowPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $user = $this->createUser();

        $this->client->request('GET', '/user/'.$user->getId());

        $this->assertResponseIsSuccessful();
    }


    public function testEditPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $user = $this->createUser();

        $this->client->request('GET', '/user/'.$user->getId().'/edit');

        $this->assertResponseIsSuccessful();
    }


    public function testDeleteUser(): void
    {
        $this->loginAsAdmin();

        $user = $this->createUser();

        $crawler = $this->client->request('GET', '/user/'.$user->getId());

        $token = $crawler
            ->filter('input[name="_token"]')
            ->count()
            ? $crawler
                ->filter('input[name="_token"]')
                ->attr('value')
            : static::getContainer()
                ->get('security.csrf.token_manager')
                ->getToken('delete'.$user->getId())
                ->getValue();

        $this->client->request(
            'POST',
            '/user/'.$user->getId(),
            ['_token' => $token]
        );

        $this->assertResponseRedirects('/user', 303);
    }


    private function createUser(): User
    {
        $user = new User();
        $user
            ->setEmail("user.controller.".uniqid()."@example.com")
            ->setPassword("password")
            ->setRoles(["ROLE_USER"])
            ->setNom("Simon")
            ->setPrenom("Claire")
            ->setTel("0600000006")
            ->setAdresse("7 rue de Bordeaux");

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }


    private function loginAsAdmin(): void
    {
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $email = "user.admin.".uniqid()."@example.com";

        $admin = new User();
        $admin
            ->setEmail($email)
            ->setNom("Admin")
            ->setPrenom("User")
            ->setTel("0600000000")
            ->setAdresse("1 rue de test")
            ->setRoles(['ROLE_ADMIN']);

        $admin->setPassword(
            $passwordHasher->hashPassword($admin, 'Password123!')
        );

        $entityManager->persist($admin);
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