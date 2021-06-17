<?php

namespace App\EventDispatcher;

use App\Event\FileUpdateEvent;
use App\Service\FileManagerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FileReplaceSubscriber implements EventSubscriberInterface
{
    protected $fileManager;

    public function __construct(FileManagerService $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            'file.update' => 'replaceFile',
            'file.new' => 'createFile'
        ];
    }

    public function replaceFile(FileUpdateEvent $fileUpdateEvent)
    {
        // Upload the new file
        $pictureFilename = $this->fileManager->upload($fileUpdateEvent->getPictureFile());
        // Remove the old file
        $this->fileManager->remove($fileUpdateEvent->getPicture()->getFilename());
        // updates the 'filename' property to store the image file name instead of its contents
        $fileUpdateEvent->getPicture()->setFilename($pictureFilename);
    }

    public function createFile(FileUpdateEvent $fileUpdateEvent)
    {
        // Upload the new file
        $pictureFilename = $this->fileManager->upload($fileUpdateEvent->getPictureFile());
        // updates the 'filename' property to store the image file name instead of its contents
        $fileUpdateEvent->getPicture()->setFilename($pictureFilename);
    }
}
