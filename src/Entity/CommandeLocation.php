<?php

namespace App\Entity;

use App\Repository\CommandeLocationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: CommandeLocationRepository::class)]
#[ApiResource]
class CommandeLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_commande = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_debut_loc = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_fin_loc = null;

    #[ORM\Column]
    private ?bool $location = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montant_total = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $id_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeImmutable
    {
        return $this->date_commande;
    }

    public function setDateCommande(\DateTimeImmutable $date_commande): static
    {
        $this->date_commande = $date_commande;

        return $this;
    }

    public function getDateDebutLoc(): ?\DateTimeImmutable
    {
        return $this->date_debut_loc;
    }

    public function setDateDebutLoc(\DateTimeImmutable $date_debut_loc): static
    {
        $this->date_debut_loc = $date_debut_loc;

        return $this;
    }

    public function getDateFinLoc(): ?\DateTimeImmutable
    {
        return $this->date_fin_loc;
    }

    public function setDateFinLoc(?\DateTimeImmutable $date_fin_loc): static
    {
        $this->date_fin_loc = $date_fin_loc;

        return $this;
    }

    public function isLocation(): ?bool
    {
        return $this->location;
    }

    public function setLocation(bool $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getMontantTotal(): ?string
    {
        return $this->montant_total;
    }

    public function setMontantTotal(string $montant_total): static
    {
        $this->montant_total = $montant_total;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }
}
