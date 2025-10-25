<?php

namespace App\Entity;

use App\Repository\StatutSignalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatutSignalRepository::class)]
class StatutSignal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $LibStatut = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $DateMiseEnPlace = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $DateDesactivation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $StatutActif = null;

    #[ORM\ManyToOne(inversedBy: 'statutSignals')]
    private ?Signal $SignalLie = null;

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

    public function getLibStatut(): ?string
    {
        return $this->LibStatut;
    }

    public function setLibStatut(string $LibStatut): static
    {
        $this->LibStatut = $LibStatut;

        return $this;
    }

    public function getDateMiseEnPlace(): ?\DateTimeImmutable
    {
        return $this->DateMiseEnPlace;
    }

    public function setDateMiseEnPlace(?\DateTimeImmutable $DateMiseEnPlace): static
    {
        $this->DateMiseEnPlace = $DateMiseEnPlace;

        return $this;
    }

    public function getDateDesactivation(): ?\DateTimeImmutable
    {
        return $this->DateDesactivation;
    }

    public function setDateDesactivation(?\DateTimeImmutable $DateDesactivation): static
    {
        $this->DateDesactivation = $DateDesactivation;

        return $this;
    }

    public function isStatutActif(): ?bool
    {
        return $this->StatutActif;
    }

    public function setStatutActif(?bool $StatutActif): static
    {
        $this->StatutActif = $StatutActif;

        return $this;
    }

    public function getSignalLie(): ?Signal
    {
        return $this->SignalLie;
    }

    public function setSignalLie(?Signal $SignalLie): static
    {
        $this->SignalLie = $SignalLie;

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
