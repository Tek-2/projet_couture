<?php

namespace App\Tests\Repository;

use App\Entity\TypeLocation;
use App\Repository\TypeLocationRepository;

class TypeLocationRepositoryTest extends RepositoryTestCase
{
    private TypeLocationRepository $repository;


    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = static::getContainer()
            ->get(TypeLocationRepository::class);
    }


    public function testTypeLocationCanBeSavedAndFound(): void
    {
        $typeLocation = new TypeLocation();

        $typeLocation->setLibelle('Location soirée');


        $this->entityManager->persist($typeLocation);
        $this->entityManager->flush();
        $this->entityManager->clear();


        $savedType = $this->repository->findOneBy([
            'libelle' => 'Location soirée'
        ]);


        $this->assertNotNull($savedType);

        $this->assertInstanceOf(
            TypeLocation::class,
            $savedType
        );

        $this->assertSame(
            'Location soirée',
            $savedType->getLibelle()
        );
    }


    public function testTypeLocationCanBeDeleted(): void
    {
        $typeLocation = new TypeLocation();

        $typeLocation->setLibelle('Type à supprimer');


        $this->entityManager->persist($typeLocation);
        $this->entityManager->flush();


        $id = $typeLocation->getId();


        $this->entityManager->remove($typeLocation);
        $this->entityManager->flush();
        $this->entityManager->clear();


        $deletedType = $this->repository->find($id);


        $this->assertNull($deletedType);
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}