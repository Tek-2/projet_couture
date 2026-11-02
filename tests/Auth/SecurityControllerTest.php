<?php

namespace App\Tests\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageIsSuccessful():void
    {
        $client= static::createClient();
        $client->request('GET','/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testUserCanLoginWithValidCredentials():void
    {
        $client= static::createClient();
        $this->createUser('login_'.uniqid().'@example.com','Password123!');
        $email= $this->lastCreatedEmail;
        $crawler= $client->request('GET','/login');
        $form= $crawler->selectButton('Sign in')->form();
        $client->submit($form, [
        'email' =>$email,
        'password' =>'Password123!',
        ]);
        $this->assertResponseRedirects();
    }

    public function testUserCannotLoginWithInvalidPassword():void
    {
        $client= static::createClient();
        $this->createUser('wrong_'.uniqid().'@example.com','Password123!');
        $email= $this->lastCreatedEmail;
        $crawler= $client->request('GET','/login');
        $form= $crawler->selectButton('Sign in')->form();
        $client->submit($form, [
        'email' =>$email,
        'password' =>'WrongPassword',
        ]);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    private string $lastCreatedEmail;

    private function createUser(string $email, string $plainPassword): void
    {
        $container= static::getContainer();
        $entityManager= $container->get(EntityManagerInterface::class);
        $passwordHasher= $container->get(UserPasswordHasherInterface::class);
        $user=new User();
        $user->setEmail($email);
        $user->setNom('NomAuth');
        $user->setPrenom('PrenomAuth');
        $user->setTel('1234567890');
        $user->setAdresse('123 Main St');
        $user->setRoles(['ROLE_USER']);

        $hashedPassword= $passwordHasher->hashPassword($user,$plainPassword);
        $user->setPassword($hashedPassword);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->lastCreatedEmail=$email;
    }
}