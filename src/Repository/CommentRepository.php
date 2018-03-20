<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param $n, $id
     * @return Comment[]
     */
    public function findComments($n, $id)
    {
        return $this->createQueryBuilder('c')
            ->select(array('c', 'u.id', 'u.first_name', 'u.last_name', 'u.username'))
            ->andWhere('c.id_status = :id')
            ->setParameter('id', $id)
            //->from('App\Entity\Status', 's')
            ->innerJoin('App\Entity\User', 'u', 'WITH', 'c.id_user = u.id')
            //->where('s.something = :value')->setParameter('value', $value)
            ->orderBy('c.date_send', 'DESC')
            ->setMaxResults($n)
            ->getQuery()
            ->getResult()
            //->execute()
        ;
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('c')
            ->where('c.something = :value')->setParameter('value', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
