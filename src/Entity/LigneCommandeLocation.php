<?php

namespace App\Entity;

use App\Repository\LigneCommandeLocationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: LigneCommandeLocationRepository::class)]
#[ApiResource]
class LigneCommandeLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?CommandeLocation $id_commandeloc = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?StockLocation $id_stockloc = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column]
    private ?int $quantite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCommandeloc(): ?CommandeLocation
    {
        return $this->id_commandeloc;
    }

    public function setIdCommandeloc(?CommandeLocation $id_commandeloc): static
    {
        $this->id_commandeloc = $id_commandeloc;

        return $this;
    }

    public function getIdStockloc(): ?StockLocation
    {
        return $this->id_stockloc;
    }

    public function setIdStockloc(?StockLocation $id_stockloc): static
    {
        $this->id_stockloc = $id_stockloc;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

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
}
