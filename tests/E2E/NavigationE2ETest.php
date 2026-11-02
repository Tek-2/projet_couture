<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class NavigationE2ETest extends PantherTestCase 
{
    public function testProductPageLoadsInBrowser(): void
{
    $client = static::createPantherClient([
        'external_base_uri' => 'http://127.0.0.1:8000',
    ]);

    $crawler = $client->request('GET', '/dressing');

    $this->assertPageTitleContains('');
    $this->assertSelectorExists('body');
    $this->assertStringContainsString(
        'Dressing',
        $crawler->filter('body')->text()
    );
}
}