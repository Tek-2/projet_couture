<?php

namespace App\Entity;

use App\Repository\StockLocationImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: StockLocationImageRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['stock:read']]
)]
class StockLocationImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['stock:read'])]
    #[ORM\ManyToOne(inversedBy: 'stockLocationImages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModeleLocation $modeleLocation = null;

    #[Groups(['stock:read', 'modele:read'])]
    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[Groups(['stock:read', 'modele:read'])]
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
