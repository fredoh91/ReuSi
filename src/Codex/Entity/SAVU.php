<?php

// namespace App\Codex\Entity;
// namespace App\Entity;
namespace App\Codex\Entity;

// use App\Codex\Repository\SAVURepository;
// use App\Repository\SAVURepository;
use App\Codex\Repository\SAVURepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

#[ORM\Entity(repositoryClass: SAVURepository::class)]
#[Index(name: "idx_code_vu", fields: ["codeVU"])]
#[Index(name: "idx_code_cis", fields: ["codeCIS"])]
#[Index(name: "idx_nom_vu", fields: ["nomVU"])]
#[Index(name: "idx_lib_rech_denomination", fields: ["libRechDenomination"])]
#[Index(name: "idx_nom_substance", fields: ["nomSubstance"])]
#[Index(name: "idx_lib_rech_substance", fields: ["libRechSubstance"])]
class SAVU
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 8, nullable: true)]
    private ?string $codeVU = null;

    #[ORM\Column(type: 'string', length: 8, nullable: true)]
    private ?string $codeCIS = null;

    #[ORM\Column(type: 'string', length: 12, nullable: true)]
    private ?string $codeDossier = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $nomVU = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numElement = null;

    #[ORM\Column(type: 'string', length: 9, nullable: true)]
    private ?string $codeSubstance = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numComposant = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $codeUniteDosage = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $codeNature = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $dosageLibraTypo = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $dosageLibra = null;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private ?string $libCourt = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $nomSubstance = null;

    #[ORM\Column(type: 'string', length: 9, nullable: true)]
    private ?string $codeProduit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libNature = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libFormePH = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libRechSubstance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libRechDenomination = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomProduit = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeVU(): ?string
    {
        return $this->codeVU;
    }

    public function setCodeVU(?string $codeVU): self
    {
        $this->codeVU = $codeVU;

        return $this;
    }

    public function getCodeCIS(): ?string
    {
        return $this->codeCIS;
    }

    public function setCodeCIS(?string $codeCIS): self
    {
        $this->codeCIS = $codeCIS;

        return $this;
    }

    public function getCodeDossier(): ?string
    {
        return $this->codeDossier;
    }

    public function setCodeDossier(?string $codeDossier): self
    {
        $this->codeDossier = $codeDossier;

        return $this;
    }

    public function getNomVU(): ?string
    {
        return $this->nomVU;
    }

    public function setNomVU(?string $nomVU): self
    {
        $this->nomVU = $nomVU;

        return $this;
    }

    public function getNumElement(): ?int
    {
        return $this->numElement;
    }

    public function setNumElement(?int $numElement): self
    {
        $this->numElement = $numElement;

        return $this;
    }

    public function getCodeSubstance(): ?string
    {
        return $this->codeSubstance;
    }

    public function setCodeSubstance(?string $codeSubstance): self
    {
        $this->codeSubstance = $codeSubstance;

        return $this;
    }

    public function getNumComposant(): ?int
    {
        return $this->numComposant;
    }

    public function setNumComposant(?int $numComposant): self
    {
        $this->numComposant = $numComposant;

        return $this;
    }

    public function getCodeUniteDosage(): ?int
    {
        return $this->codeUniteDosage;
    }

    public function setCodeUniteDosage(?int $codeUniteDosage): self
    {
        $this->codeUniteDosage = $codeUniteDosage;

        return $this;
    }

    public function getCodeNature(): ?int
    {
        return $this->codeNature;
    }

    public function setCodeNature(?int $codeNature): self
    {
        $this->codeNature = $codeNature;

        return $this;
    }

    public function getDosageLibraTypo(): ?string
    {
        return $this->dosageLibraTypo;
    }

    public function setDosageLibraTypo(?string $dosageLibraTypo): self
    {
        $this->dosageLibraTypo = $dosageLibraTypo;

        return $this;
    }

    public function getDosageLibra(): ?string
    {
        return $this->dosageLibra;
    }

    public function setDosageLibra(?string $dosageLibra): self
    {
        $this->dosageLibra = $dosageLibra;

        return $this;
    }

    public function getLibCourt(): ?string
    {
        return $this->libCourt;
    }

    public function setLibCourt(?string $libCourt): self
    {
        $this->libCourt = $libCourt;

        return $this;
    }

    public function getNomSubstance(): ?string
    {
        return $this->nomSubstance;
    }

    public function setNomSubstance(?string $nomSubstance): self
    {
        $this->nomSubstance = $nomSubstance;

        return $this;
    }

    public function getCodeProduit(): ?string
    {
        return $this->codeProduit;
    }

    public function setCodeProduit(?string $codeProduit): self
    {
        $this->codeProduit = $codeProduit;

        return $this;
    }

    public function getLibNature(): ?string
    {
        return $this->libNature;
    }

    public function setLibNature(?string $libNature): static
    {
        $this->libNature = $libNature;

        return $this;
    }

    public function getLibFormePH(): ?string
    {
        return $this->libFormePH;
    }

    public function setLibFormePH(?string $libFormePH): static
    {
        $this->libFormePH = $libFormePH;

        return $this;
    }

    public function getLibRechSubstance(): ?string
    {
        return $this->libRechSubstance;
    }

    public function setLibRechSubstance(?string $libRechSubstance): static
    {
        $this->libRechSubstance = $libRechSubstance;

        return $this;
    }

    public function getLibRechDenomination(): ?string
    {
        return $this->libRechDenomination;
    }

    public function setLibRechDenomination(?string $libRechDenomination): static
    {
        $this->libRechDenomination = $libRechDenomination;

        return $this;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(?string $nomProduit): static
    {
        $this->nomProduit = $nomProduit;

        return $this;
    }
}
