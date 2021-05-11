<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Trick;
use Faker\Factory;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function __construct()
    {
        $this->slugger = new Slugify();
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $groupNames = ['straight air', 'grab', 'spin', 'flips and inverted rotations', 'inverted hand plants', 'slide', 'stall', 'tweaks and variations', 'other'];
        foreach ($groupNames as $name) {
            $group = new Group;
            $group
                ->setName(ucfirst($name))
                ->setSlug($this->slugger->slugify($group->getName()));

            $manager->persist($group);

            for ($t = 0; $t < mt_rand(mt_rand(0, 2), 5); $t++) {
                $trick = new Trick;
                $trick
                    ->setName(ucfirst($faker->words(mt_rand(1, 3), true)))
                    ->setSlug($this->slugger->slugify($trick->getName()))
                    ->setDescription($faker->paragraphs(mt_rand(1, 3), true))
                    ->setTrickGroup($group)
                    ->setCreatedAt($faker->dateTimeBetween('-2 weeks'))
                    ->setUpdatedAt($trick->getCreatedAt());
                $random = mt_rand(0, 3);
                if ($random >= 1) {
                    $trick->setUpdatedAt($faker->dateTimeBetween($trick->getUpdatedAt()));
                }
                $manager->persist($trick);
            }
        };

        $manager->flush();
    }
}
