<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Statistics;

class StatisticsListener
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $em = $args->getEntityManager();
            
            $stats = $em->getRepository(Statistics::class)->findByDate(\date('Y-m-d'));

            if (empty($stats[0])) {
                $stats[0] = new Statistics;
                $em->persist($stats[0]);
            }

            $new_users = $stats[0]->getNewUsers();
            $new_users++;
            $stats[0]->setNewUsers($new_users);

            $em->flush();
        } else if ($entity instanceof Post) {
            $em = $args->getEntityManager();
            
            $stats = $em->getRepository(Statistics::class)->findByDate(\date('Y-m-d'));

            if (empty($stats[0])) {
                $stats[0] = new Statistics;
                $em->persist($stats[0]);
            }

            $new_posts = $stats[0]->getNewPosts();
            $new_posts++;
            $stats[0]->setNewPosts($new_posts);

            $em->flush();
        } else if ($entity instanceof Comment) {
            $em = $args->getEntityManager();
            
            $stats = $em->getRepository(Statistics::class)->findByDate(\date('Y-m-d'));

            if (empty($stats[0])) {
                $stats[0] = new Statistics;
                $em->persist($stats[0]);
            }

            $new_comments = $stats[0]->getNewComments();
            $new_comments++;
            $stats[0]->setNewComments($new_comments);

            $em->flush();
        }
    }
}