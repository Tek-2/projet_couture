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

class DressingControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);
    }


    public function testDressingIndexPageIsSuccessful(): void
    {
        $this->client->request('GET', '/dressing');

        $this->assertResponseIsSuccessful();
    }


    public function testDressingShowPageIsSuccessful(): void
    {
        $stock = $this->createStockLocation();

        $this->client->request(
            'GET',
            '/dressing/'.$stock->getModeleLocation()->getId()
        );

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            'body',
            $stock->getModeleLocation()->getLibelle()
        );
    }


    public function testDressingShowPageReturns404ForUnknownModele(): void
    {
        $this->client->request('GET', '/dressing/999999');

        $this->assertResponseStatusCodeSame(404);
    }


    public function testCreateReservationPageRedirectsAnonymousToLogin(): void
    {
        $this->client->request('GET', '/dressing/reservation/create');

        $this->assertResponseRedirects();
    }


    public function testCreateReservationPageIsSuccessfulForLoggedInUser(): void
    {
        $this->loginAsUser();

        $this->client->request('GET', '/dressing/reservation/create');

        $this->assertResponseIsSuccessful();
    }


    public function testCreateReservationWithPostRequestCreatesCommandeAndLignes(): void
    {
        $user = $this->loginAsUser();

        $stock = $this->createStockLocation();

        // On ajoute le modèle au showroom (session) avant de réserver
        $this->client->request('GET', '/showroom/add/'.$stock->getId());

        $this->client->request(
            'POST',
            '/dressing/reservation/create',
            [
                'creneaux_client' => ['2026-09-15|10:00'],
                'commentaire' => 'Merci de me recontacter le matin',
            ]
        );

        $this->assertResponseRedirects('/dressing/reservation/success');

        $commande = $this->entityManager
            ->getRepository(CommandeLocation::class)
            ->findOneBy(['user' => $user]);

        $this->assertNotNull($commande);
        $this->assertSame('en_attente', $commande->getStatut());
        $this->assertSame(
            'Merci de me recontacter le matin',
            $commande->getCommentaire()
        );

        $ligne = $this->entityManager
            ->getRepository(LigneCommandeLocation::class)
            ->findOneBy(['commandeloc' => $commande]);

        $this->assertNotNull($ligne);
        $this->assertSame($stock->getId(), $ligne->getStockloc()->getId());
        $this->assertSame(1, $ligne->getQuantite());
        $this->assertSame(
            $stock->getModeleLocation()->getPrixLocation(),
            $ligne->getPrix()
        );
    }


    public function testReservationSuccessPageIsSuccessful(): void
    {
        $this->client->request('GET', '/dressing/reservation/success');

        $this->assertResponseIsSuccessful();
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
            ->setLibelle("Robe dressing ".uniqid())
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


    private function loginAsUser(): User
    {
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $email = "dressing.user.".uniqid()."@example.com";

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