<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $model = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $colors = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $materials = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $brand = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(nullable: true)]
    private ?bool $forKids = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'sellingProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sellerUsername = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $views = null;

    #[ORM\Column]
    private ?int $itemsSold = null;

    #[ORM\ManyToOne(inversedBy: 'cart')]
    private ?User $cartedBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getColors(): ?array
    {
        return $this->colors;
    }

    public function setColors(?array $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function getMaterials(): ?array
    {
        return $this->materials;
    }

    public function setMaterials(?array $materials): static
    {
        $this->materials = $materials;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function isForKids(): ?bool
    {
        return $this->forKids;
    }

    public function setForKids(?bool $forKids): static
    {
        $this->forKids = $forKids;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSellerUsername(): ?User
    {
        return $this->sellerUsername;
    }

    public function setSellerUsername(?User $sellerUsername): static
    {
        $this->sellerUsername = $sellerUsername;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): static
    {
        $this->views = $views;

        return $this;
    }

    public function getItemsSold(): ?int
    {
        return $this->itemsSold;
    }

    public function setItemsSold(int $itemsSold): static
    {
        $this->itemsSold = $itemsSold;

        return $this;
    }

    public function getCartedBy(): ?User
    {
        return $this->cartedBy;
    }

    public function setCartedBy(?User $cartedBy): static
    {
        $this->cartedBy = $cartedBy;

        return $this;
    }
}
