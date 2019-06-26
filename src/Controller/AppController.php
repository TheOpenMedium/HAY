<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Form\PostType;
use App\Form\LogInType;
use App\Form\SignUpType;
use App\Controller\AjaxController;
use App\Controller\SurveyController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
                return $this->redirectToRoute('app_home', array('_locale' => $locale));
            }
        }

        // If none of the locale match, he's redirected to the language choose page.
        return $this->render('locale.html.twig');
    }

    /**
     * Redirect to home page
     *
     * @Route("/{_locale}/", name="app_index", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function indexAction()
    {
        return $this->redirectToRoute('app_home');
    }

    /**
     * Render the home page
     *
     * @param Request $request The HTTP request
     * @param AjaxController $ajaxController The Ajax controller
     *
     * @Route("/{_locale}/home/{filter<filter>?}/{scope}/{limit}/{order}/{date}", name="app_home", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function homeAction(Request $request, AjaxController $ajaxController, SurveyController $sc, ?string $filter = NULL, string $scope = "all", int $limit = 10, string $order = "DESC", ?string $date = NULL)
    {
        if (!$filter) {
            $securityContext =  $this->container->get('security.authorization_checker');
            if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                $settings = $this->getUser()->getSettings();
                if ($settings['default_home_scope_filter']) {
                    $scope = $settings['default_home_scope_filter'];
                }
                if ($settings['default_home_limit_filter']) {
                    $limit = $settings['default_home_limit_filter'];
                }
                if ($settings['default_home_order_filter']) {
                    $order = $settings['default_home_order_filter'];
                }
            }
        }

        if ($this->isGranted('post.submit')) {
            $post = new Post();

            // Creating Post submit Form in case he want to send a post.
            $form = $this->createForm(PostType::class, $post);
            if (!$this->isGranted('post.option_color')) {
                $form->remove("color");
            }
            if (!$this->isGranted('post.option_textsize')) {
                $form->remove("size");
            }

            $form->handleRequest($request);

            // If he send a Post, the Post is saved into database.
            if ($form->isSubmitted() && $form->isValid()) {
                $post = $form->getData();
                $post->setId($this->generateIdAction($this->getDoctrine()->getRepository(Post::class), 10));
                if (!$this->isGranted('post.option_color')) {
                    $post->setColor('696');
                }
                if (!$this->isGranted('post.option_textsize')) {
                    $post->setSize('16');
                }
                $post->setFont('SS');
                $post->setUser($this->getUser());

                // TODO: Sending notifications to followers

                $post = $sc->surveyCheckPostAction($post);
                $em = $this->getDoctrine()->getManager();
                $em->persist($post);
                $em->flush();
            }
        }

        // Fetching Post.
        $postList = $ajaxController->postGenerateAction($scope, $order, $limit, $date);

        if ($this->isGranted('post.submit')) {
            $form = $form->createView();
        } else {
            $form = null;
        }

        // All that is rendered with the home template sending a Form, Post List and Comment List.
        return $this->render('home.html.twig', array(
            'post' => $form,
            'postList' => $postList,
            'scope' => $scope,
            'order' => $order
        ));
    }

    /**
     * Render the Log In page
     *
     * @param AuthenticationUtils $authUtils Extracts Security Errors from Request
     *
     * @Route("/{_locale}/login", name="app_login", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function loginAction(AuthenticationUtils $authUtils)
    {
        $user = new User();

        // Creating a Form to Authentify.
        $form = $this->createForm(LogInType::class, $user);

        // In case of error. And getting last username.
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        // All that is rendered with the login template sending a Form, errors and last username.
        return $this->render('login.html.twig', array(
            'log_in' => $form->createView(),
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
     *     "_locale": "%app.locales%"
     * })
     */
    public function signupAction(Request $request)
    {
        $this->denyAccessUnlessGranted('user.submit');

        $user = new User();

        // Creating the Sign Up Form.
        $form = $this->createForm(SignUpType::class, $user);

        $form->handleRequest($request);

        // If a Sign Up Form has already been sent, a new user is created.
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $user->setId($this->generateIdAction($this->getDoctrine()->getRepository(User::class), 7));

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
            'sign_up' => $form->createView()
        ));
    }

    /**
     * Log Out the user
     *
     * NOTE: This action don't do anything, it just define the Log Out route.
     * It's the security bundle of symfony that log out the user.
     *
     * @Route("/{_locale}/logout", name="app_logout", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function logoutAction()
    {
    }

    /**
     * Generate a random ID.
     */
    public function generateIdAction($repository, $length)
    {
        $id = Null;
        while ($id == Null || $repository->find($id)) {
            // Thanks to Pr07o7yp3 on StackOverflow for this great function ;)!
            // @see https://stackoverflow.com/questions/4356289/php-random-string-generator/13212994#13212994
            $id = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_',ceil($length/strlen($x)))),1,$length);
        }
        return $id;
    }
}
