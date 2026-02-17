<?php

namespace App\Entity;

use App\Repository\ReunionSignalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReunionSignalRepository::class)]
class ReunionSignal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $DateReunion = null;

    /**
     * @var Collection<int, ReleveDeDecision>
     */
    #[ORM\OneToMany(targetEntity: ReleveDeDecision::class, mappedBy: 'reunionSignal')]
    private Collection $ReleveDeDecision;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $UserCreate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $UserModif = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $UpdatedAt = null;

    // #[ORM\Column(nullable: true)]
    // private ?bool $ReunionAnnulee = null;

    /**
     * @var Collection<int, Signal>
     */
    #[ORM\ManyToMany(targetEntity: Signal::class, inversedBy: 'reunionSignals')]
    private Collection $SignalLie;

    /**
     * @var Collection<int, Suivi>
     */
    #[ORM\OneToMany(targetEntity: Suivi::class, mappedBy: 'reunionSignal')]
    private Collection $suivis;

    /**
     * @var Collection<int, FichiersReunionsSignal>
     */
    #[ORM\OneToMany(targetEntity: FichiersReunionsSignal::class, mappedBy: 'reunionSignalLiee')]
    private Collection $fichiersReunionsSignals;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statutReunion = null;

    /**
     * @var Collection<int, LiensReunionsSignal>
     */
    #[ORM\OneToMany(targetEntity: LiensReunionsSignal::class, mappedBy: 'reunionSignal', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $liensReunionsSignals;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Commentaire = null;

    public function __construct(?string $userName = null)
    {
        $this->ReleveDeDecision = new ArrayCollection();
        $this->SignalLie = new ArrayCollection();
        $this->suivis = new ArrayCollection();
        $this->UserCreate = $userName;
        $this->UserModif = $userName;
        $this->CreatedAt = new \DateTimeImmutable();
        $this->UpdatedAt = new \DateTimeImmutable();
        $this->fichiersReunionsSignals = new ArrayCollection();
        $this->liensReunionsSignals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReunion(): ?\DateTime
    {
        return $this->DateReunion;
    }

    public function setDateReunion(?\DateTime $DateReunion): static
    {
        $this->DateReunion = $DateReunion;

        return $this;
    }

    /**
     * @return Collection<int, ReleveDeDecision>
     */
    public function getReleveDeDecision(): Collection
    {
        return $this->ReleveDeDecision;
    }

    public function addReleveDeDecision(ReleveDeDecision $releveDeDecision): static
    {
        if (!$this->ReleveDeDecision->contains($releveDeDecision)) {
            $this->ReleveDeDecision->add($releveDeDecision);
            $releveDeDecision->setReunionSignal($this);
        }

        return $this;
    }

    public function removeReleveDeDecision(ReleveDeDecision $releveDeDecision): static
    {
        if ($this->ReleveDeDecision->removeElement($releveDeDecision)) {
            // set the owning side to null (unless already changed)
            if ($releveDeDecision->getReunionSignal() === $this) {
                $releveDeDecision->setReunionSignal(null);
            }
        }

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

    // public function isReunionAnnulee(): ?bool
    // {
    //     return $this->ReunionAnnulee;
    // }

    // public function setReunionAnnulee(?bool $ReunionAnnulee): static
    // {
    //     $this->ReunionAnnulee = $ReunionAnnulee;

    //     return $this;
    // }

    /**
     * @return Collection<int, Signal>
     */
    public function getSignalLie(): Collection
    {
        return $this->SignalLie;
    }

    public function addSignalLie(Signal $signalLie): static
    {
        if (!$this->SignalLie->contains($signalLie)) {
            $this->SignalLie->add($signalLie);
        }

        return $this;
    }

    public function removeSignalLie(Signal $signalLie): static
    {
        $this->SignalLie->removeElement($signalLie);

        return $this;
    }

    /**
     * @return Collection<int, Suivi>
     */
    public function getSuivis(): Collection
    {
        return $this->suivis;
    }

    public function addSuivi(Suivi $suivi): static
    {
        if (!$this->suivis->contains($suivi)) {
            $this->suivis->add($suivi);
            $suivi->setReunionSignal($this);
        }

        return $this;
    }

    public function removeSuivi(Suivi $suivi): static
    {
        if ($this->suivis->removeElement($suivi)) {
            // set the owning side to null (unless already changed)
            if ($suivi->getReunionSignal() === $this) {
                $suivi->setReunionSignal(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FichiersReunionsSignal>
     */
    public function getFichiersReunionsSignals(): Collection
    {
        return $this->fichiersReunionsSignals;
    }

    public function addFichiersReunionsSignal(FichiersReunionsSignal $fichiersReunionsSignal): static
    {
        if (!$this->fichiersReunionsSignals->contains($fichiersReunionsSignal)) {
            $this->fichiersReunionsSignals->add($fichiersReunionsSignal);
            $fichiersReunionsSignal->setReunionSignalLiee($this);
        }

        return $this;
    }

    public function removeFichiersReunionsSignal(FichiersReunionsSignal $fichiersReunionsSignal): static
    {
        if ($this->fichiersReunionsSignals->removeElement($fichiersReunionsSignal)) {
            // set the owning side to null (unless already changed)
            if ($fichiersReunionsSignal->getReunionSignalLiee() === $this) {
                $fichiersReunionsSignal->setReunionSignalLiee(null);
            }
        }

        return $this;
    }

    public function getStatutReunion(): ?string
    {
        return $this->statutReunion;
    }

    public function setStatutReunion(?string $statutReunion): static
    {
        $this->statutReunion = $statutReunion;

        return $this;
    }

    /**
     * @return Collection<int, LiensReunionsSignal>
     */
    public function getLiensReunionsSignals(): Collection
    {
        return $this->liensReunionsSignals;
    }

    public function addLiensReunionsSignal(LiensReunionsSignal $liensReunionsSignal): static
    {
        if (!$this->liensReunionsSignals->contains($liensReunionsSignal)) {
            $this->liensReunionsSignals->add($liensReunionsSignal);
            $liensReunionsSignal->setReunionSignal($this);
        }

        return $this;
    }

    public function removeLiensReunionsSignal(LiensReunionsSignal $liensReunionsSignal): static
    {
        if ($this->liensReunionsSignals->removeElement($liensReunionsSignal)) {
            // set the owning side to null (unless already changed)
            if ($liensReunionsSignal->getReunionSignal() === $this) {
                $liensReunionsSignal->setReunionSignal(null);
            }
        }

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->Commentaire;
    }

    public function setCommentaire(?string $Commentaire): static
    {
        $this->Commentaire = $Commentaire;

        return $this;
    }
}
