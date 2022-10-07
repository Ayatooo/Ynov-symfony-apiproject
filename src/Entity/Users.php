<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['showUsers'])]
    private ?string $userFirstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['showUsers'])]
    private ?string $userLastName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['showUsers'])]
    private ?string $userEmail = null;

    #[ORM\Column(length: 255)]
    private ?string $userPassword = null;

    #[ORM\Column]
    private ?bool $status = null;

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

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
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
