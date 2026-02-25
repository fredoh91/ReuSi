<?php

namespace App\Entity;

use App\Repository\ProduitsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitsRepository::class)]
class Produits
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Denomination = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $DCI = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Dosage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Voie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CodeATC = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $LibATC = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TypeProcedure = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CodeCIS = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CodeVU = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CodeDossier = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NomVU = null;

    #[ORM\Column(nullable: true)]
    private ?bool $Codex = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Laboratoire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idLaboratoire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AdresseContact = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AdresseCompl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CodePost = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NomVille = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TelContact = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $FaxContact = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $DboPaysLibAbr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Titulaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idTitulaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AdresseComplExpl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CodePostExpl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NomVilleExpl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Complement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Fax = null;

    #[ORM\Column(nullable: true)]
    private ?bool $MedicAccesLibre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $PrescriptionDelivrance = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?Signal $SignalLie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $StatutActifSpecialite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $UserCreate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $UserModif = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $UpdatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomProduit = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $typeSubstance = null;

    #[ORM\Column(length: 170, nullable: true)]
    private ?string $productFamily = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $topProductName = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $unii_id = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $cas_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenomination(): ?string
    {
        return $this->Denomination;
    }

    public function setDenomination(?string $Denomination): static
    {
        $this->Denomination = $Denomination;

        return $this;
    }

    public function getDCI(): ?string
    {
        return $this->DCI;
    }

    public function setDCI(?string $DCI): static
    {
        $this->DCI = $DCI;

        return $this;
    }

    public function getDosage(): ?string
    {
        return $this->Dosage;
    }

    public function setDosage(?string $Dosage): static
    {
        $this->Dosage = $Dosage;

        return $this;
    }

    public function getVoie(): ?string
    {
        return $this->Voie;
    }

    public function setVoie(?string $Voie): static
    {
        $this->Voie = $Voie;

        return $this;
    }

    public function getLaboratoire(): ?string
    {
        return $this->Laboratoire;
    }

    public function setLaboratoire(?string $Laboratoire): static
    {
        $this->Laboratoire = $Laboratoire;

        return $this;
    }

    public function getIdLaboratoire(): ?string
    {
        return $this->idLaboratoire;
    }

    public function setIdLaboratoire(?string $idLaboratoire): static
    {
        $this->idLaboratoire = $idLaboratoire;

        return $this;
    }

    public function getTypeProcedure(): ?string
    {
        return $this->TypeProcedure;
    }

    public function setTypeProcedure(?string $TypeProcedure): static
    {
        $this->TypeProcedure = $TypeProcedure;

        return $this;
    }

    public function getCodeCIS(): ?string
    {
        return $this->CodeCIS;
    }

    public function setCodeCIS(?string $CodeCIS): static
    {
        $this->CodeCIS = $CodeCIS;

        return $this;
    }

    public function getCodeVU(): ?string
    {
        return $this->CodeVU;
    }

    public function setCodeVU(?string $CodeVU): static
    {
        $this->CodeVU = $CodeVU;

        return $this;
    }

    public function getCodeDossier(): ?string
    {
        return $this->CodeDossier;
    }

    public function setCodeDossier(?string $CodeDossier): static
    {
        $this->CodeDossier = $CodeDossier;

        return $this;
    }

    public function getNomVU(): ?string
    {
        return $this->NomVU;
    }

    public function setNomVU(?string $NomVU): static
    {
        $this->NomVU = $NomVU;

        return $this;
    }

    public function isCodex(): ?bool
    {
        return $this->Codex;
    }

    public function setCodex(?bool $Codex): static
    {
        $this->Codex = $Codex;

        return $this;
    }

    public function getTitulaire(): ?string
    {
        return $this->Titulaire;
    }

    public function setTitulaire(?string $Titulaire): static
    {
        $this->Titulaire = $Titulaire;

        return $this;
    }

    public function getIdTitulaire(): ?string
    {
        return $this->idTitulaire;
    }

    public function setIdTitulaire(?string $idTitulaire): static
    {
        $this->idTitulaire = $idTitulaire;

        return $this;
    }

    public function getAdresseContact(): ?string
    {
        return $this->AdresseContact;
    }

    public function setAdresseContact(?string $AdresseContact): static
    {
        $this->AdresseContact = $AdresseContact;

        return $this;
    }

    public function getAdresseCompl(): ?string
    {
        return $this->AdresseCompl;
    }

    public function setAdresseCompl(?string $AdresseCompl): static
    {
        $this->AdresseCompl = $AdresseCompl;

        return $this;
    }

    public function getCodePost(): ?string
    {
        return $this->CodePost;
    }

    public function setCodePost(?string $CodePost): static
    {
        $this->CodePost = $CodePost;

        return $this;
    }

    public function getNomVille(): ?string
    {
        return $this->NomVille;
    }

    public function setNomVille(?string $NomVille): static
    {
        $this->NomVille = $NomVille;

        return $this;
    }

    public function getTelContact(): ?string
    {
        return $this->TelContact;
    }

    public function setTelContact(?string $TelContact): static
    {
        $this->TelContact = $TelContact;

        return $this;
    }

    public function getFaxContact(): ?string
    {
        return $this->FaxContact;
    }

    public function setFaxContact(?string $FaxContact): static
    {
        $this->FaxContact = $FaxContact;

        return $this;
    }

    public function getDboPaysLibAbr(): ?string
    {
        return $this->DboPaysLibAbr;
    }

    public function setDboPaysLibAbr(?string $DboPaysLibAbr): static
    {
        $this->DboPaysLibAbr = $DboPaysLibAbr;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(?string $Adresse): static
    {
        $this->Adresse = $Adresse;

        return $this;
    }

    public function getAdresseComplExpl(): ?string
    {
        return $this->AdresseComplExpl;
    }

    public function setAdresseComplExpl(?string $AdresseComplExpl): static
    {
        $this->AdresseComplExpl = $AdresseComplExpl;

        return $this;
    }

    public function getCodePostExpl(): ?string
    {
        return $this->CodePostExpl;
    }

    public function setCodePostExpl(?string $CodePostExpl): static
    {
        $this->CodePostExpl = $CodePostExpl;

        return $this;
    }

    public function getNomVilleExpl(): ?string
    {
        return $this->NomVilleExpl;
    }

    public function setNomVilleExpl(?string $NomVilleExpl): static
    {
        $this->NomVilleExpl = $NomVilleExpl;

        return $this;
    }

    public function getComplement(): ?string
    {
        return $this->Complement;
    }

    public function setComplement(?string $Complement): static
    {
        $this->Complement = $Complement;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->Tel;
    }

    public function setTel(?string $Tel): static
    {
        $this->Tel = $Tel;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->Fax;
    }

    public function setFax(?string $Fax): static
    {
        $this->Fax = $Fax;

        return $this;
    }

    public function getCodeATC(): ?string
    {
        return $this->CodeATC;
    }

    public function setCodeATC(?string $CodeATC): static
    {
        $this->CodeATC = $CodeATC;

        return $this;
    }

    public function getLibATC(): ?string
    {
        return $this->LibATC;
    }

    public function setLibATC(?string $LibATC): static
    {
        $this->LibATC = $LibATC;

        return $this;
    }

    public function isMedicAccesLibre(): ?bool
    {
        return $this->MedicAccesLibre;
    }

    public function setMedicAccesLibre(?bool $MedicAccesLibre): static
    {
        $this->MedicAccesLibre = $MedicAccesLibre;

        return $this;
    }

    public function getPrescriptionDelivrance(): ?string
    {
        return $this->PrescriptionDelivrance;
    }

    public function setPrescriptionDelivrance(?string $PrescriptionDelivrance): static
    {
        $this->PrescriptionDelivrance = $PrescriptionDelivrance;

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

    public function getStatutActifSpecialite(): ?string
    {
        return $this->StatutActifSpecialite;
    }

    public function setStatutActifSpecialite(?string $StatutActifSpecialite): static
    {
        $this->StatutActifSpecialite = $StatutActifSpecialite;

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

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(?string $nomProduit): static
    {
        $this->nomProduit = $nomProduit;

        return $this;
    }

    public function getTypeSubstance(): ?string
    {
        return $this->typeSubstance;
    }

    public function setTypeSubstance(?string $typeSubstance): static
    {
        $this->typeSubstance = $typeSubstance;

        return $this;
    }

    public function getProductFamily(): ?string
    {
        return $this->productFamily;
    }

    public function setProductFamily(?string $productFamily): static
    {
        $this->productFamily = $productFamily;

        return $this;
    }

    public function getTopProductName(): ?string
    {
        return $this->topProductName;
    }

    public function setTopProductName(?string $topProductName): static
    {
        $this->topProductName = $topProductName;

        return $this;
    }

    public function getUniiId(): ?string
    {
        return $this->unii_id;
    }

    public function setUniiId(string $unii_id): static
    {
        $this->unii_id = $unii_id;

        return $this;
    }

    public function getCasId(): ?string
    {
        return $this->cas_id;
    }

    public function setCasId(?string $cas_id): static
    {
        $this->cas_id = $cas_id;

        return $this;
    }
}
