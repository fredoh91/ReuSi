<?php

namespace App\Entity;

use App\Repository\ListeMesuresRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ListeMesuresRepository::class)]
class ListeMesures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $LibMesure = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $DesactivateAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $OrdreTriListe = null;

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

    public function getDesactivateAt(): ?\DateTimeImmutable
    {
        return $this->DesactivateAt;
    }

    public function setDesactivateAt(?\DateTimeImmutable $DesactivateAt): static
    {
        $this->DesactivateAt = $DesactivateAt;

        return $this;
    }

    public function getOrdreTriListe(): ?int
    {
        return $this->OrdreTriListe;
    }

    public function setOrdreTriListe(?int $OrdreTriListe): static
    {
        $this->OrdreTriListe = $OrdreTriListe;

        return $this;
    }
}
