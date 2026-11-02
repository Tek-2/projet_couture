<?php

namespace App\Tests\Controller;

use App\Entity\Entretien;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EntretienControllerTest extends WebTestCase
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
        $this->client->request('GET', '/entretien');

        $this->assertResponseRedirects();
    }


    public function testEntretienIndexPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/entretien');

        $this->assertResponseIsSuccessful();
    }


    public function testEntretienNewPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/entretien/new');

        $this->assertResponseIsSuccessful();
    }


    public function testEntretienShowPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $entretien = $this->createEntretien();

        $this->client->request('GET', '/entretien/'.$entretien->getId());

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('body', $entretien->getLavage());
    }


    public function testEntretienEditPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $entretien = $this->createEntretien();

        $this->client->request('GET', '/entretien/'.$entretien->getId().'/edit');

        $this->assertResponseIsSuccessful();
    }


    public function testCreateEntretienWithPostRequest(): void
    {
        $this->loginAsAdmin();

        $crawler = $this->client->request('GET', '/entretien/new');

        $token = $crawler
            ->filter('input[name="entretien[_token]"]')
            ->attr('value');


        $this->client->request('POST', '/entretien/new', [
                'entretien' => [
                    'lavage' => 'TEST_30°C',
                    'nettoyage_sec' => 'Non',
                    'repassage' => '110°C',
                    'description' => 'Lavage délicat',
                    '_token' => $token,
                ]
            ]
        );


        $this->assertResponseRedirects('/entretien', 303);
    }


    public function testDeleteEntretien(): void
    {
        $this->loginAsAdmin();

        $entretien = $this->createEntretien();


        $crawler = $this->client->request(
            'GET',
            '/entretien/'.$entretien->getId()
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
                    'delete'.$entretien->getId()
                )
                ->getValue();



        $this->client->request(
            'POST',
            '/entretien/'.$entretien->getId(),
            [
                '_token' => $token
            ]
        );


        $this->assertResponseRedirects('/entretien', 303);
    }

    private function createEntretien(): Entretien
    {
        $entretien = new Entretien();

        $entretien
            ->setLavage('TEST_30°C')
            ->setNettoyageSec('Non')
            ->setRepassage('110°C')
            ->setDescription('Lavage délicat');


        $this->entityManager->persist($entretien);

        $this->entityManager->flush();


        return $entretien;
    }


    private function loginAsAdmin(): void
    {
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $email = "entretien.admin.".uniqid()."@example.com";

        $user = new User();
        $user
            ->setEmail($email)
            ->setNom("Admin")
            ->setPrenom("Entretien")
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