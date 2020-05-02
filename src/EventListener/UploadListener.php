<?php

namespace App\EventListener;

use App\Entity\Photo;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Minn\AdsBundle\Entity\MotorsAdsFile;

class UploadListener
{
    protected $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function onUpload(PostPersistEvent $event)
    {
        $file = $event->getFile();

        $object = new Photo();
        $object->setTitle($file->getPathName());

        $this->manager->persist($object);
        $this->manager->flush();
    }
}