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
    public function searchPost(string $value,$sort,$sortKey,int $offset,$limit) : Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->join('p.categories', 'c')
            ->Where('p.title LIKE :value')
            ->setParameter('value', '%'. $value. '%')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        if ($sort){
            $qb->OrderBy($sortKey,$sort ?? 'ASC');
        }
        return new Paginator($qb);
    }

    public function searchsubheadline($value)
    {
        return $this->createQueryBuilder('p')
            ->where('p.subheadline LIKE :value')
            ->setParameter('value', '%'. $value. '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $id
     * @return int
     */
    public function deletePosts(array $ids): int
    {
        $qb = $this->createQueryBuilder('p');

        return $qb->delete( Post::class,'p')
            ->where($qb->expr()->notIn('p.title', $ids))
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $postId
     */
    public function findComments(int $postId)
    {
        return $this->createQueryBuilder('p')

            ->andWhere('p.id = :value')
            ->setParameter('value', $postId)
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
