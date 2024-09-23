<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $surname = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?bool $isVendor = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'sellerUsername', orphanRemoval: true)]
    private Collection $sellingProducts;

    /**
     * @var Collection<int, product>
     */
    #[ORM\OneToMany(targetEntity: product::class, mappedBy: 'cartedBy')]
    private Collection $cart;

    /**
     * @var Collection<int, Rating>
     */
    #[ORM\OneToMany(targetEntity: Rating::class, mappedBy: 'buyer')]
    private Collection $doneRatings;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user')]
    private Collection $orders;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $postalAddress = null;

    public function __construct()
    {
        $this->sellingProducts = new ArrayCollection();
        $this->cart = new ArrayCollection();
        $this->doneRatings = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isVendor(): ?bool
    {
        return $this->isVendor;
    }

    public function setVendor(bool $isVendor): static
    {
        $this->isVendor = $isVendor;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getSellingProducts(): Collection
    {
        return $this->sellingProducts;
    }

    public function addSellingProduct(Product $sellingProduct): static
    {
        if (!$this->sellingProducts->contains($sellingProduct)) {
            $this->sellingProducts->add($sellingProduct);
            $sellingProduct->setSellerUsername($this);
        }

        return $this;
    }

    public function removeSellingProduct(Product $sellingProduct): static
    {
        if ($this->sellingProducts->removeElement($sellingProduct)) {
            // set the owning side to null (unless already changed)
            if ($sellingProduct->getSellerUsername() === $this) {
                $sellingProduct->setSellerUsername(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, product>
     */
    public function getCart(): Collection
    {
        return $this->cart;
    }

    public function addCart(product $cart): static
    {
        if (!$this->cart->contains($cart)) {
            $this->cart->add($cart);
            $cart->setCartedBy($this);
        }

        return $this;
    }

    public function removeCart(product $cart): static
    {
        if ($this->cart->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getCartedBy() === $this) {
                $cart->setCartedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getDoneRatings(): Collection
    {
        return $this->doneRatings;
    }

    public function addDoneRating(Rating $doneRating): static
    {
        if (!$this->doneRatings->contains($doneRating)) {
            $this->doneRatings->add($doneRating);
            $doneRating->setBuyer($this);
        }

        return $this;
    }

    public function removeDoneRating(Rating $doneRating): static
    {
        if ($this->doneRatings->removeElement($doneRating)) {
            // set the owning side to null (unless already changed)
            if ($doneRating->getBuyer() === $this) {
                $doneRating->setBuyer(null);
            }
        }

        return $this;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }
    public function removeOrders(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    public function getPostalAddress(): ?string
    {
        return $this->postalAddress;
    }

    public function setPostalAddress(string $postalAddress): static
    {
        $this->postalAddress = $postalAddress;

        return $this;
    }
}
