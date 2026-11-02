<?php

namespace App\Tests\Unit;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{

    public function testUserCanBeCreated():void {
        $user=new User();
        $this->assertInstanceOf(User::class,$user);
    }

    public function testUserPropertiesCanBeSetAndRead():void {
        $user=new User();
        $user->setEmail('johndoe@gmail.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('password123');
        $user->setNom('Doe');
        $user->setPrenom('John');
        $user->setTel('0612345678');
        $user->setAdresse('12 rue de la Paix, 69003 Lyon');
        $this->assertSame('johndoe@gmail.com', $user->getEmail());
        $this->assertSame('Doe', $user->getNom());
        $this->assertSame('John', $user->getPrenom());
        $this->assertSame('0612345678', $user->getTel());
        $this->assertSame('12 rue de la Paix, 69003 Lyon', $user->getAdresse());
        $this->assertSame(['ROLE_USER'], $user->getRoles());
        $this->assertSame('password123', $user->getPassword());
    }
}