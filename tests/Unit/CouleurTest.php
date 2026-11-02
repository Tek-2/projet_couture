<?php

namespace App\Tests\Unit;

use App\Entity\Couleur;
use PHPUnit\Framework\TestCase;

class CouleurTest extends TestCase
{

    public function testCouleurCanBeCreated():void {
        $couleur=new Couleur();
        $this->assertInstanceOf(Couleur::class,$couleur);
    }

    public function testCouleurPropertiesCanBeSetAndRead():void {
        $couleur=new Couleur();
        $couleur->setNomCouleur('Blanc');
        $couleur->setImage('blanc.png');
        $this->assertSame('Blanc', $couleur->getNomCouleur());
        $this->assertSame('blanc.png', $couleur->getImage());
    }
}