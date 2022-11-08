<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RestaurantRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Le nom du restaurant est obligatoire')]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, minMessage: 'Le nom du restaurant doit faire au moins 3 caractères')]
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

    #[Assert\Length(max: 20, maxMessage: 'Le téléphone ne doit pas faire plus de 20 caractères')]
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['showRestaurants'])]
    private ?string $restaurantPhone = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['showRestaurants'])]
    private ?float $restaurantDistance = null;

    #[Assert\Choice(choices: ["true", "false"], message: 'Le statut doit être true ou false')]
    #[ORM\Column(length: 255, nullable: false)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'userRestaurant')]
    private ?RestaurantOwner $restaurantOwner = null;

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

    public function isStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRestaurantDistance(): ?float
    {
        return $this->restaurantDistance;
    }

    public function setRestaurantDistance(?float $restaurantDistance): self
    {
        $this->restaurantDistance = $restaurantDistance;

        return $this;
    }

    public function getRestaurantOwner(): ?RestaurantOwner
    {
        return $this->restaurantOwner;
    }

    public function setRestaurantOwner(?RestaurantOwner $restaurantOwner): self
    {
        $this->restaurantOwner = $restaurantOwner;

        return $this;
    }
}
