<?php

namespace App\Tests\Controller;

use App\Entity\Couleur;
use App\Entity\Entretien;
use App\Entity\ModeleLocation;
use App\Entity\StockLocation;
use App\Entity\TypeLocation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StockLocationControllerTest extends WebTestCase
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
        $this->client->request('GET', '/stock_location');

        $this->assertResponseRedirects();
    }


    public function testIndexPageIsSuccessfulForAdmin(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/stock_location');

        $this->assertResponseIsSuccessful();
    }


    public function testNewPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/stock_location/new');

        $this->assertResponseIsSuccessful();
    }


    public function testCreateStockLocationWithPostRequest(): void
    {
        $this->loginAsAdmin();

        $couleur = $this->createCouleur();
        $modele = $this->createModeleLocation();

        $crawler = $this->client->request('GET', '/stock_location/new');

        $token = $crawler
            ->filter('input[name="stock_location[_token]"]')
            ->attr('value');

        $this->client->request(
            'POST',
            '/stock_location/new',
            [
                'stock_location' => [
                    'taille' => 'L',
                    'quantite' => '4',
                    'couleur' => $couleur->getId(),
                    'modeleLocation' => $modele->getId(),
                    '_token' => $token,
                ]
            ]
        );

        $this->assertResponseRedirects('/stock_location', 303);
    }


    public function testShowPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $stock = $this->createStockLocation();

        $this->client->request('GET', '/stock_location/'.$stock->getId());

        $this->assertResponseIsSuccessful();
    }


    public function testEditPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $stock = $this->createStockLocation();

        $this->client->request('GET', '/stock_location/'.$stock->getId().'/edit');

        $this->assertResponseIsSuccessful();
    }


    public function testDeleteStockLocation(): void
    {
        $this->loginAsAdmin();

        $stock = $this->createStockLocation();

        $crawler = $this->client->request('GET', '/stock_location/'.$stock->getId());

        $token = $crawler
            ->filter('input[name="_token"]')
            ->count()
            ? $crawler
                ->filter('input[name="_token"]')
                ->attr('value')
            : static::getContainer()
                ->get('security.csrf.token_manager')
                ->getToken('delete'.$stock->getId())
                ->getValue();

        $this->client->request(
            'POST',
            '/stock_location/'.$stock->getId(),
            ['_token' => $token]
        );

        $this->assertResponseRedirects('/stock_location', 303);
    }


    private function createCouleur(): Couleur
    {
        $couleur = new Couleur();
        $couleur
            ->setNomCouleur("Rouge ".uniqid())
            ->setImage("rouge.jpg");

        $this->entityManager->persist($couleur);
        $this->entityManager->flush();

        return $couleur;
    }


    private function createModeleLocation(): ModeleLocation
    {
        $entretien = new Entretien();
        $entretien
            ->setLavage("30°C max")
            ->setNettoyageSec("non")
            ->setRepassage("110°C max");

        $type = new TypeLocation();
        $type->setLibelle("Robes ".uniqid());

        $modele = new ModeleLocation();
        $modele
            ->setLibelle("Robe stock ".uniqid())
            ->setPrixLocation("59.90")
            ->setTissu("Soie")
            ->setSilhouette("Ajustée")
            ->setLongueur("Longue")
            ->setLongueurManches("Sans manches")
            ->setFormeCol("Bateau")
            ->setEntretien($entretien)
            ->setType($type);

        $this->entityManager->persist($entretien);
        $this->entityManager->persist($type);
        $this->entityManager->persist($modele);
        $this->entityManager->flush();

        return $modele;
    }


    private function createStockLocation(): StockLocation
    {
        $couleur = $this->createCouleur();
        $modele = $this->createModeleLocation();

        $stock = new StockLocation();
        $stock
            ->setTaille("M")
            ->setQuantite(3)
            ->setCouleur($couleur)
            ->setModeleLocation($modele);

        $this->entityManager->persist($stock);
        $this->entityManager->flush();

        return $stock;
    }


    private function loginAsAdmin(): void
    {
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $email = "stock.admin.".uniqid()."@example.com";

        $user = new User();
        $user
            ->setEmail($email)
            ->setNom("Admin")
            ->setPrenom("Stock")
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