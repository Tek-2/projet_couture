<?php

namespace App\Tests\Unit;

use App\Entity\TypeLocation;
use PHPUnit\Framework\TestCase;

class TypeLocationTest extends TestCase
{

    public function testTypeLocationCanBeCreated():void {
        $typeLocation=new TypeLocation();
        $this->assertInstanceOf(TypeLocation::class,$typeLocation);
    }

    public function testTypeLocationPropertiesCanBeSetAndRead():void {
        $typeLocation=new TypeLocation();
        $typeLocation->setLibelle('Hauts');
        $this->assertSame('Hauts', $typeLocation->getLibelle());
    }
}