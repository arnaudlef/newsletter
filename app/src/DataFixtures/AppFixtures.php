<?php

namespace App\DataFixtures;

use App\Entity\Newsletter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $faker->addProvider(new \Faker\Provider\Book($faker));

        for ($i = 0; $i < 5; $i++) {
            $newsletter = new Newsletter();
            $newsletter->setName($faker->title());
            $newsletter->setDescription($faker->text());
            $manager->persist($newsletter);
        }

        $manager->flush();
    }
}
