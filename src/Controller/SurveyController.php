<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Survey;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SurveyController extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * CAN'T BE ACCESSED BY URL, ONLY USED FOR THE SURVEY ENTITY.
     */
    public function fetchSurveyUsers(array $answers)
    {
        foreach ($answers as $i => $answerOption) {
            foreach ($answerOption as $j => $value) {
                $answers[$i][$j] = $this->container->get('doctrine')->getRepository(User::class)->find($value);
            }
        }
        return $answers;
    }
}
