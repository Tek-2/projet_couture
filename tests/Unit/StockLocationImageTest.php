<?php

namespace App\Tests\Unit;

use App\Entity\StockLocationImage;
use App\Entity\ModeleLocation;
use PHPUnit\Framework\TestCase;

class StockLocationImageTest extends TestCase
{

    public function testStockLocationImageCanBeCreated(): void
    {
        $image = new StockLocationImage();
        $this->assertInstanceOf(StockLocationImage::class, $image);
    }

    public function testStockLocationImagePropertiesCanBeSetAndRead(): void
    {
        $image = new StockLocationImage();
        $image->setUrl('https://example.com/images/robe-rouge.jpg');
        $image->setOrdre(1);

        $this->assertSame('https://example.com/images/robe-rouge.jpg', $image->getUrl());
        $this->assertSame(1, $image->getOrdre());
    }

    public function testStockLocationImageOrdreCanBeNull(): void
    {
        $image = new StockLocationImage();
        $image->setUrl('https://example.com/images/robe-rouge.jpg');
        $image->setOrdre(null);

        $this->assertNull($image->getOrdre());
    }

    public function testStockLocationImageCanHaveAModeleLocation(): void
    {
        $image = new StockLocationImage();
        $modele = new ModeleLocation();
        $modele->setLibelle('Robe de soirée');

        $image->setModeleLocation($modele);

        $this->assertSame($modele, $image->getModeleLocation());
        $this->assertSame('Robe de soirée', $image->getModeleLocation()->getLibelle());
    }
}