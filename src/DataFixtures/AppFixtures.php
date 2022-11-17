<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Rates;
use App\Entity\Restaurant;
use App\Entity\RestaurantOwner;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;
    private $userPasswordHasher;


    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->faker = Factory::create("fr_FR");
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {


        $admin = new User();
        $password = "boursettes";
        $adminEmail = "admin@gmail.com";
        $admin->setEmail($adminEmail)
            ->setPassword($this->userPasswordHasher->hashPassword($admin, $password))
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $user = new User();
        $password = "bourses";
        $userEmail = "user@gmail.com";
        $user->setEmail($userEmail)
            ->setPassword($this->userPasswordHasher->hashPassword($user, $password))
            ->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $restaurantOwner = new RestaurantOwner();
        $restaurantOwner->setRestaurantOwnerFirstName($this->faker->firstName())
            ->setRestaurantOwnerEmail($this->faker->email())
            ->setRestaurantOwnerLastName($this->faker->lastName())
            ->setRestaurantOwnerPassword($this->faker->password())
            ->setStatus($this->faker->randomElement(['true', 'false']));
        $manager->persist($restaurantOwner);

        $restaurant = new Restaurant();
        $restaurant->setRestaurantName($this->faker->company())
            ->setRestaurantDescription($this->faker->text(10))
            ->setRestaurantLatitude($this->faker->latitude())
            ->setRestaurantLongitude($this->faker->longitude())
            ->setRestaurantPhone($this->faker->optional($weight = 0.8)->phoneNumber())
            ->setRestaurantOwner($restaurantOwner)
            ->setStatus($this->faker->randomElement(['true', 'false']));
        $manager->persist($restaurant);

        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $password = $this->faker->password(8, 10);
            $userEmail = $this->faker->email();
            $user->setEmail($userEmail)
                ->setPassword($this->userPasswordHasher->hashPassword($user, $password))
                ->setRoles(['ROLE_USER']);
            $manager->persist($user);

            $rates = new Rates();
            $rates->setUser($user)
                ->setStarsNumber($this->faker->numberBetween(0, 5));
            $restaurant->addRate($rates);      
            $manager->persist($rates);      
        }

        for ($i = 0; $i < 100; $i++) {
            $restaurantOwner = new RestaurantOwner();
            $restaurantOwner->setRestaurantOwnerFirstName($this->faker->firstName())
                ->setRestaurantOwnerEmail($this->faker->email())
                ->setRestaurantOwnerLastName($this->faker->lastName())
                ->setRestaurantOwnerPassword($this->faker->password())
                ->setStatus($this->faker->randomElement(['true', 'false']));
            $manager->persist($restaurantOwner);

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
