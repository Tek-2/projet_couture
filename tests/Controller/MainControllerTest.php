<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    private KernelBrowser $client;


    protected function setUp(): void
    {
        $this->client = static::createClient();
    }


    public function testHomepageIsSuccessful(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }
}