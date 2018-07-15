<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param $o The results order
     * @param $n The number of max results
     * @return Post[] Returns an array of Post objects
     */
    public function findPost($o, $n)
    {
        return $this->createQueryBuilder('p')
            ->select(array('p'))
            ->orderBy('p.date_post', $o)
            ->setMaxResults($n)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $id The last ID
     * @return Post[] Returns an array of Post objects
     */
    public function findPostWithNoLimitAndFromId($id)
    {
        return $this->createQueryBuilder('p')
            ->select(array('p'))
            ->andWhere('p.id > :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $u The user's post
     * @param $o The results order
     * @param $n The number of max results
     * @return Post[] Returns an array of Post objects
     */
    public function findPostByUser($u, $o, $n)
    {
        return $this->createQueryBuilder('p')
            ->select(array('p'))
            ->andWhere('p.user = :user_id')
            ->setParameter('user_id', $u)
            ->orderBy('p.date_post', $o)
            ->setMaxResults($n)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $u The user's post
     * @param $id The last ID
     * @return Post[] Returns an array of Post objects
     */
    public function findPostByUserWithNoLimitAndFromId($u, $id)
    {
        return $this->createQueryBuilder('p')
            ->select(array('p'))
            ->andWhere('p.user = :user_id')
            ->setParameter('user_id', $u)
            ->andWhere('p.id > :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLastPost()
    {
        return $this->createQueryBuilder('p')
            ->select(array('p'))
            ->orderBy('p.date_post', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
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
