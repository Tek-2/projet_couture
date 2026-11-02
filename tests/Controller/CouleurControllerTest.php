<?php

namespace App\Tests\Controller;

use App\Entity\Couleur;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CouleurControllerTest extends WebTestCase
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
        $this->client->request('GET', '/couleur');

        $this->assertResponseRedirects();
    }


    public function testCouleurIndexPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/couleur');

        $this->assertResponseIsSuccessful();
    }


    public function testCouleurNewPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/couleur/new');

        $this->assertResponseIsSuccessful();
    }


    public function testCouleurShowPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $couleur = $this->createCouleur();

        $this->client->request('GET', '/couleur/'.$couleur->getId());

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('body', $couleur->getNomCouleur());
    }


    public function testCouleurEditPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $couleur = $this->createCouleur();

        $this->client->request('GET', '/couleur/'.$couleur->getId().'/edit');

        $this->assertResponseIsSuccessful();
    }


    public function testCreateCouleurWithPostRequest(): void
    {
        $this->loginAsAdmin();

        $crawler = $this->client->request('GET', '/couleur/new');

        $token = $crawler
            ->filter('input[name="couleur[_token]"]')
            ->attr('value');


        $this->client->request('POST', '/couleur/new', [
                'couleur' => [
                    'nom_couleur' => 'Couleur test',
                    'image' => 'image.jpg',
                    '_token' => $token,
                ]
            ]
        );


        $this->assertResponseRedirects('/couleur', 303);
    }


    public function testDeleteCouleur(): void
    {
        $this->loginAsAdmin();

        $couleur = $this->createCouleur();


        $crawler = $this->client->request(
            'GET',
            '/couleur/'.$couleur->getId()
        );


        $token = $crawler
            ->filter('input[name="_token"]')
            ->count()
            ? $crawler
                ->filter('input[name="_token"]')
                ->attr('value')
            : static::getContainer()
                ->get('security.csrf.token_manager')
                ->getToken(
                    'delete'.$couleur->getId()
                )
                ->getValue();



        $this->client->request(
            'POST',
            '/couleur/'.$couleur->getId(),
            [
                '_token' => $token
            ]
        );


        $this->assertResponseRedirects('/couleur', 303);
    }

    private function createCouleur(): Couleur
    {

        $couleur = new Couleur();


        $couleur
            ->setNomCouleur('Couleur test '.uniqid())
            ->setImage('image.jpg');


        $this->entityManager->persist($couleur);

        $this->entityManager->flush();


        return $couleur;
    }


    private function loginAsAdmin(): void
    {
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $email = "couleur.admin.".uniqid()."@example.com";

        $user = new User();
        $user
            ->setEmail($email)
            ->setNom("Admin")
            ->setPrenom("Couleur")
            ->setTel("0600000000")
            ->setAdresse("1 rue de test")
            ->setRoles(['ROLE_ADMIN']);

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