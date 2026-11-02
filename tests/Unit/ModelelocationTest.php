<?php

namespace App\Tests\Unit;

use App\Entity\Entretien;
use App\Entity\Modelelocation;
use App\Entity\Typelocation;
use PHPUnit\Framework\TestCase;

class ModelelocationTest extends TestCase
{

    public function testModelelocationCanBeCreated():void {
        $modelelocation=new Modelelocation();
        $this->assertInstanceOf(Modelelocation::class,$modelelocation);
    }

    public function testModelelocationPropertiesCanBeSetAndRead():void {
        $modelelocation=new Modelelocation();
        $modelelocation->setLibelle('Chemisier');
        $modelelocation->setPrixLocation(19.99);
        $modelelocation->setTissu('Coton');
        $modelelocation->setSilhouette('Large');
        $modelelocation->setLongueur('court');
        $modelelocation->setLongueurManches('longues'); 
        $modelelocation->setFormeCol('Claudine');
        $modelelocation->setDescription('Chemisier blanc en coton');
        $this->assertSame('Chemisier', $modelelocation->getLibelle());
        $this->assertEquals(19.99, $modelelocation->getPrixLocation());
        $this->assertSame('Coton', $modelelocation->getTissu());
        $this->assertSame('Large', $modelelocation->getSilhouette());
        $this->assertSame('court', $modelelocation->getLongueur());
        $this->assertSame('longues', $modelelocation->getLongueurManches());
        $this->assertSame('Claudine', $modelelocation->getFormeCol());  
        $this->assertSame('Chemisier blanc en coton', $modelelocation->getDescription());
    }

    public function testModelelocationCanHaveType():void
    {
        $modelelocation=new Modelelocation();
        $type=new TypeLocation();
        $type->setLibelle('Hauts');
        $modelelocation->setType($type);
        $this->assertSame($type, $modelelocation->getType());
        $this->assertSame('Hauts', $modelelocation->getType()->getLibelle());
    }

    public function testModelelocationCanHaveAnEntretien():void
    {
        $modelelocation=new Modelelocation();
        $entretien=new Entretien();
        $entretien->setLavage('30°C max');
        $entretien->setNettoyageSec('non');
        $entretien->setRepassage('110°C max');
        $entretien->setDescription('Fragile, à laver avec précaution');
        $modelelocation->setEntretien($entretien);
        $this->assertSame($entretien, $modelelocation->getEntretien());
        $this->assertSame('30°C max', $modelelocation->getEntretien()->getLavage());
        $this->assertSame('110°C max', $modelelocation->getEntretien()->getRepassage());
        $this->assertSame('non', $modelelocation->getEntretien()->getNettoyageSec());
        $this->assertSame('Fragile, à laver avec précaution', $modelelocation->getEntretien()->getDescription());
    }
}