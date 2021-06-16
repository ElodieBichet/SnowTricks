<?php

namespace App\Doctrine\Listener;

use App\Entity\Picture;
use App\Service\FileUploaderService;

class PictureRemoveFileListener
{
    protected $fileUploader;

    public function __construct(FileUploaderService $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    public function postRemove(Picture $picture)
    {
        $this->fileUploader->remove($picture->getFilename());
    }
}
