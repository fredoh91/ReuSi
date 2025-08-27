<?php

// namespace App\Codex\Entity;
// namespace App\Entity;
namespace App\Codex\Entity;

// use App\Codex\Repository\CODEXPresentationRepository;
use App\Codex\Repository\CODEXPresentationRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

#[ORM\Entity(repositoryClass: CODEXPresentationRepository::class)]
#[Index(name: "idx_code_vu", fields: ["codeVU"])]
class CODEXPresentation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $codeVU = null;

    #[ORM\Column(nullable: true)]
    private ?int $numPresentation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomPresentation = null;

    #[ORM\Column(nullable: true)]
    private ?int $codeCIP = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeCIP13 = null;

    #[ORM\Column(nullable: true)]
    private ?int $statutComm = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $infoCommCourt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $infoCommLong = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeVU(): ?string
    {
        return $this->codeVU;
    }

    public function setCodeVU(?string $codeVU): static
    {
        $this->codeVU = $codeVU;

        return $this;
    }

    public function getNumPresentation(): ?int
    {
        return $this->numPresentation;
    }

    public function setNumPresentation(?int $numPresentation): static
    {
        $this->numPresentation = $numPresentation;

        return $this;
    }

    public function getNomPresentation(): ?string
    {
        return $this->nomPresentation;
    }

    public function setNomPresentation(?string $nomPresentation): static
    {
        $this->nomPresentation = $nomPresentation;

        return $this;
    }

    public function getCodeCIP(): ?int
    {
        return $this->codeCIP;
    }

    public function setCodeCIP(?int $codeCIP): static
    {
        $this->codeCIP = $codeCIP;

        return $this;
    }

    public function getCodeCIP13(): ?string
    {
        return $this->codeCIP13;
    }

    public function setCodeCIP13(?string $codeCIP13): static
    {
        $this->codeCIP13 = $codeCIP13;

        return $this;
    }

    public function getStatutComm(): ?int
    {
        return $this->statutComm;
    }

    public function setStatutComm(?int $statutComm): static
    {
        $this->statutComm = $statutComm;

        return $this;
    }

    public function getInfoCommCourt(): ?string
    {
        return $this->infoCommCourt;
    }

    public function setInfoCommCourt(?string $infoCommCourt): static
    {
        $this->infoCommCourt = $infoCommCourt;

        return $this;
    }

    public function getInfoCommLong(): ?string
    {
        return $this->infoCommLong;
    }

    public function setInfoCommLong(?string $infoCommLong): static
    {
        $this->infoCommLong = $infoCommLong;

        return $this;
    }
}
