<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Video;
use App\Repository\TrickRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class VideoFixtures extends Fixture
{
    protected $trickRepository;

    public function __construct(TrickRepository $trickRepository)
    {
        $this->trickRepository = $trickRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $tricks = $this->trickRepository->findAll();

        $videos = [
            '10 Snowboard Grabs - Snowboarding Trick List' => 'https://youtu.be/CA5bURVJ5zk',
            'Hectic Pyramid Gap Session' => 'https://youtu.be/wfKClEdfcNk',
            'How To Ride Switch On A Snowboard' => 'https://youtu.be/e7SKudIEvU0',
            'How To Rodeo Flip On A Snowboard' => 'https://youtu.be/vf9Z05XY79A',
            'How To Turn On A Snowboard' => 'https://youtu.be/A_3_s8uK3_0',
            'Your First Frontside 180s On A Snowboard' => 'https://youtu.be/GnYAlEt-s00',
            'Twe12ve - Clip 1 (Snowboarding)' => 'https://dai.ly/x81c0a7',
            'Twe12ve - Clip 2 (Snowboarding)' => 'https://dai.ly/x81m07z',
            'Twe12ve - Clip 3 (Snowboarding)' => 'https://dai.ly/x81m0bg',
            'Twe12ve - Clip 4 (Snowboarding)' => 'https://dai.ly/x81m0ch',
            'Twe12ve - Clip 5 (Snowboarding)' => 'https://dai.ly/x81m0ea',
            'world snowboard day' => 'https://vimeo.com/18364353',
            'Snowboard Video' => 'https://vimeo.com/299887499'
        ];

        foreach ($videos as $title => $url) {
            $video = new Video;

            $oneTrick = $faker->randomElement($tricks);

            $video->setTitle($title)
                ->setVideoUrl($url)
                ->setTrick($oneTrick);

            $manager->persist($video);
        }

        $manager->flush();
    }
}
