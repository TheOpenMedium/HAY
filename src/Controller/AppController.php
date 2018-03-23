<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Status;
use App\Entity\Comment;
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

class AppController extends Controller
{
    /**
     * @Route("/")
     */
    public function localeAction()
    {
        // Here, the controller retrieve prefered languages of the user
        // then he split the string (For example: "fr,fr-FR;q=0.8,en;q=0.5,ar;q=0.3")
        // to locales and other things that he will skip (For example: ['fr', 'fr', 'FR',
        // 'q=0.8', 'en', 'q=0.5', 'ar', 'q=0.3']).
        $localeList = preg_split('#[,;-]#', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

        // Here, the controller compare every locale to his list of accepted locales when
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
     * @Route("/{_locale}/", name="app_index", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function indexAction(Request $request)
    {
        // Making sure that he's connected.
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $status = new Status();

        // Creating Status submit Form in case he want to send a status.
        $form = $this->createFormBuilder($status)
            ->add('content', TextareaType::class)
            ->add('color', ChoiceType::class, array(
                'choices' => array(
                    '000',
                    '222',
                    '696',
                    '999',
                    'DDD',
                    'FFF',

                    'E00',
                    '72C',
                    '008',
                    '099',
                    '0A0',
                    'F91',

                    'F00',
                    'D0F',
                    '22F',
                    '6DF',
                    '0F0',
                    'FD0',

                    'F44',
                    'F2E',
                    '08F',
                    '0FF',
                    'BF0',
                    'EE0',

                    'F05',
                    'F6F',
                    '0AE',
                    '9FF',
                    '5F9',
                    'FF0'
                ),
                'multiple' => false,
                'expanded' => true
            ))
            ->add('size', IntegerType::class)
            ->add('id_user', HiddenType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        // If he send a Status, the Status is saved into database.
        if ($form->isSubmitted() && $form->isValid()) {
            $status = $form->getData();
            $status->setFont('SS');

            $em = $this->getDoctrine()->getManager();
            $em->persist($status);
            $em->flush();
        }

        // Fetching Status and their comments.
        $statusList = $this->getDoctrine()->getRepository(Status::class)->findStatus(10);
        $commentList = array();

        // If there is one Status or more :
        if ($statusList) {
            // We replace new lines by the <br /> tag and we fetch from the database the 10 newer comments of each status.
            foreach ($statusList as $status) {
                $content = $status[0]->getContent();
                $status[0]->setContent(preg_replace('#\n#', '<br />', $content));
                $commentList[] = $this->getDoctrine()->getRepository(Comment::class)->findComments(10, $status[0]->getId());
            }

            // Then, we replace new lines by the <br /> tag.
            foreach ($commentList as $commentStatus) {
                if ($commentStatus) {
                    foreach ($commentStatus as $comment) {
                        $c = $comment[0]->getComment();
                        $comment[0]->setComment(preg_replace('#\n#', '<br />', $c));
                    }
                }
            }
        }

        // All that is rendered with the home template sending a Form, Status List and Comment List.
        return $this->render('home.html.twig', array(
            'form' => $form->createView(),
            'commentList' => $commentList,
            'statusList' => $statusList
        ));
    }

    /**
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

            // And redirecting user to the home page
            return $this->redirectToRoute('app_index');
        }

        // All that is rendered with the Sign Up template sending a Form.
        return $this->render('signup.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{_locale}/logout", name="app_logout", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function logoutAction()
    {
    }
}
