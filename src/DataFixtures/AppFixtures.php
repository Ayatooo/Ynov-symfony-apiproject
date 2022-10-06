<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Restaurant;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < 100; $i++) {
            $restaurant = new Restaurant();
            $restaurant->setRestaurantName('Restaurant'.$i);
            $restaurant->setRestaurantDescription(
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                Nulla vitae elit libero, a pharetra augue.'
            );
            $restaurant->setRestaurantLatitude((string) rand(0, 100) . '.' . rand(0, 100));
            $restaurant->setRestaurantLongitude((string) rand(0, 100) . '.' . rand(0, 100));
            $restaurant->setRestaurantPhone(
                '0' . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9)
            );
            $restaurant->setStatus((bool) rand(0, 1));
            $manager->persist($restaurant);
        }
        $manager->flush();
    }
}
