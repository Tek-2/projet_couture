<?php

namespace App\Tests\Controller;

use App\Entity\TypeLocation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TypeLocationControllerTest extends WebTestCase
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
        $this->client->request('GET', '/type_location');

        $this->assertResponseRedirects();
    }


    public function testTypeLocationIndexPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/type_location');

        $this->assertResponseIsSuccessful();
    }


    public function testTypeLocationNewPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/type_location/new');

        $this->assertResponseIsSuccessful();
    }


    public function testTypeLocationShowPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $typeLocation = $this->createTypeLocation();

        $this->client->request('GET', '/type_location/'.$typeLocation->getId());

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('body', $typeLocation->getLibelle());
    }


    public function testTypeLocationEditPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $typeLocation = $this->createTypeLocation();

        $this->client->request('GET', '/type_location/'.$typeLocation->getId().'/edit');

        $this->assertResponseIsSuccessful();
    }


    public function testCreateTypeLocationWithPostRequest(): void
    {
        $this->loginAsAdmin();

        $crawler = $this->client->request('GET', '/type_location/new');

        $token = $crawler
            ->filter('input[name="type_location[_token]"]')
            ->attr('value');


        $this->client->request('POST', '/type_location/new', [
                'type_location' => [
                    'libelle' => 'Type test création',
                    '_token' => $token,
                ]
            ]
        );


        $this->assertResponseRedirects('/type_location', 303);
    }


    public function testDeleteTypeLocation(): void
    {
        $this->loginAsAdmin();

        $typeLocation = $this->createTypeLocation();


        $crawler = $this->client->request(
            'GET',
            '/type_location/'.$typeLocation->getId()
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
                    'delete'.$typeLocation->getId()
                )
                ->getValue();



        $this->client->request(
            'POST',
            '/type_location/'.$typeLocation->getId(),
            [
                '_token' => $token
            ]
        );


        $this->assertResponseRedirects('/type_location', 303);
    }

    private function createTypeLocation(): TypeLocation
    {

        $typeLocation = new TypeLocation();


        $typeLocation
            ->setLibelle('Soirée '.uniqid());


        $this->entityManager->persist($typeLocation);

        $this->entityManager->flush();


        return $typeLocation;
    }


    private function loginAsAdmin(): void
    {
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $email = "type.admin.".uniqid()."@example.com";

        $user = new User();
        $user
            ->setEmail($email)
            ->setNom("Admin")
            ->setPrenom("Type")
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