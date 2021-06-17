<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Picture;
use Cocur\Slugify\Slugify;
use App\Repository\TrickRepository;
use App\Service\FileManagerService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PictureFixtures extends Fixture implements DependentFixtureInterface
{
    protected $slugger;
    protected $fileManager;
    protected $trickRepository;

    public function __construct(FileManagerService $fileManager, TrickRepository $trickRepository)
    {
        $this->slugger = new Slugify();
        $this->fileManager = $fileManager;
        $this->trickRepository = $trickRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('en_US');

        $tricks = $this->trickRepository->findAll();

        // Empty uploads/pictures directory
        array_map('unlink', glob($this->fileManager->getTargetDirectory() . '/*'));

        // Get starting pictures
        $dir = __DIR__ . '/images/';
        $allPictures = scandir($dir);

        // Create pictures in tricks
        foreach ($allPictures as $fileName) {
            if (is_file($dir . $fileName)) {
                //for ($img = 1; $img < 67; $img++) {
                $picture = new Picture;

                $file = new File($dir . $fileName);

                // Upload file in the pictures directory
                $originalFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slugify($originalFilename);
                $pictureFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
                $newFile = $file->move($this->fileManager->getTargetDirectory(), $pictureFilename);

                // Copy file in the orginal directory in case we need to reload fixtures
                copy($newFile, $dir . $originalFilename);

                $oneTrick = $faker->randomElement($tricks);

                // Udate the picture entity
                $picture
                    ->setFilename($pictureFilename)
                    ->setTrick($oneTrick)
                    ->setTitle($faker->words(mt_rand(2, 4), true))
                    ->setDescription($faker->paragraph(1));

                $manager->persist($picture);
            }
        }

        $manager->flush();

        foreach ($tricks as $trick) {
            $picture = $faker->randomElement($trick->getPictures());
            $trick->setMainPicture($picture);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
        ];
    }
}
