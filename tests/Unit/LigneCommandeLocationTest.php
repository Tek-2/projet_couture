<?php

namespace App\Tests\Unit;

use App\Entity\LigneCommandeLocation;
use App\Entity\CommandeLocation;
use App\Entity\StockLocation;
use PHPUnit\Framework\TestCase;

class LigneCommandeLocationTest extends TestCase
{

    public function testLigneCommandeLocationCanBeCreated(): void
    {
        $ligne = new LigneCommandeLocation();
        $this->assertInstanceOf(LigneCommandeLocation::class, $ligne);
    }

    public function testLigneCommandeLocationPropertiesCanBeSetAndRead(): void
    {
        $ligne = new LigneCommandeLocation();
        $ligne->setPrix('49.90');
        $ligne->setQuantite(2);

        $this->assertSame('49.90', $ligne->getPrix());
        $this->assertSame(2, $ligne->getQuantite());
    }

    public function testLigneCommandeLocationCanHaveACommandeLocation(): void
    {
        $ligne = new LigneCommandeLocation();
        $commande = new CommandeLocation();
        $commande->setStatut('valide');

        $ligne->setCommandeloc($commande);

        $this->assertSame($commande, $ligne->getCommandeloc());
        $this->assertSame('valide', $ligne->getCommandeloc()->getStatut());
    }

    public function testLigneCommandeLocationCanHaveAStockLocation(): void
    {
        $ligne = new LigneCommandeLocation();
        $stock = new StockLocation();
        $stock->setTaille('M');
        $stock->setQuantite(5);

        $ligne->setStockloc($stock);

        $this->assertSame($stock, $ligne->getStockloc());
        $this->assertSame('M', $ligne->getStockloc()->getTaille());
        $this->assertSame(5, $ligne->getStockloc()->getQuantite());
    }
}