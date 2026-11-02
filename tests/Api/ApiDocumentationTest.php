<?php
namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ApiDocumentationTest extends ApiTestCase
{
    public function testApiDocumentationIsAvailable(): void
    {
        static::createClient()->request('GET','/api');
        $this->assertResponseIsSuccessful();
    }
    
    public function testApiJsonLdDocumentationIsSuccessful(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/docs.jsonld');

        $this->assertResponseIsSuccessful();
    }
}