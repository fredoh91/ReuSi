<?php

namespace App\Entity;

use App\Repository\SuiviRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuiviRepository::class)]
class Suivi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $NumeroSuivi = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $DescriptionSuivi = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $PiloteDS = null;

    #[ORM\ManyToOne(inversedBy: 'suivis')]
    private ?Signal $SignalLie = null;

    #[ORM\ManyToOne(inversedBy: 'suivis')]
    private ?ReunionSignal $reunionSignal = null;

    #[ORM\OneToOne(inversedBy: 'suivi', cascade: ['persist', 'remove'])]
    private ?ReleveDeDecision $RddLie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $UserCreate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $UserModif = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $UpdatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $EmetteurSuivi = null;

    /**
     * @var Collection<int, StatutSuivi>
     */
    #[ORM\OneToMany(targetEntity: StatutSuivi::class, mappedBy: 'SuiviLie', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(["id" => "ASC"])]
    private Collection $statutSuivis;

    public function __construct()
    {
        $this->statutSuivis = new ArrayCollection();
                // Création automatique du StatutSignal "brouillon"
        $statutSuivi = new StatutSuivi();
        $statutSuivi->setLibStatut('brouillon');
        $statutSuivi->setDateDesactivation(null);
        $statutSuivi->setStatutActif(true);
        $statutSuivi->setSuiviLie($this);

        $this->statutSuivis->add($statutSuivi);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroSuivi(): ?int
    {
        return $this->NumeroSuivi;
    }

    public function setNumeroSuivi(int $NumeroSuivi): static
    {
        $this->NumeroSuivi = $NumeroSuivi;

        return $this;
    }

    public function getDescriptionSuivi(): ?string
    {
        return $this->DescriptionSuivi;
    }

    public function setDescriptionSuivi(?string $DescriptionSuivi): static
    {
        $this->DescriptionSuivi = $DescriptionSuivi;

        return $this;
    }

    public function getPiloteDS(): ?string
    {
        return $this->PiloteDS;
    }

    public function setPiloteDS(?string $PiloteDS): static
    {
        $this->PiloteDS = $PiloteDS;

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

    public function getRddLie(): ?ReleveDeDecision
    {
        return $this->RddLie;
    }

    public function setRddLie(?ReleveDeDecision $RddLie): static
    {
        $this->RddLie = $RddLie;

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

    public function getEmetteurSuivi(): ?string
    {
        return $this->EmetteurSuivi;
    }

    public function setEmetteurSuivi(?string $EmetteurSuivi): static
    {
        $this->EmetteurSuivi = $EmetteurSuivi;

        return $this;
    }

    /**
     * @return Collection<int, StatutSuivi>
     */
    public function getStatutSuivis(): Collection
    {
        return $this->statutSuivis;
    }

    public function addStatutSuivi(StatutSuivi $statutSuivi): static
    {
        if (!$this->statutSuivis->contains($statutSuivi)) {
            $this->statutSuivis->add($statutSuivi);
            $statutSuivi->setSuiviLie($this);
        }

        return $this;
    }

    public function removeStatutSuivi(StatutSuivi $statutSuivi): static
    {
        if ($this->statutSuivis->removeElement($statutSuivi)) {
            // set the owning side to null (unless already changed)
            if ($statutSuivi->getSuiviLie() === $this) {
                $statutSuivi->setSuiviLie(null);
            }
        }

        return $this;
    }
}
