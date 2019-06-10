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
     * @param $date_post The last date
     * @return Post[] Returns an array of Post objects
     */
    public function findPostWithNoLimitAndFromDate($date_post)
    {
        return $this->createQueryBuilder('p')
            ->select(array('p'))
            ->andWhere('p.date_post > :date_post')
            ->setParameter('date_post', $date_post)
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
     * @param $date_post The last date
     * @return Post[] Returns an array of Post objects
     */
    public function findPostByUserWithNoLimitAndFromDate($u, $date_post)
    {
        return $this->createQueryBuilder('p')
            ->select(array('p'))
            ->andWhere('p.user = :user_id')
            ->setParameter('user_id', $u)
            ->andWhere('p.date_post > :date_post')
            ->setParameter('date_post', $date_post)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $u The users' post
     * @param $o The results order
     * @param $n The number of max results
     * @return Post[] Returns an array of Post objects
     */
    public function findPostByFriends($u, $o, $n)
    {
        return $this->createQueryBuilder('p')
            ->select(array('p'))
            ->andWhere('p.user IN (:users_ids)')
            ->setParameter('users_ids', $u)
            ->orderBy('p.date_post', $o)
            ->setMaxResults($n)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $u The users' post
     * @param $date_post The last date
     * @return Post[] Returns an array of Post objects
     */
    public function findPostByFriendsWithNoLimitAndFromDate($u, $date_post)
    {
        return $this->createQueryBuilder('p')
            ->select(array('p'))
            ->andWhere('p.user IN (:users_ids)')
            ->setParameter('users_ids', $u)
            ->andWhere('p.date_post > :date_post')
            ->setParameter('date_post', $date_post)
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
