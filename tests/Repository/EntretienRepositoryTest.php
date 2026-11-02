<?php

namespace App\Tests\Repository;

use App\Entity\Entretien;
use App\Repository\EntretienRepository;

class EntretienRepositoryTest extends RepositoryTestCase
{
    private EntretienRepository $repository;


    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = static::getContainer()
            ->get(EntretienRepository::class);
    }


    public function testEntretienCanBeSavedAndFound(): void
    {
        $entretien = new Entretien();


        $entretien
            ->setLavage('TEST_30°C')
            ->setNettoyageSec('Non')
            ->setRepassage('110°C')
            ->setDescription('Lavage délicat');


        $this->entityManager->persist($entretien);
        $this->entityManager->flush();
        $this->entityManager->clear();


        $savedEntretien = $this->repository->findOneBy([
            'lavage' => 'TEST_30°C'
        ]);


        $this->assertNotNull($savedEntretien);

        $this->assertInstanceOf(
            Entretien::class,
            $savedEntretien
        );

        $this->assertSame(
            'TEST_30°C',
            $savedEntretien->getLavage()
        );

        $this->assertSame(
            'Non',
            $savedEntretien->getNettoyageSec()
        );

        $this->assertSame(
            '110°C',
            $savedEntretien->getRepassage()
        );

        $this->assertSame(
            'Lavage délicat',
            $savedEntretien->getDescription()
        );
    }


    public function testEntretienCanBeDeleted(): void
    {
        $entretien = new Entretien();


        $entretien
            ->setLavage('TEST_SUPPRESSION')
            ->setNettoyageSec('Oui')
            ->setRepassage('150°C')
            ->setDescription('Test suppression');


        $this->entityManager->persist($entretien);
        $this->entityManager->flush();


        $id = $entretien->getId();


        $this->entityManager->remove($entretien);
        $this->entityManager->flush();
        $this->entityManager->clear();


        $deletedEntretien = $this->repository->find($id);


        $this->assertNull($deletedEntretien);
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}