<?php

namespace App\Entity;

use App\Repository\FichiersReunionsSignalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FichiersReunionsSignalRepository::class)]
class FichiersReunionsSignal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $nomFichier = null;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $nomOriginal = null;

    #[ORM\Column(nullable: true)]
    private ?int $taille = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mimeType = null;

    #[ORM\ManyToOne(inversedBy: 'fichiersReunionsSignals')]
    private ?ReunionSignal $reunionSignalLiee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $UserCreate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $UserModif = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $UpdatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFichier(): ?string
    {
        return $this->nomFichier;
    }

    public function setNomFichier(?string $nomFichier): static
    {
        $this->nomFichier = $nomFichier;

        return $this;
    }

    public function getNomOriginal(): ?string
    {
        return $this->nomOriginal;
    }

    public function setNomOriginal(?string $nomOriginal): static
    {
        $this->nomOriginal = $nomOriginal;

        return $this;
    }

    public function getTaille(): ?int
    {
        return $this->taille;
    }

    public function setTaille(?int $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getReunionSignalLiee(): ?ReunionSignal
    {
        return $this->reunionSignalLiee;
    }

    public function setReunionSignalLiee(?ReunionSignal $reunionSignalLiee): static
    {
        $this->reunionSignalLiee = $reunionSignalLiee;

        return $this;
    }

    public function getUserCreate(): ?string
    {
        return $this->UserCreate;
    }

    public function setUserCreate(?string $UserCreate): static
    {
        $this->UserCreate = $UserCreate;

        return $this;
    }

    public function getUserModif(): ?string
    {
        return $this->UserModif;
    }

    public function setUserModif(?string $UserModif): static
    {
        $this->UserModif = $UserModif;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $CreatedAt): static
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->UpdatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $UpdatedAt): static
    {
        $this->UpdatedAt = $UpdatedAt;

        return $this;
    }
}
