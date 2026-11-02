<?php

namespace App\Tests\Unit;

use App\Entity\Entretien;
use PHPUnit\Framework\TestCase;

class EntretienTest extends TestCase
{

    public function testEntretienCanBeCreated():void {
        $entretien=new Entretien();
        $this->assertInstanceOf(Entretien::class,$entretien);
    }

    public function testEntretienPropertiesCanBeSetAndRead():void {
        $entretien=new Entretien();
        $entretien->setLavage('30°C max');
        $entretien->setNettoyageSec('non');
        $entretien->setRepassage('110°C max');
        $entretien->setDescription('Fragile, à laver avec précaution');
        $this->assertSame('30°C max', $entretien->getLavage());
        $this->assertSame('110°C max', $entretien->getRepassage());
        $this->assertSame('non', $entretien->getNettoyageSec());
        $this->assertSame('Fragile, à laver avec précaution', $entretien->getDescription());
    }

    
}