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

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $color = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $material = null;

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

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'sellingProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $vendor = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $views = null;

    #[ORM\Column]
    private ?int $itemsSold = null;

    #[ORM\ManyToOne(inversedBy: 'cart')]
    private ?User $cartedBy = null;

    #[ORM\Column(length: 255)]
    private ?string $mainImage = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $otherImages = [];

    #[ORM\Column]
    private ?int $size = null;

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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getMaterial(): ?string
    {
        return $this->material;
    }

    public function setMaterial(?string $material): static
    {
        $this->material = $material;

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

    public function getvendor(): ?User
    {
        return $this->vendor;
    }

    public function setvendor(?User $vendor): static
    {
        $this->vendor = $vendor;

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

    public function decreaseQuantity(): ?int
    {
        return $this->quantity-=1;
    }

    public function increaseQuantity(): ?int
    {
        return $this->quantity+=1;
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


    public function increaseViews(): ?int
    {
        return $this->views+=1;
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

    public function increaseItemsSold(): ?int
    {
        return $this->itemsSold+=1;
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

    public function getMainImage(): ?string
    {
        return $this->mainImage;
    }

    public function setMainImage(string $mainImage): static
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    public function getOtherImages(): array
    {
        return $this->otherImages;
    }

    public function setOtherImages(array $otherImages): static
    {
        $this->otherImages = $otherImages;

        return $this;
    }


    public function addOtherImage(string $image): self
    {
        // Correctly add image to the array
        if (!in_array($image, $this->otherImages, true)) {
            $this->otherImages[] = $image; // Adds the image path as a string
        }
        
        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

}
