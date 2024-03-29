<?php

namespace App\Entity;

use App\Repository\PassageCTPRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PassageCTPRepository::class)]
class PassageCTP
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Libelle;

    #[ORM\Column(type: 'boolean')]
    private $Actif;

    #[ORM\OneToMany(targetEntity: ReleveDecision::class, mappedBy: 'PassageCTP')]
    private $releveDecisions;

    public function __construct()
    {
        $this->releveDecisions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->Libelle;
    }

    public function setLibelle(string $Libelle): self
    {
        $this->Libelle = $Libelle;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->Actif;
    }

    public function setActif(bool $Actif): self
    {
        $this->Actif = $Actif;

        return $this;
    }

    /**
     * @return Collection|ReleveDecision[]
     */
    public function getReleveDecisions(): Collection
    {
        return $this->releveDecisions;
    }

    public function addReleveDecision(ReleveDecision $releveDecision): self
    {
        if (!$this->releveDecisions->contains($releveDecision)) {
            $this->releveDecisions[] = $releveDecision;
            $releveDecision->setPassageCTP($this);
        }

        return $this;
    }

    public function removeReleveDecision(ReleveDecision $releveDecision): self
    {
        if ($this->releveDecisions->removeElement($releveDecision)) {
            // set the owning side to null (unless already changed)
            if ($releveDecision->getPassageCTP() === $this) {
                $releveDecision->setPassageCTP(null);
            }
        }

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->Actif;
    }
}
