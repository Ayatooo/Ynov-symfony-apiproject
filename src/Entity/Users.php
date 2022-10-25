<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, minMessage: 'Le prénom doit faire au moins 3 caractères')]
    #[ORM\Column(length: 255)]
    #[Groups(['showUsers'])]
    private ?string $userFirstName = null;

    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, minMessage: 'Le nom doit faire au moins 3 caractères')]
    #[ORM\Column(length: 255)]
    #[Groups(['showUsers'])]
    private ?string $userLastName = null;

    #[Assert\NotBlank(message: 'Le mail est obligatoire')]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, minMessage: 'Le mail doit faire au moins 3 caractères')]
    #[ORM\Column(length: 255)]
    #[Groups(['showUsers'])]
    private ?string $userEmail = null;

    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire')]
    #[Assert\NotNull()]
    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit faire au moins 8 caractères')]
    #[ORM\Column(length: 255)]
    private ?string $userPassword = null;

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

    public function getUserFirstName(): ?string
    {
        return $this->userFirstName;
    }

    public function setUserFirstName(string $userFirstName): self
    {
        $this->userFirstName = $userFirstName;

        return $this;
    }

    public function getUserLastName(): ?string
    {
        return $this->userLastName;
    }

    public function setUserLastName(string $userLastName): self
    {
        $this->userLastName = $userLastName;

        return $this;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): self
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    public function getUserPassword(): ?string
    {
        return $this->userPassword;
    }

    public function setUserPassword(string $userPassword): self
    {
        $this->userPassword = $userPassword;

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
