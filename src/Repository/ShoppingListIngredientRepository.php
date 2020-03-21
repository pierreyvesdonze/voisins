<?php

namespace App\Repository;

use App\Entity\ShoppingListIngredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ShoppingListIngredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShoppingListIngredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShoppingListIngredient[]    findAll()
 * @method ShoppingListIngredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShoppingListIngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShoppingListIngredient::class);
    }

   /* public function increase ($value) {

         $qb = $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->set('s.amount', $qb->expr()->sum('s.amount', $value), 's')
            ->getQuery()
            ->getResult()
        ;

        return $qb;
    }*/


    // /**
    //  * @return ShoppingListIngredient[] Returns an array of ShoppingListIngredient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ShoppingListIngredient
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
