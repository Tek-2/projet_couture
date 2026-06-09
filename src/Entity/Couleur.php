<?php

namespace App\Entity;

use App\Repository\CouleurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;


#[ORM\Entity(repositoryClass: CouleurRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['modele:read']]
)]
class Couleur
{
    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(length: 255)]
    private ?string $nom_couleur = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(length: 255)]
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCouleur(): ?string
    {
        return $this->nom_couleur;
    }

    public function setNomCouleur(string $nom_couleur): static
    {
        $this->nom_couleur = $nom_couleur;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
