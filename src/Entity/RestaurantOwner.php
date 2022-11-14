<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RestaurantOwnerRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RestaurantOwnerRepository::class)]
class RestaurantOwner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, minMessage: 'Le prénom doit faire au moins 3 caractères')]
    #[ORM\Column(length: 255)]
    #[Groups(['showUsers', 'showRestaurants'])]
    private ?string $restaurantOwnerFirstName = null;

    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, minMessage: 'Le nom doit faire au moins 3 caractères')]
    #[ORM\Column(length: 255)]
    #[Groups(['showUsers', 'showRestaurants'])]
    private ?string $restaurantOwnerLastName = null;

    #[Assert\NotBlank(message: 'Le mail est obligatoire')]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, minMessage: 'Le mail doit faire au moins 3 caractères')]
    #[ORM\Column(length: 255)]
    #[Groups(['showUsers', 'showRestaurants'])]
    private ?string $restaurantOwnerEmail = null;

    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire')]
    #[Assert\NotNull()]
    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit faire au moins 8 caractères')]
    #[ORM\Column(length: 255)]
    private ?string $restaurantOwnerPassword = null;

    #[Assert\Choice(choices: ["true", "false"], message: 'Le statut doit être true ou false')]
    #[ORM\Column(length: 255, nullable: false)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'restaurantOwner', targetEntity: Restaurant::class)]
    private Collection $userRestaurant;

    public function __construct()
    {
        $this->userRestaurant = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRestaurantOwnerFirstName(): ?string
    {
        return $this->restaurantOwnerFirstName;
    }

    public function setRestaurantOwnerFirstName(string $restaurantOwnerFirstName): self
    {
        $this->restaurantOwnerFirstName = $restaurantOwnerFirstName;

        return $this;
    }

    public function getRestaurantOwnerLastName(): ?string
    {
        return $this->restaurantOwnerLastName;
    }

    public function setRestaurantOwnerLastName(string $restaurantOwnerLastName): self
    {
        $this->restaurantOwnerLastName = $restaurantOwnerLastName;

        return $this;
    }

    public function getRestaurantOwnerEmail(): ?string
    {
        return $this->restaurantOwnerEmail;
    }

    public function setRestaurantOwnerEmail(string $restaurantOwnerEmail): self
    {
        $this->restaurantOwnerEmail = $restaurantOwnerEmail;

        return $this;
    }

    public function getRestaurantOwnerPassword(): ?string
    {
        return $this->restaurantOwnerPassword;
    }

    public function setRestaurantOwnerPassword(string $restaurantOwnerPassword): self
    {
        $this->restaurantOwnerPassword = $restaurantOwnerPassword;

        return $this;
    }

    public function isStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Restaurant>
     */
    public function getUserRestaurant(): Collection
    {
        return $this->userRestaurant;
    }

    public function addUserRestaurant(Restaurant $userRestaurant): self
    {
        if (!$this->userRestaurant->contains($userRestaurant)) {
            $this->userRestaurant->add($userRestaurant);
            $userRestaurant->setRestaurantOwner($this);
        }

        return $this;
    }

    public function removeUserRestaurant(Restaurant $userRestaurant): self
    {
        if ($this->userRestaurant->removeElement($userRestaurant)) {
            // set the owning side to null (unless already changed)
            if ($userRestaurant->getRestaurantOwner() === $this) {
                $userRestaurant->setRestaurantOwner(null);
            }
        }

        return $this;
    }
}
