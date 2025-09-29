<?php

namespace App\Entity;

use App\Repository\SignalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SignalRepository::class)]
#[ORM\Table(name: '`signal`')]
class Signal
{

    // public const STATUS_DRAFT = 'draft';
    // public const STATUS_COMPLETED = 'completed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $DescriptionSignal = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Indication = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $DateCreation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Contexte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NiveauRisqueInitial = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NiveauRisqueFinal = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $AnaRisqueComment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $SourceSignal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $RefSignal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $IdentifiantSource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $UserCreate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $UserModif = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $UpdatedAt = null;

    /**
     * @var Collection<int, ReleveDeDecision>
     */
    #[ORM\OneToMany(targetEntity: ReleveDeDecision::class, mappedBy: 'SignalLie', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ReleveDeDecision;

    /**
     * @var Collection<int, ReunionSignal>
     */
    #[ORM\ManyToMany(targetEntity: ReunionSignal::class, mappedBy: 'SignalLie')]
    private Collection $reunionSignals;

    /**
     * @var Collection<int, Produits>
     */
    #[ORM\OneToMany(targetEntity: Produits::class, mappedBy: 'SignalLie', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $produits;

    // #[ORM\Column(length: 255, nullable: true)]
    // private ?string $StatutCreation = null;

    #[ORM\Column(length: 255)]
    private ?string $TypeSignal = null;

    /**
     * @var Collection<int, StatutSignal>
     */
    #[ORM\OneToMany(targetEntity: StatutSignal::class, mappedBy: 'SignalLie', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $statutSignals;

    /**
     * @var Collection<int, MesuresRDD>
     */
    #[ORM\OneToMany(targetEntity: MesuresRDD::class, mappedBy: 'SignalLie', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $mesuresRDDs;

    /**
     * @var Collection<int, Suivi>
     */
    #[ORM\OneToMany(targetEntity: Suivi::class, mappedBy: 'SignalLie')]
    private Collection $suivis;

    /**
     * @var Collection<int, DirectionPoleConcerne>
     */
    #[ORM\ManyToMany(targetEntity: DirectionPoleConcerne::class, mappedBy: 'SignalLie')]
    private Collection $directionPoleConcernes;

    // #[ORM\Column(length: 255, nullable: true)]
    // private ?string $TypeSignal = null;

    public function __construct()
    {
        $this->ReleveDeDecision = new ArrayCollection();
        $this->reunionSignals = new ArrayCollection();
        $this->produits = new ArrayCollection();
        // $this->StatutCreation = self::STATUS_DRAFT; // Par défaut, un signal est un brouillon
        $this->statutSignals = new ArrayCollection();

        // Création automatique du StatutSignal "brouillon"
        $statutSignal = new StatutSignal();
        $statutSignal->setLibStatut('brouillon');
        $statutSignal->setDateMiseEnPlace(new \DateTimeImmutable());
        $statutSignal->setDateDesactivation(null);
        $statutSignal->setStatutActif(true);
        $statutSignal->setSignalLie($this);

        $this->statutSignals->add($statutSignal);
        $this->mesuresRDDs = new ArrayCollection();
        $this->suivis = new ArrayCollection();
        $this->directionPoleConcernes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(?string $Titre): static
    {
        $this->Titre = $Titre;

        return $this;
    }

    public function getDescriptionSignal(): ?string
    {
        return $this->DescriptionSignal;
    }

    public function setDescriptionSignal(?string $DescriptionSignal): static
    {
        $this->DescriptionSignal = $DescriptionSignal;

        return $this;
    }

    public function getIndication(): ?string
    {
        return $this->Indication;
    }

    public function setIndication(?string $Indication): static
    {
        $this->Indication = $Indication;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->DateCreation;
    }

    public function setDateCreation(?\DateTimeImmutable $DateCreation): static
    {
        $this->DateCreation = $DateCreation;

        return $this;
    }

    public function getContexte(): ?string
    {
        return $this->Contexte;
    }

    public function setContexte(?string $Contexte): static
    {
        $this->Contexte = $Contexte;

        return $this;
    }

    public function getNiveauRisqueInitial(): ?string
    {
        return $this->NiveauRisqueInitial;
    }

    public function setNiveauRisqueInitial(?string $NiveauRisqueInitial): static
    {
        $this->NiveauRisqueInitial = $NiveauRisqueInitial;

        return $this;
    }

    public function getNiveauRisqueFinal(): ?string
    {
        return $this->NiveauRisqueFinal;
    }

    public function setNiveauRisqueFinal(?string $NiveauRisqueFinal): static
    {
        $this->NiveauRisqueFinal = $NiveauRisqueFinal;

        return $this;
    }

    public function getAnaRisqueComment(): ?string
    {
        return $this->AnaRisqueComment;
    }

    public function setAnaRisqueComment(?string $AnaRisqueComment): static
    {
        $this->AnaRisqueComment = $AnaRisqueComment;

        return $this;
    }

    public function getSourceSignal(): ?string
    {
        return $this->SourceSignal;
    }

    public function setSourceSignal(?string $SourceSignal): static
    {
        $this->SourceSignal = $SourceSignal;

        return $this;
    }

    public function getRefSignal(): ?string
    {
        return $this->RefSignal;
    }

    public function setRefSignal(?string $RefSignal): static
    {
        $this->RefSignal = $RefSignal;

        return $this;
    }

    public function getIdentifiantSource(): ?string
    {
        return $this->IdentifiantSource;
    }

    public function setIdentifiantSource(?string $IdentifiantSource): static
    {
        $this->IdentifiantSource = $IdentifiantSource;

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
            $releveDeDecision->setSignalLie($this);
        }

        return $this;
    }

