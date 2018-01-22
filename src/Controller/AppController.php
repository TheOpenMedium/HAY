<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Status;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AppController extends Controller
{
    /**
     * @Route("/")
     */
    public function localeAction(Request $request)
    {
        $locale = $request->getLocale();

        if($locale == 'en' || 'fr') {
            return $this->redirectToRoute('app_index', array('_locale' => $locale));
        }
        else {
            return $this->render('locale.html.twig');
        }
    }

    /**
     * @Route("/{_locale}/", name="app_index", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function indexAction(Request $request)
    {
        $status = new Status();

        $form = $this->createFormBuilder($status)
            ->add('content', TextareaType::class)
            ->add('color', RadioType::class)
            ->add('size', RadioType::class)
            ->getForm();

        return $this->render('home.html.twig');
    }

    /**
     * @Route("/{_locale}/login", name="app_login", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('rememberme', CheckboxType::class, array(
                'required' => 'false'
            ))
            ->add('submit', SubmitType::class)
            ->getForm();

        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('login.html.twig', array(
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error
        ));
    }

    /**
     * @Route("/{_locale}/signup", name="app_signup", requirements={
     *     "_locale": "en|fr"
     * })
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
            $user->setSalt('');

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_index');
        }

        return $this->render('signup.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{_locale}/logout", name="app_logout", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function logoutAction(Request $request)
    {
    }
}
