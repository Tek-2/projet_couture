<?php

namespace App\Tests\Unit;

use App\Entity\CommandeLocation;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CommandeLocationTest extends TestCase
{

    public function testCommandeLocationCanBeCreated(): void
    {
        $commandeLocation = new CommandeLocation();

        $this->assertInstanceOf(
            CommandeLocation::class,
            $commandeLocation
        );
    }


    public function testCommandeLocationPropertiesCanBeSetAndRead(): void
    {
        $commandeLocation = new CommandeLocation();

        $user = new User();

        $dateDebut = new \DateTimeImmutable('2026-08-10 10:00:00');
        $dateFin = new \DateTimeImmutable('2026-08-10 12:00:00');


        $commandeLocation
            ->setUser($user)
            ->setStatut('valide')
            ->setCommentaire('Livraison souhaitée le matin')
            ->setCreneauxClient([
                '2026-08-10 09:00',
                '2026-08-10 10:00'
            ])
            ->setPropositionDebut($dateDebut)
            ->setPropositionFin($dateFin)
            ->setDateDebutValidee($dateDebut)
            ->setDateFinValidee($dateFin);


        $this->assertSame(
            $user,
            $commandeLocation->getUser()
        );


        $this->assertSame(
            'valide',
            $commandeLocation->getStatut()
        );


        $this->assertSame(
            'Livraison souhaitée le matin',
            $commandeLocation->getCommentaire()
        );


        $this->assertSame(
            [
                '2026-08-10 09:00',
                '2026-08-10 10:00'
            ],
            $commandeLocation->getCreneauxClient()
        );


        $this->assertSame(
            $dateDebut,
            $commandeLocation->getPropositionDebut()
        );


        $this->assertSame(
            $dateFin,
            $commandeLocation->getPropositionFin()
        );


        $this->assertSame(
            $dateDebut,
            $commandeLocation->getDateDebutValidee()
        );


        $this->assertSame(
            $dateFin,
            $commandeLocation->getDateFinValidee()
        );
    }


    public function testCommandeLocationIsNotValidatedByDefault(): void
    {
        $commandeLocation = new CommandeLocation();

        $this->assertFalse(
            $commandeLocation->estValide()
        );
    }


    public function testCommandeLocationCanBeValidated(): void
    {
        $commandeLocation = new CommandeLocation();

        $commandeLocation->setStatut('valide');


        $this->assertTrue(
            $commandeLocation->estValide()
        );
    }


    public function testDateCommandeIsAutomaticallyCreated(): void
    {
        $commandeLocation = new CommandeLocation();


        $this->assertNotNull(
            $commandeLocation->getDateCommande()
        );


        $this->assertInstanceOf(
            \DateTimeImmutable::class,
            $commandeLocation->getDateCommande()
        );
    }
}