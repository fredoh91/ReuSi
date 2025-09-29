<?php

namespace App\Entity;

use App\Repository\ReleveDeDecisionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReleveDeDecisionRepository::class)]
class ReleveDeDecision
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $PassageCTP = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $PassageRSS = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $EmetteurSuivi = null;

    /**
     * @var Collection<int, MesuresRDD>
     */
    #[ORM\OneToMany(targetEntity: MesuresRDD::class, mappedBy: 'RddLie', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $mesuresRDDs;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $DatePresentationReunion = null;

    #[ORM\OneToOne(mappedBy: 'RddLie', cascade: ['persist', 'remove'])]
    private ?Suivi $suivi = null;

    public function __construct()
    {
        $this->mesuresRDDs = new ArrayCollection();
    }

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

    public function getPassageCTP(): ?string
    {
        return $this->PassageCTP;
    }

    public function setPassageCTP(?string $PassageCTP): static
    {
        $this->PassageCTP = $PassageCTP;

        return $this;
    }

    public function getPassageRSS(): ?string
    {
        return $this->PassageRSS;
    }

    public function setPassageRSS(?string $PassageRSS): static
    {
        $this->PassageRSS = $PassageRSS;

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
     * @return Collection<int, MesuresRDD>
     */
    public function getMesuresRDDs(): Collection
    {
        return $this->mesuresRDDs;
    }

    public function addMesuresRDD(MesuresRDD $mesuresRDD): static
    {
        if (!$this->mesuresRDDs->contains($mesuresRDD)) {
            $this->mesuresRDDs->add($mesuresRDD);
            $mesuresRDD->setRddLie($this);
        }

        return $this;
    }

    public function removeMesuresRDD(MesuresRDD $mesuresRDD): static
    {
        if ($this->mesuresRDDs->removeElement($mesuresRDD)) {
            // set the owning side to null (unless already changed)
            if ($mesuresRDD->getRddLie() === $this) {
                $mesuresRDD->setRddLie(null);
            }
        }

        return $this;
    }

    public function getDatePresentationReunion(): ?\DateTimeImmutable
    {
        return $this->DatePresentationReunion;
    }

    public function setDatePresentationReunion(?\DateTimeImmutable $DatePresentationReunion): static
    {
        $this->DatePresentationReunion = $DatePresentationReunion;

        return $this;
    }

    public function getSuivi(): ?Suivi
    {
        return $this->suivi;
    }

    public function setSuivi(?Suivi $suivi): static
    {
        // unset the owning side of the relation if necessary
        if ($suivi === null && $this->suivi !== null) {
            $this->suivi->setRddLie(null);
        }

        // set the owning side of the relation if necessary
        if ($suivi !== null && $suivi->getRddLie() !== $this) {
            $suivi->setRddLie($this);
        }

        $this->suivi = $suivi;

        return $this;
    }
}
