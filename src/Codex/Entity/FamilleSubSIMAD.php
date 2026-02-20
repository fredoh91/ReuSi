<?php

// namespace App\Entity;
namespace App\Codex\Entity;

// use App\Repository\FamilleSubSIMADRepository;
use App\Codex\Repository\FamilleSubSIMADRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FamilleSubSIMADRepository::class)]
class FamilleSubSIMAD
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 170, nullable: true)]
    private ?string $productfamily = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TypSub = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Autre_txt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $FlActif = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductfamily(): ?string
    {
        return $this->productfamily;
    }

    public function setProductfamily(?string $productfamily): static
    {
        $this->productfamily = $productfamily;

        return $this;
    }

    public function getTypSub(): ?string
    {
        return $this->TypSub;
    }

    public function setTypSub(?string $TypSub): static
    {
        $this->TypSub = $TypSub;

        return $this;
    }

    public function getAutreTxt(): ?string
    {
        return $this->Autre_txt;
    }

    public function setAutreTxt(?string $Autre_txt): static
    {
        $this->Autre_txt = $Autre_txt;

        return $this;
    }

    public function isFlActif(): ?bool
    {
        return $this->FlActif;
    }

    public function setFlActif(?bool $FlActif): static
    {
        $this->FlActif = $FlActif;

        return $this;
    }
}
