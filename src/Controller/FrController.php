<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FrController extends Controller
{
    /**
     * @Route("/fr/")
     */
    public function indexAction()
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/fr/connexion", name="app_fr_login")
     */
    public function loginAction()
    {
        $user = new User();

        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('cookies', ChoiceType::class, array(
                'choices' => array(
                    'Ne pas se connecter automatiquement' => 0,
                    '3 mois' => 0.25,
                    '6 mois' => 0.5,
                    '1 an' => 1,
                    '2 ans' => 2
                )))
            ->add('submit', SubmitType::class)
            ->getForm();

        return $this->render('login.html.twig', array(
            'form' => $form->createView()
        ));
    }
}