<?php

namespace App\Repository;

use App\Entity\Laws;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Laws|null find($id, $lockMode = null, $lockVersion = null)
 * @method Laws|null findOneBy(array $criteria, array $orderBy = null)
 * @method Laws[]    findAll()
 * @method Laws[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LawsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Laws::class);
    }

//    /**
//     * @return Laws[] Returns an array of Laws objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Laws
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
