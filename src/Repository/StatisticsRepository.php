<?php

namespace App\Repository;

use App\Entity\Statistics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Statistics|null find($id, $lockMode = null, $lockVersion = null)
 * @method Statistics|null findOneBy(array $criteria, array $orderBy = null)
 * @method Statistics[]    findAll()
 * @method Statistics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatisticsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Statistics::class);
    }

    /**
     * @param string $date The day's statistic
     *
     * @return Statistics $stats Return the given date statistics
     */
    public function findByDate($date)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.date = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param string $max The maximum day / @example '31-01-1970'
     * @param string $min The minimum day (not included) / @example '01-01-1970'
     */
    public function findByDateInterval($max, $min = '01-01-1970')
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.date <= :max')
            ->setParameter('max', $max)
            ->andWhere('s.date > :min')
            ->setParameter('min', $min)
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Statistics[] Returns an array of Statistics objects
//     */
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
    public function findOneBySomeField($value): ?Statistics
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
