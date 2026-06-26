<?php

namespace App\Entity;

use App\Repository\CreneauRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreneauRepository::class)]
class Creneau
{
    public const STATUT_AVAILABLE = 'available';
    public const STATUT_FULL = 'full';
    public const STATUT_BLOCKED = 'blocked';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private \DateTimeImmutable $dateDebut;

    #[ORM\Column]
    private \DateTimeImmutable $dateFin;

    #[ORM\Column]
    private int $capacite = 3;

    #[ORM\Column(length: 20)]
    private string $statut = self::STATUT_AVAILABLE;

    #[ORM\Column]
    private bool $isWeekendAllowed = false;
}