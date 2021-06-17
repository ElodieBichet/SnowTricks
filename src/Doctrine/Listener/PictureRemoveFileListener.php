<?php

namespace App\Doctrine\Listener;

use App\Entity\Picture;
use App\Service\FileManagerService;

class PictureRemoveFileListener
{
    protected $fileManager;

    public function __construct(FileManagerService $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    public function postRemove(Picture $picture)
    {
        $this->fileManager->remove($picture->getFilename());
    }
}
