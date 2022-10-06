<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Restaurant;
use Faker\Generator;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create("fr_FR");
    }

    public function load(ObjectManager $manager): void
    {
        
        for ($i = 0; $i < 100; $i++) {
            $restaurant = new Restaurant();
            $restaurant->setRestaurantName($this->faker->company())
                ->setRestaurantDescription($this->faker->text(10))
                ->setRestaurantLatitude($this->faker->latitude())
                ->setRestaurantLongitude($this->faker->longitude())
                ->setRestaurantPhone($this->faker->optional($weight= 0.8)->phoneNumber())
                ->setStatus(rand(0, 1));
            $manager->persist($restaurant);
        }
        $manager->flush();
    }
}
