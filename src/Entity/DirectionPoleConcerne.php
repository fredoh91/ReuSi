<?php

namespace App\Entity;

use App\Repository\DirectionPoleConcerneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DirectionPoleConcerneRepository::class)]
class DirectionPoleConcerne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Direction = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $PoleCourt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $PoleLong = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $PoleTresCourt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $Inactif = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $OrdreTri = null;

    /**
     * @var Collection<int, Signal>
     */
    #[ORM\ManyToMany(targetEntity: Signal::class, mappedBy: 'directionPoleConcernes')]
    private Collection $signaux;

    public function __construct()
    {
        $this->signaux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDirection(): ?string
    {
        return $this->Direction;
    }

    public function setDirection(?string $Direction): static
    {
        $this->Direction = $Direction;

        return $this;
    }

    public function getPoleCourt(): ?string
    {
        return $this->PoleCourt;
    }

    public function setPoleCourt(?string $PoleCourt): static
    {
        $this->PoleCourt = $PoleCourt;

        return $this;
    }

    public function getPoleLong(): ?string
    {
        return $this->PoleLong;
    }

    public function setPoleLong(?string $PoleLong): static
    {
        $this->PoleLong = $PoleLong;

        return $this;
    }

    public function getPoleTresCourt(): ?string
    {
        return $this->PoleTresCourt;
    }

    public function setPoleTresCourt(?string $PoleTresCourt): static
    {
        $this->PoleTresCourt = $PoleTresCourt;

        return $this;
    }

    public function isInactif(): ?bool
    {
        return $this->Inactif;
    }

    public function setInactif(?bool $Inactif): static
    {
        $this->Inactif = $Inactif;

        return $this;
    }

    public function getOrdreTri(): ?int
    {
        return $this->OrdreTri;
    }

    public function setOrdreTri(?int $OrdreTri): static
    {
        $this->OrdreTri = $OrdreTri;

        return $this;
    }

    /**
     * @return Collection<int, Signal>
     */
    public function getSignaux(): Collection
    {
        return $this->signaux;
    }

    public function addSignal(Signal $signal): self
    {
        if (!$this->signaux->contains($signal)) {
            $this->signaux[] = $signal;
            $signal->addDirectionPoleConcerne($this);
        }

        return $this;
    }

    public function removeSignal(Signal $signal): self
    {
        if ($this->signaux->removeElement($signal)) {
            $signal->removeDirectionPoleConcerne($this);
        }

        return $this;
    }
}
