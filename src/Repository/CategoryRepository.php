<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\Search;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findAllCategories()
    {
        return $this->createQueryBuilder('c')
            ->select('c.id')
            ->getQuery()
            ->getResult();
    }

    public function PaginationCategories($limit, $offset) : Paginator
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.posts', 'p')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        return new Paginator($qb);
    }

    public function PaginationCategoriesPosts($limit, $offset, $categoryId) : Paginator
    {
        $qb = $this->createQueryBuilder('c')
            ->select('p.title, c.id')
            ->join('c.posts', 'p')
            ->andWhere(' c.id = :id')
            ->setParameter('id', $categoryId)
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        return new Paginator($qb, false);
    }










    // /**
    //  * @return Category[] Returns an array of Category objects
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
    public function findOneBySomeField($value): ?Category
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
