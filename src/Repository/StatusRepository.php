<?php

namespace App\Repository;

use App\Entity\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class StatusRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Status::class);
    }

    /**
     * @param $n
     * @return Status[]
     */
    public function findStatus($n)
    {
        return $this->createQueryBuilder('s')
            ->select(array('s', 'u.id', 'u.first_name', 'u.last_name', 'u.username'))
            //->from('App\Entity\Status', 's')
            ->innerJoin('App\Entity\User', 'u', 'WITH', 's.id_user = u.id')
            //->where('s.something = :value')->setParameter('value', $value)
            ->orderBy('s.date_content', 'DESC')
            ->setMaxResults($n)
            ->getQuery()
            ->getResult()
            //->execute()
        ;
    }

    /**
     * @param $id
     * @return Status[]
     */
    public function findStatusById($id)
    {
        return $this->createQueryBuilder('s')
            ->select(array('s', 'u.id', 'u.first_name', 'u.last_name', 'u.username'))
            //->from('App\Entity\Status', 's')
            ->innerJoin('App\Entity\User', 'u', 'WITH', 's.id_user = u.id')
            ->where('s.id = :id')->setParameter('id', $id)
            ->getQuery()
            ->getResult()
            //->execute()
        ;
    }

    /**
     * @param $n, $id
     * @return Status[]
     */
    public function findStatusByUser($n, $id)
    {
        return $this->createQueryBuilder('s')
            ->select(array('s', 'u.id', 'u.first_name', 'u.last_name', 'u.username'))
            //->from('App\Entity\Status', 's')
            ->innerJoin('App\Entity\User', 'u', 'WITH', 's.id_user = u.id')
            ->where('s.id_user = :id')->setParameter('id', $id)
            ->orderBy('s.date_content', 'DESC')
            ->setMaxResults($n)
            ->getQuery()
            ->getResult()
            //->execute()
        ;
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('s')
            ->where('s.something = :value')->setParameter('value', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
