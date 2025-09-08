<?php

// namespace App\Codex\Entity;
// namespace App\Entity;
namespace App\Codex\Entity;
// use App\Codex\Repository\VUUtilRepository;
// use App\Repository\VUUtilRepository;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping as ORM;
// use App\Repository\VUUtilRepository;
use App\Codex\Repository\VUUtilRepository ;

#[ORM\Entity(repositoryClass: VUUtilRepository::class)]
#[Index(name: "idx_code_vu", fields: ["codeVU"])]
#[Index(name: "idx_code_cis", fields: ["codeCIS"])]
#[Index(name: "idx_nom_vu", fields: ["nomVU"])]
#[Index(name: "idx_lib_rech_denomination", fields: ["libRechDenomination"])]

class VUUtil
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

    #[ORM\Column(type: 'string', length: 9, nullable: true)]
    private ?string $dbo_Autorisation_libAbr = null;

    #[ORM\Column(type: 'string', length: 9, nullable: true)]
    private ?string $dbo_ClasseATC_libAbr = null;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private ?string $dbo_ClasseATC_libCourt = null;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private ?string $libCourt = null;

    #[ORM\Column(type: 'string', length: 9, nullable: true)]
    private ?string $codeContact = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $nomContactLibra = null;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private ?string $adresseContact = null;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private ?string $adresseCompl = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $codePost = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $nomVille = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $telContact = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $faxContact = null;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private ?string $dbo_Pays_libCourt = null;

    #[ORM\Column(type: 'string', length: 9, nullable: true)]
    private ?string $dbo_StatutSpeci_libAbr = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $statutAbrege = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $codeActeur = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $codeTigre = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $nomActeurLong = null;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private ?string $adresseComplExpl = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $codePostExpl = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $nomVilleExpl = null;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private ?string $complement = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $fax = null;

    #[ORM\Column(type: 'string', length: 9, nullable: true)]
    private ?string $dbo_Pays_libAbr = null;

    #[ORM\Column(type: 'string', length: 9, nullable: true)]
    private ?string $codeProduit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libRechDenomination = null;

    #[ORM\Column(length: 8, nullable: true)]
    private ?string $codeVUPrinceps = null;

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

    public function getDboAutorisationLibAbr(): ?string
    {
        return $this->dbo_Autorisation_libAbr;
    }

    public function setDboAutorisationLibAbr(?string $dbo_Autorisation_libAbr): self
    {
        $this->dbo_Autorisation_libAbr = $dbo_Autorisation_libAbr;

        return $this;
    }

    public function getDboClasseATCLibAbr(): ?string
    {
        return $this->dbo_ClasseATC_libAbr;
    }

    public function setDboClasseATCLibAbr(?string $dbo_ClasseATC_libAbr): self
    {
        $this->dbo_ClasseATC_libAbr = $dbo_ClasseATC_libAbr;

        return $this;
    }

    public function getDboClasseATCLibCourt(): ?string
    {
        return $this->dbo_ClasseATC_libCourt;
    }

    public function setDboClasseATCLibCourt(?string $dbo_ClasseATC_libCourt): self
    {
        $this->dbo_ClasseATC_libCourt = $dbo_ClasseATC_libCourt;

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

    public function getCodeContact(): ?string
    {
        return $this->codeContact;
    }

    public function setCodeContact(?string $codeContact): self
    {
        $this->codeContact = $codeContact;

        return $this;
    }

    public function getNomContactLibra(): ?string
    {
        return $this->nomContactLibra;
    }

    public function setNomContactLibra(?string $nomContactLibra): self
    {
        $this->nomContactLibra = $nomContactLibra;

        return $this;
    }

    public function getAdresseContact(): ?string
    {
        return $this->adresseContact;
    }

    public function setAdresseContact(?string $adresseContact): self
    {
        $this->adresseContact = $adresseContact;

        return $this;
    }

    public function getAdresseCompl(): ?string
    {
        return $this->adresseCompl;
    }

    public function setAdresseCompl(?string $adresseCompl): self
    {
        $this->adresseCompl = $adresseCompl;

        return $this;
    }

    public function getCodePost(): ?string
    {
        return $this->codePost;
    }

    public function setCodePost(?string $codePost): self
    {
        $this->codePost = $codePost;

        return $this;
    }

    public function getNomVille(): ?string
    {
        return $this->nomVille;
    }

    public function setNomVille(?string $nomVille): self
    {
        $this->nomVille = $nomVille;

        return $this;
    }

    public function getTelContact(): ?string
    {
        return $this->telContact;
    }

    public function setTelContact(?string $telContact): self
    {
        $this->telContact = $telContact;

        return $this;
    }

    public function getFaxContact(): ?string
    {
        return $this->faxContact;
    }

    public function setFaxContact(?string $faxContact): self
    {
        $this->faxContact = $faxContact;

        return $this;
    }

    public function getDboPaysLibCourt(): ?string
    {
        return $this->dbo_Pays_libCourt;
    }

    public function setDboPaysLibCourt(?string $dbo_Pays_libCourt): self
    {
        $this->dbo_Pays_libCourt = $dbo_Pays_libCourt;

        return $this;
    }

    public function getDboStatutSpeciLibAbr(): ?string
    {
        return $this->dbo_StatutSpeci_libAbr;
    }

    public function setDboStatutSpeciLibAbr(?string $dbo_StatutSpeci_libAbr): self
    {
        $this->dbo_StatutSpeci_libAbr = $dbo_StatutSpeci_libAbr;

        return $this;
    }

    public function getStatutAbrege(): ?string
    {
        return $this->statutAbrege;
    }

    public function setStatutAbrege(?string $statutAbrege): self
    {
        $this->statutAbrege = $statutAbrege;

        return $this;
    }

    public function getCodeActeur(): ?int
    {
        return $this->codeActeur;
    }

    public function setCodeActeur(?int $codeActeur): self
    {
        $this->codeActeur = $codeActeur;

        return $this;
    }

    public function getCodeTigre(): ?int
    {
        return $this->codeTigre;
    }

    public function setCodeTigre(?int $codeTigre): self
    {
        $this->codeTigre = $codeTigre;

        return $this;
    }

    public function getNomActeurLong(): ?string
    {
        return $this->nomActeurLong;
    }

    public function setNomActeurLong(?string $nomActeurLong): self
    {
        $this->nomActeurLong = $nomActeurLong;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getAdresseComplExpl(): ?string
    {
        return $this->adresseComplExpl;
    }

    public function setAdresseComplExpl(?string $adresseComplExpl): self
    {
        $this->adresseComplExpl = $adresseComplExpl;

        return $this;
    }

    public function getCodePostExpl(): ?string
    {
        return $this->codePostExpl;
    }

    public function setCodePostExpl(?string $codePostExpl): self
    {
        $this->codePostExpl = $codePostExpl;

        return $this;
    }

    public function getNomVilleExpl(): ?string
    {
        return $this->nomVilleExpl;
    }

    public function setNomVilleExpl(?string $nomVilleExpl): self
    {
        $this->nomVilleExpl = $nomVilleExpl;

        return $this;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getDboPaysLibAbr(): ?string
    {
        return $this->dbo_Pays_libAbr;
    }

    public function setDboPaysLibAbr(?string $dbo_Pays_libAbr): self
    {
        $this->dbo_Pays_libAbr = $dbo_Pays_libAbr;

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

    public function getLibRechDenomination(): ?string
    {
        return $this->libRechDenomination;
    }

    public function setLibRechDenomination(?string $libRechDenomination): static
    {
        $this->libRechDenomination = $libRechDenomination;

        return $this;
    }

    public function getCodeVUPrinceps(): ?string
    {
        return $this->codeVUPrinceps;
    }

    public function setCodeVUPrinceps(?string $codeVUPrinceps): static
    {
        $this->codeVUPrinceps = $codeVUPrinceps;

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
