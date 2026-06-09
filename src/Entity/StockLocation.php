<?php

namespace App\Entity;

use App\Repository\StockLocationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: StockLocationRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['stock:read']]
)]
class StockLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(length: 255)]
    private ?string $taille = null;
    
    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column]
    private ?int $quantite = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(length: 255)]
    private ?string $imageVariante = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Couleur $couleur = null;

    #[Groups(['stock:read'])]
    #[ORM\ManyToOne(inversedBy: "stockLocations")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
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
