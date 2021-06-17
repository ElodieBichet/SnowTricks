<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Group;
use App\Entity\Trick;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrickFixtures extends Fixture
{
    protected $slugger;

    public function __construct()
    {
        $this->slugger = new Slugify();
    }

    protected function getGroups(): array
    {
        $groupNames = [
            'straight air',
            'grab',
            'spin',
            'flips and inverted rotations',
            'inverted hand plants',
            'slide',
            'stall',
            'tweaks and variations',
            'other'
        ];

        $groups = [];

        foreach ($groupNames as $name) {
            $group = new Group;
            $group
                ->setName(ucfirst($name))
                ->setSlug($this->slugger->slugify($group->getName()));

            $groups[] = $group;
        }

        return $groups;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('en_US');

        // Create real groups
        $groups = $this->getGroups();

        foreach ($groups as $group) {
            $manager->persist($group);

            // Create fake tricks
            for ($t = 0; $t < mt_rand(mt_rand(0, 2), 7); $t++) {
                $trick = new Trick;
                $trick
                    ->setName(ucfirst($faker->words(mt_rand(2, 4), true)))
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
        }

        $manager->flush();
    }
}
