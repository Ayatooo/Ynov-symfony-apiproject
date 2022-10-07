<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RestaurantRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['showRestaurants'])]
    private ?string $restaurantName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['showRestaurants'])]
    private ?string $restaurantLatitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['showRestaurants'])]
    private ?string $restaurantLongitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['showRestaurants'])]
    private ?string $restaurantDescription = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['showRestaurants'])]
    private ?string $restaurantPhone = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\ManyToOne(inversedBy: 'userRestaurant')]
    private ?Users $restaurantOwner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRestaurantName(): ?string
    {
        return $this->restaurantName;
    }

    public function setRestaurantName(string $restaurantName): self
    {
        $this->restaurantName = $restaurantName;

        return $this;
    }

    public function getRestaurantLatitude(): ?string
    {
        return $this->restaurantLatitude;
    }

    public function setRestaurantLatitude(?string $restaurantLatitude): self
    {
        $this->restaurantLatitude = $restaurantLatitude;

        return $this;
    }

    public function getRestaurantLongitude(): ?string
    {
        return $this->restaurantLongitude;
    }

    public function setRestaurantLongitude(?string $restaurantLongitude): self
    {
        $this->restaurantLongitude = $restaurantLongitude;

        return $this;
    }

    public function getRestaurantDescription(): ?string
    {
        return $this->restaurantDescription;
    }

    public function setRestaurantDescription(?string $restaurantDescription): self
    {
        $this->restaurantDescription = $restaurantDescription;

        return $this;
    }

    public function getRestaurantPhone(): ?string
    {
        return $this->restaurantPhone;
    }

    public function setRestaurantPhone(?string $restaurantPhone): self
    {
        $this->restaurantPhone = $restaurantPhone;

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

    public function getRestaurantOwner(): ?Users
    {
        return $this->restaurantOwner;
    }

    public function setRestaurantOwner(?Users $restaurantOwner): self
    {
        $this->restaurantOwner = $restaurantOwner;

        return $this;
    }
}
