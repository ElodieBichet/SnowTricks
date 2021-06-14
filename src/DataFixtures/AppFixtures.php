<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Group;
use App\Entity\Trick;
use App\Entity\Message;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = new Slugify();
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('en_US');

        // Create admin user
        $admin = new User;

        $hash = $this->encoder->encodePassword($admin, $_ENV['SUPER_ADMIN_PASSWORD']);

        $users = [];

        $admin->setEmail($_ENV['SUPER_ADMIN_EMAIL'])
            ->setPassword($hash)
            ->setFullname("Admin")
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(1);

        $manager->persist($admin);

        array_push($users, $admin);

        // Create fake users
        for ($u = 0; $u < 9; $u++) {
            $user = new User();
            $hash = $this->encoder->encodePassword($user, "password");
            $user->setEmail("user$u@email.com")
                ->setFullname($faker->name())
                ->setPassword($hash)
                ->setIsVerified(mt_rand(0, 1));

            $manager->persist($user);
            array_push($users, $user);
        }

        // Create real groups, fake tricks and fake messages
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
        foreach ($groupNames as $name) {
            $group = new Group;
            $group
                ->setName(ucfirst($name))
                ->setSlug($this->slugger->slugify($group->getName()));

            $manager->persist($group);

            for ($t = 0; $t < mt_rand(mt_rand(0, 2), 7); $t++) {
                $trick = new Trick;
                $trick
                    ->setName(ucfirst($faker->words(mt_rand(2, 4), true)))
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

                // fake message
                for ($m = 0; $m < mt_rand(mt_rand(0, 2), 15); $m++) {
                    $message = new Message;
                    $author = $users[array_rand($users)];
                    $message
                        ->setAuthor($author)
                        ->setTrick($trick)
                        ->setContent($faker->paragraph(mt_rand(1, 4)))
                        ->setCreatedAt($faker->dateTimeBetween($trick->getCreatedAt()));

                    $manager->persist($message);
                }
            }
        };

        $manager->flush();
    }
}
