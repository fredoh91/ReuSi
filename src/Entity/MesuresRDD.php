<?php

namespace App\Entity;

use App\Repository\MesuresRDDRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MesuresRDDRepository::class)]
class MesuresRDD
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $LibMesure = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $DetailCommentaire = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $DateCloturePrev = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $DateClotureEffective = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $DesactivateAt = null;

    #[ORM\ManyToOne(inversedBy: 'mesuresRDDs')]
    private ?ReleveDeDecision $RddLie = null;

    #[ORM\ManyToOne(inversedBy: 'mesuresRDDs')]
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

    public function getLibMesure(): ?string
    {
        return $this->LibMesure;
    }

    public function setLibMesure(?string $LibMesure): static
    {
        $this->LibMesure = $LibMesure;

        return $this;
    }

    public function getDetailCommentaire(): ?string
    {
        return $this->DetailCommentaire;
    }

    public function setDetailCommentaire(?string $DetailCommentaire): static
    {
        $this->DetailCommentaire = $DetailCommentaire;

        return $this;
    }

    public function getDateCloturePrev(): ?\DateTimeImmutable
    {
        return $this->DateCloturePrev;
    }

    public function setDateCloturePrev(?\DateTimeImmutable $DateCloturePrev): static
    {
        $this->DateCloturePrev = $DateCloturePrev;

        return $this;
    }

    public function getDateClotureEffective(): ?\DateTimeImmutable
    {
        return $this->DateClotureEffective;
    }

    public function setDateClotureEffective(?\DateTimeImmutable $DateClotureEffective): static
    {
        $this->DateClotureEffective = $DateClotureEffective;

        return $this;
    }

    public function getDesactivateAt(): ?\DateTimeImmutable
    {
        return $this->DesactivateAt;
    }

    public function setDesactivateAt(?\DateTimeImmutable $DesactivateAt): static
    {
        $this->DesactivateAt = $DesactivateAt;

        return $this;
    }

    public function getRddLie(): ?ReleveDeDecision
    {
        return $this->RddLie;
    }

    public function setRddLie(?ReleveDeDecision $RddLie): static
    {
        $this->RddLie = $RddLie;

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
