<?php

namespace App\Entity;

use App\Repository\CommandeLocationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get; 
use ApiPlatform\Metadata\GetCollection; 
use ApiPlatform\Metadata\Post; 
use ApiPlatform\Metadata\Put; 
use ApiPlatform\Metadata\Delete;
use App\State\CommandeLocationProcessor;

#[ORM\Entity(repositoryClass: CommandeLocationRepository::class)] 
#[ApiResource( 
    operations: [ 
        new GetCollection(security: "is_granted('ROLE_ADMIN')"), 
        new Post( security: "is_granted('ROLE_USER')", processor: CommandeLocationProcessor::class ), 
        new Get(security: "is_granted('ROLE_ADMIN') or object.getUser() == user"), 
        new Put(security: "is_granted('ROLE_ADMIN') or object.getUser() == user"), 
        new Delete(security: "is_granted('ROLE_ADMIN') or object.getUser() == user"), 
    ] 
)] 
class CommandeLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commandeLocations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 50)]
    private string $statut = 'en_attente';

    #[ORM\Column]
    private ?\DateTimeImmutable $date_commande = null;

    // Créneaux proposés par le client
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $creneaux_client = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $commentaire = null;

    // Créneau choisi par admin (unique)
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $proposition_debut = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $proposition_fin = null;

    // Créneau validé final
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_debut_validee = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_fin_validee = null;

    public function __construct()
    {
        $this->date_commande = new \DateTimeImmutable();
    }

    public function estValide(): bool
    {
        return $this->statut === 'valide';
    }

    // ================= GETTERS / SETTERS =================

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }

    public function getDateCommande(): ?\DateTimeImmutable { return $this->date_commande; }

    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(?string $c): self { $this->commentaire = $c; return $this; }

    public function getCreneauxClient(): ?array { return $this->creneaux_client; }
    public function setCreneauxClient(?array $c): self { $this->creneaux_client = $c; return $this; }

    public function getPropositionDebut(): ?\DateTimeImmutable { return $this->proposition_debut; }
    public function setPropositionDebut(?\DateTimeImmutable $d): self { $this->proposition_debut = $d; return $this; }

    public function getPropositionFin(): ?\DateTimeImmutable { return $this->proposition_fin; }
    public function setPropositionFin(?\DateTimeImmutable $d): self { $this->proposition_fin = $d; return $this; }

    public function getDateDebutValidee(): ?\DateTimeImmutable { return $this->date_debut_validee; }
    public function setDateDebutValidee(?\DateTimeImmutable $d): self { $this->date_debut_validee = $d; return $this; }

    public function getDateFinValidee(): ?\DateTimeImmutable { return $this->date_fin_validee; }
    public function setDateFinValidee(?\DateTimeImmutable $d): self { $this->date_fin_validee = $d; return $this; }
}