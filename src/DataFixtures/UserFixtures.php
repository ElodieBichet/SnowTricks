<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('en_US');

        $admin = $this->createSuperAdmin();
        $manager->persist($admin);

        // Create fake users
        for ($u = 0; $u < 9; $u++) {
            $user = new User();
            $hash = $this->encoder->encodePassword($user, "password");
            $user->setEmail("user$u@email.com")
                ->setFullname($faker->name())
                ->setPassword($hash)
                ->setIsVerified(mt_rand(0, 1));

            $manager->persist($user);
        }

        $manager->flush();
    }

    protected function createSuperAdmin(): User
    {
        // Create admin user
        $admin = new User;

        $hash = $this->encoder->encodePassword($admin, $_ENV['SUPER_ADMIN_PASSWORD']);

        $admin->setEmail($_ENV['SUPER_ADMIN_EMAIL'])
            ->setPassword($hash)
            ->setFullname("Admin")
            ->setRoles(['ROLE_ADMIN'])
            ->setIsVerified(1);

        return $admin;
    }
}
