<?php

namespace App\Repository;

use App\Entity\Gallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

class GalleryRepository extends ServiceEntityRepository
{
    /** @var  EntityRepository */
    private $repository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gallery::class);
        $this->repository = $registry->getRepository(Gallery::class);
    }

    public function findNewest($limit = 5)
    {
        return $this->repository->findBy([], ['createdAt' => 'DESC'], $limit);
    }

    public function findRelated(Gallery $gallery, $limit = 5)
    {
        return $this->repository->findBy(['user' => $gallery->getUser()], ['createdAt' => 'DESC'], $limit);
    }
}