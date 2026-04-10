<?php

namespace App\Entity;

use App\Repository\ModeleLocationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: ModeleLocationRepository::class)]
#[ApiResource]
class ModeleLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prixLocation = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $prixAchat = null;

    #[ORM\Column(length: 255)]
    private ?string $tissu = null;

    #[ORM\Column(length: 255)]
    private ?string $silhouette = null;

    #[ORM\Column(length: 255)]
    private ?string $longueur = null;

    #[ORM\Column(length: 255)]
    private ?string $longueurManches = null;

    #[ORM\Column(length: 255)]
    private ?string $formeCol = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entretien $entretien = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeLocation $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPrixLocation(): ?string
    {
        return $this->prixLocation;
    }

    public function setPrixLocation(string $prixLocation): static
    {
        $this->prixLocation = $prixLocation;

        return $this;
    }

    public function getPrixAchat(): ?string
    {
        return $this->prixAchat;
    }

    public function setPrixAchat(?string $prixAchat): static
    {
        $this->prixAchat = $prixAchat;

        return $this;
    }

    public function getTissu(): ?string
    {
        return $this->tissu;
    }

    public function setTissu(string $tissu): static
    {
        $this->tissu = $tissu;

        return $this;
    }

    public function getSilhouette(): ?string
    {
        return $this->silhouette;
    }

    public function setSilhouette(string $silhouette): static
    {
        $this->silhouette = $silhouette;

        return $this;
    }

    public function getLongueur(): ?string
    {
        return $this->longueur;
    }

    public function setLongueur(string $longueur): static
    {
        $this->longueur = $longueur;

        return $this;
    }

    public function getLongueurManches(): ?string
    {
        return $this->longueurManches;
    }

    public function setLongueurManches(string $longueurManches): static
    {
        $this->longueurManches = $longueurManches;

        return $this;
    }

    public function getFormeCol(): ?string
    {
        return $this->formeCol;
    }

    public function setFormeCol(string $formeCol): static
    {
        $this->formeCol = $formeCol;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEntretien(): ?Entretien
    {
        return $this->entretien;
    }

    public function setEntretien(?Entretien $entretien): static
    {
        $this->entretien = $entretien;

        return $this;
    }

    public function getType(): ?TypeLocation
    {
        return $this->type;
    }

    public function setType(?TypeLocation $type): static
    {
        $this->type = $type;

        return $this;
    }
}
