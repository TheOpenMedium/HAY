<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FrController extends Controller
{
    /**
     * @Route("/fr/")
     */
    public function indexAction(Request $request)
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/fr/connexion", name="app_fr_login")
     */
    public function loginAction(Request $request)
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
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
    /**
     * @Route("/fr/inscription", name="app_fr_signup")
     */
    public function signupAction(Request $request)
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('username', TextType::class, array('required' => false))
            ->add('email', RepeatedType::class, array(
                'type' => EmailType::class
            ))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class
            ))
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_fr_index');
        }

        return $this->render('signup.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
