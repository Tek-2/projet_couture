<?php

namespace App\Entity;

use App\Repository\EntretienRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: EntretienRepository::class)]
#[ApiResource]
class Entretien
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lavage = null;

    #[ORM\Column(length: 255)]
    private ?string $nettoyage_sec = null;

    #[ORM\Column(length: 255)]
    private ?string $repassage = null;

    #[ORM\Column(type: Types::TEXT, nullable:true)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLavage(): ?string
    {
        return $this->lavage;
    }

    public function setLavage(string $lavage): static
    {
        $this->lavage = $lavage;

        return $this;
    }

    public function getNettoyageSec(): ?string
    {
        return $this->nettoyage_sec;
    }

    public function setNettoyageSec(string $nettoyage_sec): static
    {
        $this->nettoyage_sec = $nettoyage_sec;

        return $this;
    }

    public function getRepassage(): ?string
    {
        return $this->repassage;
    }

    public function setRepassage(string $repassage): static
    {
        $this->repassage = $repassage;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
