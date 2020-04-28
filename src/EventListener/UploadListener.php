<?php
namespace App\EventListener;

use App\Entity\Photo;
use Doctrine\Common\Persistence\ObjectManager;
use Oneup\UploaderBundle\Event\PostPersistEvent;

class UploadListener
{
    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }
    
    public function onUpload(PostPersistEvent $event)
    {
        $file = $event->getFile();

        $photo = new Photo();
        $photo->setTitle($file->getPathName());

        $this->om->persist($photo);
        $this->om->flush();
        $response = $event->getResponse();
        $response['success'] = true;
        return $response;
    }
}