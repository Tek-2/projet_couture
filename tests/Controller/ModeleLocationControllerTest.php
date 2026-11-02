<?php

namespace App\Tests\Controller;

use App\Entity\ModeleLocation;
use App\Entity\Entretien;
use App\Entity\TypeLocation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ModeleLocationControllerTest extends WebTestCase
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
        $this->client->request('GET', '/modele_location');

        $this->assertResponseRedirects();
    }


    public function testModeleLocationIndexPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/modele_location');

        $this->assertResponseIsSuccessful();
    }


    public function testModeleLocationNewPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/modele_location/new');

        $this->assertResponseIsSuccessful();
    }


    public function testModeleLocationShowPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $modeleLocation = $this->createModeleLocation();

        $this->client->request(
            'GET',
            '/modele_location/'.$modeleLocation->getId()
        );

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            'body',
            $modeleLocation->getLibelle()
        );
    }


    public function testModeleLocationEditPageIsSuccessful(): void
    {
        $this->loginAsAdmin();

        $modeleLocation = $this->createModeleLocation();

        $this->client->request(
            'GET',
            '/modele_location/'.$modeleLocation->getId().'/edit'
        );

        $this->assertResponseIsSuccessful();
    }


    public function testCreateModeleLocationWithPostRequest(): void
    {
        $this->loginAsAdmin();

        $entretien = $this->createEntretien();

        $typeLocation = $this->createTypeLocation();


        $crawler = $this->client->request(
            'GET',
            '/modele_location/new'
        );


        $token = $crawler
            ->filter('input[name="modele_location[_token]"]')
            ->attr('value');


        $this->client->request(
            'POST',
            '/modele_location/new',
            [
                'modele_location' => [
                    'libelle' => 'Modèle test création',
                    'prixLocation' => '100.00',
                    'tissu' => 'coton',
                    'silhouette' => 'ample',
                    'longueur' => 'genou',
                    'longueurManches' => 'courtes',
                    'formeCol' => 'rond',
                    'description' => 'Description test',
                    'entretien' => $entretien->getId(),
                    'type' => $typeLocation->getId(),
                    '_token' => $token,
                ]
            ]
        );


        $this->assertResponseRedirects('/modele_location', 303);
    }


    public function testDeleteModeleLocation(): void
    {
        $this->loginAsAdmin();

        $modeleLocation = $this->createModeleLocation();


        $crawler = $this->client->request(
            'GET',
            '/modele_location/'.$modeleLocation->getId()
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
                    'delete'.$modeleLocation->getId()
                )
                ->getValue();



        $this->client->request(
            'POST',
            '/modele_location/'.$modeleLocation->getId(),
            [
                '_token' => $token
            ]
        );


        $this->assertResponseRedirects('/modele_location', 303);
    }



    private function createModeleLocation(): ModeleLocation
    {

        $entretien = $this->createEntretien();

        $typeLocation = $this->createTypeLocation();


        $modeleLocation = new ModeleLocation();


        $modeleLocation
            ->setLibelle('Test Modèle '.uniqid())
            ->setPrixLocation('100.00')
            ->setTissu('coton')
            ->setSilhouette('ample')
            ->setLongueur('genou')
            ->setLongueurManches('courtes')
            ->setFormeCol('rond')
            ->setDescription('test description')
            ->setEntretien($entretien)
            ->setType($typeLocation);



        $this->entityManager->persist($modeleLocation);

        $this->entityManager->flush();


        return $modeleLocation;
    }



    private function createEntretien(): Entretien
    {

        $entretien = new Entretien();


        $entretien
            ->setLavage('Lavage main uniquement')
            ->setNettoyageSec('Non')
            ->setRepassage('110 degrés')
            ->setDescription('Entretien délicat');


        $this->entityManager->persist($entretien);

        $this->entityManager->flush();


        return $entretien;
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

        $email = "modele.admin.".uniqid()."@example.com";

        $user = new User();
        $user
            ->setEmail($email)
            ->setNom("Admin")
            ->setPrenom("Modele")
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