    public function removeReleveDeDecision(ReleveDeDecision $releveDeDecision): static
    {
        if ($this->ReleveDeDecision->removeElement($releveDeDecision)) {
            // set the owning side to null (unless already changed)
            if ($releveDeDecision->getSignalLie() === $this) {
                $releveDeDecision->setSignalLie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReunionSignal>
     */
    public function getReunionSignals(): Collection
    {
        return $this->reunionSignals;
    }

    public function addReunionSignal(ReunionSignal $reunionSignal): static
    {
        if (!$this->reunionSignals->contains($reunionSignal)) {
            $this->reunionSignals->add($reunionSignal);
            $reunionSignal->addSignalLie($this);
        }

        return $this;
    }

    public function removeReunionSignal(ReunionSignal $reunionSignal): static
    {
        if ($this->reunionSignals->removeElement($reunionSignal)) {
            $reunionSignal->removeSignalLie($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Produits>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produits $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->setSignalLie($this);
        }

        return $this;
    }

    public function removeProduit(Produits $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getSignalLie() === $this) {
                $produit->setSignalLie(null);
            }
        }

        return $this;
    }

    // public function getStatutCreation(): ?string
    // {
    //     return $this->StatutCreation;
    // }

    // public function setStatutCreation(?string $StatutCreation): static
    // {
    //     $this->StatutCreation = $StatutCreation;

    //     return $this;
    // }

    // public function getTypeSignal(): ?string
    // {
    //     return $this->TypeSignal;
    // }

    // public function setTypeSignal(?string $TypeSignal): static
    // {
    //     $this->TypeSignal = $TypeSignal;

    //     return $this;
    // }

    public function getTypeSignal(): ?string
    {
        return $this->TypeSignal;
    }

    public function setTypeSignal(string $TypeSignal): static
    {
        $this->TypeSignal = $TypeSignal;

        return $this;
    }

    /**
     * @return Collection<int, StatutSignal>
     */
    public function getStatutSignals(): Collection
    {
        return $this->statutSignals;
    }

    public function addStatutSignal(StatutSignal $statutSignal): static
    {
        if (!$this->statutSignals->contains($statutSignal)) {
            $this->statutSignals->add($statutSignal);
            $statutSignal->setSignalLie($this);
        }

        return $this;
    }

    public function removeStatutSignal(StatutSignal $statutSignal): static
    {
        if ($this->statutSignals->removeElement($statutSignal)) {
            // set the owning side to null (unless already changed)
            if ($statutSignal->getSignalLie() === $this) {
                $statutSignal->setSignalLie(null);
            }
        }

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
            $mesuresRDD->setSignalLie($this);
        }

        return $this;
    }

    public function removeMesuresRDD(MesuresRDD $mesuresRDD): static
    {
        if ($this->mesuresRDDs->removeElement($mesuresRDD)) {
            // set the owning side to null (unless already changed)
            if ($mesuresRDD->getSignalLie() === $this) {
                $mesuresRDD->setSignalLie(null);
            }
        }

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
            $suivi->setSignalLie($this);
        }

        return $this;
    }

    public function removeSuivi(Suivi $suivi): static
    {
        if ($this->suivis->removeElement($suivi)) {
            // set the owning side to null (unless already changed)
            if ($suivi->getSignalLie() === $this) {
                $suivi->setSignalLie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DirectionPoleConcerne>
     */
    public function getDirectionPoleConcernes(): Collection
    {
        return $this->directionPoleConcernes;
    }

    public function addDirectionPoleConcerne(DirectionPoleConcerne $directionPoleConcerne): static
    {
        if (!$this->directionPoleConcernes->contains($directionPoleConcerne)) {
            $this->directionPoleConcernes->add($directionPoleConcerne);
            $directionPoleConcerne->addSignalLie($this);
        }

        return $this;
    }

    public function removeDirectionPoleConcerne(DirectionPoleConcerne $directionPoleConcerne): static
    {
        if ($this->directionPoleConcernes->removeElement($directionPoleConcerne)) {
            $directionPoleConcerne->removeSignalLie($this);
        }

        return $this;
    }
}
