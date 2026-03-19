<?php

namespace App\DataFixtures;

use App\Entity\Newsletter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Enum\NewsletterStatus;
use Faker;
use Faker\Provider;
use App\Provider\Book;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();
        $faker->addProvider(new Book($faker));

        for ($i = 0; $i < 5; $i++) {
            $newsletter = new Newsletter();
            $newsletter->setName($faker->title());
            $newsletter->setDescription($faker->text());
            $newsletter->setStatus(NewsletterStatus::PUBLISHED);
            $manager->persist($newsletter);
        }

        $manager->flush();
    }
}
