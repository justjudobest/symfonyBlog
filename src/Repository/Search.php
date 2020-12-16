<?php


namespace App\Repository;


class Search
{
    public $mainAlias;
    public $field;


    /**
     * @param string $value
     * @param $sort
     * @param $sortKey
     * @return int|mixed|string
     */
    public function search(string $value,$sort,$sortKey)
    {
        $qb = $this->createQueryBuilder('')
            ->where(' LIKE :val')
            ->setParameter('val', '%'. $value. '%')
            ->setMaxResults(10);
        if ($sort){
            $qb->OrderBy($sortKey,$sort ?? 'ASC');
        }
        return $qb
            ->getQuery()
            ->getResult();

    }



}