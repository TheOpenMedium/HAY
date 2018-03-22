<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @param $id
     * @return Notification[]
     */
    public function findNotification($id)
    {
        return $this->createQueryBuilder('n')
            ->select(array('n', 'u.id', 'u.first_name', 'u.last_name', 'u.username'))
            ->andWhere('n.id_user = :id')->setParameter('id', $id)
            ->innerJoin('App\Entity\User', 'u', 'WITH', 'n.id_user = u.id')
            ->orderBy('n.date_send', 'DESC')
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
