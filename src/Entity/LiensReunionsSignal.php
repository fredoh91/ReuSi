<?php

namespace App\Entity;

use App\Repository\LiensReunionsSignalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LiensReunionsSignalRepository::class)]
class LiensReunionsSignal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle = null;

    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'liensReunionsSignals')]
    #[ORM\JoinColumn(nullable: false)] 
    private ?ReunionSignal $reunionSignal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

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
}
