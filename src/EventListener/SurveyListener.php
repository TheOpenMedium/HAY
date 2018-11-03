<?php

namespace App\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Entity\User;
use App\Entity\Survey;

class SurveyListener
{
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Survey) {
            $em = $args->getEntityManager();

            $answers = $entity->getRawAnswers();

            foreach ($answers as $i => $answerOption) {
                foreach ($answerOption as $j => $value) {
                    $answers[$i][$j] = $em->getRepository(User::class)->find($value);
                }
            }

            $entity->setDoneAnswers($answers);
        }
    }
}
