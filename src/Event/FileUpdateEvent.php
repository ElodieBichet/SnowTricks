<?php

namespace App\Event;

use App\Entity\Picture;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\EventDispatcher\Event;

class FileUpdateEvent extends Event
{
    protected $picture;
    protected $pictureFile;

    public function __construct(Picture $picture, UploadedFile $pictureFile)
    {
        $this->picture = $picture;
        $this->pictureFile = $pictureFile;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function getPictureFile()
    {
        return $this->pictureFile;
    }
}
