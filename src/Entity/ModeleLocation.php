<?php

namespace App\Entity;

use App\Repository\ModeleLocationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: ModeleLocationRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['modele:read']]
)]
class ModeleLocation
{
    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prixLocation = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $prixAchat = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(length: 255)]
    private ?string $tissu = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(length: 255)]
    private ?string $silhouette = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(length: 255)]
    private ?string $longueur = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(length: 255)]
    private ?string $longueurManches = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(length: 255)]
    private ?string $formeCol = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Entretien $entretien = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?TypeLocation $type = null;

    #[ORM\OneToMany(mappedBy: "modeleLocation", targetEntity: StockLocation::class)]
    #[Groups(["modele:read"])]
    private Collection $stockLocations;

    public function __construct()
    {
        $this->stockLocations = new ArrayCollection();
    }   

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

    public function getStockLocations(): Collection
    {
        return $this->stockLocations;
    }
    

}
