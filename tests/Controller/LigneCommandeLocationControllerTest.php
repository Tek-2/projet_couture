<?php

namespace App\Tests\Controller;

use App\Entity\CommandeLocation;
use App\Entity\Couleur;
use App\Entity\Entretien;
use App\Entity\LigneCommandeLocation;
use App\Entity\ModeleLocation;
use App\Entity\StockLocation;
use App\Entity\TypeLocation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LigneCommandeLocationControllerTest extends WebTestCase
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
        $this->client->request('GET', '/ligne_commande_location');

        $this->assertResponseRedirects();
    }


    public function testIndexPageIsSuccessfulForAdmin(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/ligne_commande_location');

        $this->assertResponseIsSuccessful();
    }


    public function testNewPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/ligne_commande_location/new');

        $this->assertResponseIsSuccessful();
    }


    public function testCreateLigneCommandeLocationWithPostRequest(): void
    {
        $this->loginAsAdmin();

        $stock = $this->createStockLocation();
        $commande = $this->createCommandeLocation();

        $crawler = $this->client->request('GET', '/ligne_commande_location/new');

        $token = $crawler
            ->filter('input[name="ligne_commande_location[_token]"]')
            ->attr('value');

        $this->client->request(
            'POST',
            '/ligne_commande_location/new',
            [
                'ligne_commande_location' => [
                    'prix' => '59.90',
                    'quantite' => '2',
                    'commandeloc' => $commande->getId(),
                    'stockloc' => $stock->getId(),
                    '_token' => $token,
                ]
            ]
        );

        $this->assertResponseRedirects('/ligne_commande_location', 303);
    }


    public function testShowPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $ligne = $this->createLigneCommandeLocation();

        $this->client->request('GET', '/ligne_commande_location/'.$ligne->getId());

        $this->assertResponseIsSuccessful();
    }


    public function testEditPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $ligne = $this->createLigneCommandeLocation();

        $this->client->request('GET', '/ligne_commande_location/'.$ligne->getId().'/edit');

        $this->assertResponseIsSuccessful();
    }


    public function testDeleteLigneCommandeLocation(): void
    {
        $this->loginAsAdmin();

        $ligne = $this->createLigneCommandeLocation();

        $crawler = $this->client->request('GET', '/ligne_commande_location/'.$ligne->getId());

        $token = $crawler
            ->filter('input[name="_token"]')
            ->count()
            ? $crawler
                ->filter('input[name="_token"]')
                ->attr('value')
            : static::getContainer()
                ->get('security.csrf.token_manager')
                ->getToken('delete'.$ligne->getId())
                ->getValue();

        $this->client->request(
            'POST',
            '/ligne_commande_location/'.$ligne->getId(),
            ['_token' => $token]
        );

        $this->assertResponseRedirects('/ligne_commande_location', 303);
    }


    private function createStockLocation(): StockLocation
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
            ->setLibelle("Robe ligne ".uniqid())
            ->setPrixLocation("59.90")
            ->setTissu("Soie")
            ->setSilhouette("Ajustée")
            ->setLongueur("Longue")
            ->setLongueurManches("Sans manches")
            ->setFormeCol("Bateau")
            ->setEntretien($entretien)
            ->setType($type);

        $couleur = new Couleur();
        $couleur
            ->setNomCouleur("Rouge ".uniqid())
            ->setImage("rouge.jpg");

        $stock = new StockLocation();
        $stock
            ->setTaille("M")
            ->setQuantite(3)
            ->setCouleur($couleur)
            ->setModeleLocation($modele);

        $this->entityManager->persist($entretien);
        $this->entityManager->persist($type);
        $this->entityManager->persist($modele);
        $this->entityManager->persist($couleur);
        $this->entityManager->persist($stock);
        $this->entityManager->flush();

        return $stock;
    }


    private function createCommandeLocation(): CommandeLocation
    {
        $user = new User();
        $user
            ->setEmail("ligne.client.".uniqid()."@example.com")
            ->setPassword("password")
            ->setRoles(["ROLE_USER"])
            ->setNom("Roux")
            ->setPrenom("Paul")
            ->setTel("0600000004")
            ->setAdresse("5 rue de Nantes");

        $commande = new CommandeLocation();
        $commande->setUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($commande);
        $this->entityManager->flush();

        return $commande;
    }


    private function createLigneCommandeLocation(): LigneCommandeLocation
    {
        $stock = $this->createStockLocation();
        $commande = $this->createCommandeLocation();

        $ligne = new LigneCommandeLocation();
        $ligne
            ->setCommandeloc($commande)
            ->setStockloc($stock)
            ->setPrix("59.90")
            ->setQuantite(1);

        $this->entityManager->persist($ligne);
        $this->entityManager->flush();

        return $ligne;
    }


    private function loginAsAdmin(): void
    {
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $email = "ligne.admin.".uniqid()."@example.com";

        $user = new User();
        $user
            ->setEmail($email)
            ->setNom("Admin")
            ->setPrenom("Ligne")
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