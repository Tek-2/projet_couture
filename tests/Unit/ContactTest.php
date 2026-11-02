<?php

namespace App\Tests\Unit;

use App\Entity\Contact;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{

    public function testContactCanBeCreated():void {
        $contact=new Contact();
        $this->assertInstanceOf(Contact::class,$contact);
    }

    public function testContactPropertiesCanBeSetAndRead():void {
        $contact=new Contact();
        $contact->setSujet('Test');
        $contact->setMessage('Bonjour, ceci est un test.');
        $contact->setDate(new \DateTime());
        $this->assertSame('Test', $contact->getSujet());
        $this->assertSame('Bonjour, ceci est un test.', $contact->getMessage());
        $this->assertInstanceOf(\DateTime::class, $contact->getDate());
    }

    public function testContactCanHaveUser():void
    {
        $contact=new Contact();
        $user=new User();
        $contact->setIdUser($user);
        $this->assertSame($user, $contact->getIdUser());
    }

}