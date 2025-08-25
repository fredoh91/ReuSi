<?php

namespace App\Entity;

use App\Repository\ReleveDeDecisionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReleveDeDecisionRepository::class)]
class ReleveDeDecision
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $NumeroRDD = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $DescriptionRDD = null;

    #[ORM\ManyToOne(inversedBy: 'ReleveDeDecision')]
    private ?Signal $SignalLie = null;

    #[ORM\ManyToOne(inversedBy: 'ReleveDeDecision')]
    private ?ReunionSignal $reunionSignal = null;

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

    public function getNumeroRDD(): ?int
    {
        return $this->NumeroRDD;
    }

    public function setNumeroRDD(?int $NumeroRDD): static
    {
        $this->NumeroRDD = $NumeroRDD;

        return $this;
    }

    public function getDescriptionRDD(): ?string
    {
        return $this->DescriptionRDD;
    }

    public function setDescriptionRDD(?string $DescriptionRDD): static
    {
        $this->DescriptionRDD = $DescriptionRDD;

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

    public function getReunionSignal(): ?ReunionSignal
    {
        return $this->reunionSignal;
    }

    public function setReunionSignal(?ReunionSignal $reunionSignal): static
    {
        $this->reunionSignal = $reunionSignal;

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
