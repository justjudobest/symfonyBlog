<?php

namespace App\Repository;

use App\Entity\CommonFields;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommonFields|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommonFields|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommonFields[]    findAll()
 * @method CommonFields[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommonFieldsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommonFields::class);
    }

    // /**
    //  * @return CommonFields[] Returns an array of CommonFields objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommonFields
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
