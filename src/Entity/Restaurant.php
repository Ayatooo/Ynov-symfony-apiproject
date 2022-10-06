<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $restaurantName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $restaurantLatitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $restaurantLongitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $restaurantDescription = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $restaurantPhone = null;

    #[ORM\Column]
    private ?bool $status = null;

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
}
