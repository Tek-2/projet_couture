<?php

namespace App\Entity;

use App\Repository\StockLocationImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockLocationImageRepository::class)]
class StockLocationImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stockLocationImages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModeleLocation $modeleLocation = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    private ?int $ordre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModeleLocation(): ?ModeleLocation
    {
        return $this->modeleLocation;
    }

    public function setModeleLocation(?ModeleLocation $modeleLocation): static
    {
        $this->modeleLocation = $modeleLocation;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }
}
