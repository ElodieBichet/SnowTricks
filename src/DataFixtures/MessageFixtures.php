<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Message;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    protected $userRepository;
    protected $trickRepository;

    public function __construct(
        UserRepository $userRepository,
        TrickRepository $trickRepository
    ) {
        $this->userRepository = $userRepository;
        $this->trickRepository = $trickRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('en_US');

        $tricks = $this->trickRepository->findAll();
        $users = $this->userRepository->findAll();

        foreach ($tricks as $trick) {
            // fake message
            for ($m = 0; $m < mt_rand(mt_rand(0, 3), 15); $m++) {
                $message = new Message;
                $message
                    ->setAuthor($faker->randomElement($users))
                    ->setTrick($trick)
                    ->setContent($faker->paragraph(mt_rand(1, 4)))
                    ->setCreatedAt($faker->dateTimeBetween($trick->getCreatedAt()));

                $manager->persist($message);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TrickFixtures::class,
        ];
    }
}
