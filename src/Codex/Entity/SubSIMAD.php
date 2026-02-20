<?php

// namespace App\Entity;
namespace App\Codex\Entity;

// use App\Repository\SubSIMADRepository;
use App\Codex\Repository\SubSIMADRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubSIMADRepository::class)]
class SubSIMAD
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 170, nullable: true)]
    private ?string $productfamily = null;

    #[ORM\Column(length: 170, nullable: true)]
    private ?string $topproductname = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $productname = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $creation_date = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $modification_date = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $unii_id = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $cas_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $is_product_enabled = null;

    #[ORM\Column(nullable: true)]
    private ?int $productPV = null;

    #[ORM\Column(nullable: true)]
    private ?int $productADDICTO = null;

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

    public function getTopproductname(): ?string
    {
        return $this->topproductname;
    }

    public function setTopproductname(?string $topproductname): static
    {
        $this->topproductname = $topproductname;

        return $this;
    }

    public function getProductname(): ?string
    {
        return $this->productname;
    }

    public function setProductname(?string $productname): static
    {
        $this->productname = $productname;

        return $this;
    }

    public function getCreationDate(): ?string
    {
        return $this->creation_date;
    }

    public function setCreationDate(?string $creation_date): static
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getModificationDate(): ?string
    {
        return $this->modification_date;
    }

    public function setModificationDate(?string $modification_date): static
    {
        $this->modification_date = $modification_date;

        return $this;
    }

    public function getUniiId(): ?string
    {
        return $this->unii_id;
    }

    public function setUniiId(?string $unii_id): static
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

    public function getIsProductEnabled(): ?int
    {
        return $this->is_product_enabled;
    }

    public function setIsProductEnabled(?int $is_product_enabled): static
    {
        $this->is_product_enabled = $is_product_enabled;

        return $this;
    }

    public function getProductPV(): ?int
    {
        return $this->productPV;
    }

    public function setProductPV(?int $productPV): static
    {
        $this->productPV = $productPV;

        return $this;
    }

    public function getProductADDICTO(): ?int
    {
        return $this->productADDICTO;
    }

    public function setProductADDICTO(?int $productADDICTO): static
    {
        $this->productADDICTO = $productADDICTO;

        return $this;
    }
}
