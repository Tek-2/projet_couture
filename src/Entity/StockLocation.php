<?php

namespace App\Entity;

use App\Repository\StockLocationRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: StockLocationRepository::class)]
#[ApiResource]
class StockLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $taille = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(length: 255)]
    private ?string $imageVariante = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Couleur $couleur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModeleLocation $modeleLocation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille(string $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getImageVariante(): ?string
    {
        return $this->imageVariante;
    }

    public function setImageVariante(string $imageVariante): static
    {
        $this->imageVariante = $imageVariante;

        return $this;
    }

    public function getCouleur(): ?Couleur
    {
        return $this->couleur;
    }

    public function setCouleur(?Couleur $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
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
}
