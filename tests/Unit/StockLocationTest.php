<?php

namespace App\Tests\Unit;

use App\Entity\Stocklocation;
use App\Entity\Couleur;
use App\Entity\Modelelocation;
use PHPUnit\Framework\TestCase;

class StocklocationTest extends TestCase
{

    public function testStocklocationCanBeCreated():void {
        $stocklocation=new Stocklocation();
        $this->assertInstanceOf(Stocklocation::class,$stocklocation);
    }

    public function testStocklocationPropertiesCanBeSetAndRead():void {
        $stocklocation=new Stocklocation();
        $stocklocation->setTaille('S');
        $stocklocation->setQuantite(17);
        $this->assertEquals('S', $stocklocation->getTaille());
        $this->assertSame(17, $stocklocation->getQuantite());
    }

    public function testStocklocationCanHaveColor():void
    {
        $stocklocation=new Stocklocation();
        $color=new Couleur();
        $color->setNomCouleur('Blanc');
        $stocklocation->setCouleur($color);
        $this->assertSame($color, $stocklocation->getCouleur());
        $this->assertSame('Blanc', $stocklocation->getCouleur()->getNomCouleur());
    }

    public function testStocklocationCanHaveAModeleLocation():void
    {
        $stocklocation=new Stocklocation();
        $modelelocation=new Modelelocation();
        $modelelocation->setLibelle('Chemisier');
        $modelelocation->setPrixLocation(19.99);
        $modelelocation->setTissu('Coton');
        $modelelocation->setSilhouette('Large');
        $modelelocation->setLongueur('court');
        $modelelocation->setLongueurManches('longues'); 
        $modelelocation->setFormeCol('Claudine');
        $modelelocation->setDescription('Chemisier blanc en coton');
        $stocklocation->setModeleLocation($modelelocation);
        $this->assertSame($modelelocation, $stocklocation->getModeleLocation());
        $this->assertSame('Chemisier', $stocklocation->getModeleLocation()->getLibelle());
        $this->assertEquals(19.99, $stocklocation->getModeleLocation()->getPrixLocation());
        $this->assertSame('Coton', $stocklocation->getModeleLocation()->getTissu());
        $this->assertSame('Large', $stocklocation->getModeleLocation()->getSilhouette());
        $this->assertSame('court', $stocklocation->getModeleLocation()->getLongueur());
        $this->assertSame('longues', $stocklocation->getModeleLocation()->getLongueurManches());
        $this->assertSame('Claudine', $stocklocation->getModeleLocation()->getFormeCol());
        $this->assertSame('Chemisier blanc en coton', $stocklocation->getModeleLocation()->getDescription());
    }
}