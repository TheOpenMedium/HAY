<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Controller\PostController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * A controller related to the application
 *
 * List of actions:
 * * localeAction()                              -- app_locale
 * * indexAction(Request $request)               -- app_index
 * * loginAction(AuthenticationUtils $authUtils) -- app_login
 * * signupAction(Request $request)              -- app_signup
 * * logoutAction()                              -- app_logout
 */
class AppController extends Controller
{
    /**
     * Choose the user's locale
     *
     * @Route("/", name="app_locale")
     */
    public function localeAction()
    {
        // Here, the controller retrieve prefered languages of the user
        // then he split the string (For example: "fr,fr-FR;q=0.8,en;q=0.5,ar;q=0.3")
        // to locales and other things that he will skip (For example: ['fr', 'fr', 'FR',
        // 'q=0.8', 'en', 'q=0.5', 'ar', 'q=0.3']).
        $localeList = preg_split('#[,;-]#', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

        // Here, the controller compare every locale to his list of accepted locales. When
        // one of them match, the user is redirected to the homepage with the good locale.
        foreach ($localeList as $locale) {
            if ($locale == 'en' || $locale == 'fr') {
                return $this->redirectToRoute('app_index', array('_locale' => $locale));
            }
        }

        // If none of the locale match, he's redirected to the language choose page.
        return $this->render('locale.html.twig');
    }

    /**
     * Render the home page
     *
     * @param Request $request The HTTP request
     *
     * @Route("/{_locale}/", name="app_index", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function indexAction(Request $request, PostController $postController)
    {
        $post = new Post();

        // Creating Post submit Form in case he want to send a post.
        $form = $this->createFormBuilder($post)
            ->add('content', TextareaType::class)
            ->add('color', ChoiceType::class, array(
                'choices' => array(
                    '000', '222', '696', '999', 'DDD', 'FFF',
                    'E00', '72C', '008', '099', '0A0', 'F91',
                    'F00', 'D0F', '22F', '6DF', '0F0', 'FD0',
                    'F44', 'F2E', '08F', '0FF', 'BF0', 'EE0',
                    'F05', 'F6F', '0AE', '9FF', '5F9', 'FF0'
                ),
                'multiple' => false,
                'expanded' => true
            ))
            ->add('size', IntegerType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        // If he send a Post, the Post is saved into database.
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setFont('SS');
            $post->setUser($this->getUser());

            // TODO: Sending notifications to followers

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
        }

        // Fetching Post.
        $postList = $postController->postGenerateAction();

        // All that is rendered with the home template sending a Form, Post List and Comment List.
        return $this->render('home.html.twig', array(
            'form' => $form->createView(),
            'postList' => $postList
        ));
    }

    /**
     * Render the Log In page
     *
     * @param AuthenticationUtils $authUtils Extracts Security Errors from Request
     *
     * @Route("/{_locale}/login", name="app_login", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function loginAction(AuthenticationUtils $authUtils)
    {
        $user = new User();

        // Creating a Form to Authentify.
        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('rememberme', CheckboxType::class, array(
                'required' => false
            ))
            ->add('submit', SubmitType::class)
            ->getForm();

        // In case of error. And getting last username.
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        // All that is rendered with the login template sending a Form, errors and last username.
        return $this->render('login.html.twig', array(
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error
        ));
    }

    /**
     * Render the Sign Up page
     *
     * @param Request $request The HTTP Request
     *
     * @Route("/{_locale}/signup", name="app_signup", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function signupAction(Request $request)
    {
        $user = new User();

        // Creating the Sign Up Form.
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

        // If a Sign Up Form has already been sent, a new user is created.
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            // Password is hashed using the ARGON2I method.
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_ARGON2I));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // And redirecting user to the home page.
            return $this->redirectToRoute('app_login');
        }

        // All that is rendered with the Sign Up template sending a Form.
        return $this->render('signup.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Log Out the user
     *
     * NOTE: This action don't do anything, it just define the Log Out route.
     * It's the security bundle of symfony that log out the user.
     *
     * @Route("/{_locale}/logout", name="app_logout", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function logoutAction()
    {
    }
}
