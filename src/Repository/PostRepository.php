<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 5;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param string $value
     * @param $sort
     * @param $sortKey
     * @return int|mixed|string
     */
    public function searchPost(string $value,$sort,$sortKey,int $offset) : Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->join('p.categories', 'c')
            ->where('p.title LIKE :val')
            ->setParameter('val', '%'. $value. '%')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset);
        if ($sort){
            $qb->OrderBy($sortKey,$sort ?? 'ASC');
        }
        return new Paginator($qb);

    }

    public function delete($id)
    {
        return $this->createQueryBuilder('p')
                ->andWhere('p.id = :id')
                ->setParameter('id',  $id)
                ->getQuery()
                ->getResult();
    }

    public function export()
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, COUNT(c.name)')
            ->join('p.categories', 'c')
            ->groupBy('p.id')
            ->getQuery()
            ->getResult();
    }








    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
