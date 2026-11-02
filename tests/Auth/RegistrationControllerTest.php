<?php

namespace App\Tests\Auth;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = static::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->userRepository = $container->get(UserRepository::class);
    }


    public function testRegisterPageIsSuccessful(): void
    {
        $this->client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('form');
        $this->assertSelectorExists('[name="registration_form[nom]"]');
        $this->assertSelectorExists('[name="registration_form[prenom]"]');
        $this->assertSelectorExists('[name="registration_form[email]"]');
        $this->assertSelectorExists('[name="registration_form[plainPassword]"]');
        $this->assertSelectorExists('[name="registration_form[tel]"]');
        $this->assertSelectorExists('[name="registration_form[adresse]"]');
        $this->assertSelectorExists('[name="registration_form[agreeTerms]"]');
    }


    public function testRegisterCreatesNewUser(): void
    {
        $email = 'register' . uniqid() . '@example.com';

        $crawler = $this->client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form([
            'registration_form[nom]' => 'NomTest',
            'registration_form[prenom]' => 'PrenomTest',
            'registration_form[email]' => $email,
            'registration_form[plainPassword]' => 'password123',
            'registration_form[tel]' => '1234567890',
            'registration_form[adresse]' => '123 Main St',
            'registration_form[agreeTerms]' => '1',
        ]);

        $this->client->submit($form);


        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);


        $this->assertNotNull($user);

        $this->assertSame($email, $user->getEmail());
        $this->assertSame('NomTest', $user->getNom());
        $this->assertSame('PrenomTest', $user->getPrenom());
        $this->assertSame('1234567890', $user->getTel());
        $this->assertSame('123 Main St', $user->getAdresse());

        // Le mot de passe doit être hashé
        $this->assertNotSame('password123', $user->getPassword());
    }


    public function testRegisterWithInvalidDataDoesNotCreateUser(): void
    {
        $countBefore = count($this->userRepository->findAll());


        $crawler = $this->client->request('GET', '/register');

        $this->assertResponseIsSuccessful();


        $form = $crawler->filter('form')->form([
            'registration_form[nom]' => '',
            'registration_form[prenom]' => '',
            'registration_form[email]' => '',
            'registration_form[plainPassword]' => '',
            'registration_form[tel]' => '',
            'registration_form[adresse]' => '',
        ]);


        $this->client->submit($form);


        $countAfter = count($this->userRepository->findAll());


        $this->assertSame($countBefore, $countAfter);

        $this->assertSelectorExists('form');
    }


    public function testRegisterWithDuplicateEmailDoesNotCreateSecondUser(): void
    {
        $email = 'duplicate-register' . uniqid() . '@example.com';


        $this->createUserDirectly($email);


        $crawler = $this->client->request('GET', '/register');

        $this->assertResponseIsSuccessful();


        $form = $crawler->filter('form')->form([
            'registration_form[nom]' => 'Duplicate User',
            'registration_form[prenom]' => 'Duplicate User',
            'registration_form[email]' => $email,
            'registration_form[plainPassword]' => 'password123',
            'registration_form[tel]' => '1234567890',
            'registration_form[adresse]' => '123 Main St',
            'registration_form[agreeTerms]' => '1',
        ]);


        $this->client->submit($form);


        $users = $this->userRepository->findBy([
            'email' => $email,
        ]);


        $this->assertCount(1, $users);
    }



    private function createUserDirectly(string $email): void
    {
        $userClass = $this->userRepository->getClassName();

        $user = new $userClass();


        $user
            ->setEmail($email)
            ->setNom('Existing User')
            ->setPrenom('Existing User')
            ->setPassword('hashed_password')
            ->setTel('1234567890')
            ->setAdresse('123 Main St');


        $this->entityManager->persist($user);

        $this->entityManager->flush();
    }
}