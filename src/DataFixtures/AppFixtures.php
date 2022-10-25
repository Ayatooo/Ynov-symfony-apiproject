<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Users;
use App\Entity\Restaurant;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

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
            $restaurantOwner = new Users();
            $restaurantOwner->setUserFirstName($this->faker->firstName())
                ->setUserEmail($this->faker->email())
                ->setUserLastName($this->faker->lastName())
                ->setUserPassword($this->faker->password())
                ->setStatus($this->faker->randomElement(['true', 'false']));
            $manager->persist($restaurantOwner);

            $user = new User();
            $user->setEmail($this->faker->email())
                ->setPassword($this->faker->password());
            $manager->persist($user);

            $restaurant = new Restaurant();
            $restaurant->setRestaurantName($this->faker->company())
                ->setRestaurantDescription($this->faker->text(10))
                ->setRestaurantLatitude($this->faker->latitude())
                ->setRestaurantLongitude($this->faker->longitude())
                ->setRestaurantPhone($this->faker->optional($weight = 0.8)->phoneNumber())
                ->setRestaurantOwner($restaurantOwner)
                ->setStatus($this->faker->randomElement(['true', 'false']));
            $manager->persist($restaurant);
        }
        $manager->flush();
    }
}
