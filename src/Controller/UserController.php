<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Controller\AjaxController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * A controller related to the User entity
 *
 * List of actions:
 * * userShowAction(User $user)               -- user_show
 * * userEditAction(Request $request)         -- user_edit
 * * userEditImageAction(Request $request)    -- user_edit_image
 * * userEditNameAction(Request $request)     -- user_edit_name
 * * userEditUserNameAction(Request $request) -- user_edit_username
 * * userEditEmailAction(Request $request)    -- user_edit_email
 * * userEditPasswordAction(Request $request) -- user_edit_password
 */
class UserController extends Controller
{
    /**
     * Render an user page
     *
     * @param User $user The user to show
     *
     * @Route("/{_locale}/show/user/{id}", name="user_show", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function userShowAction(User $user, AjaxController $ajaxController)
    {
        // Fetching Post.
        $postList = $ajaxController->postGenerateAction("user", "DESC", 10, NULL, NULL, $user->getId());

        $bool = "no";

        if ($this->getUser()) {
            // Does the users are already friends?
            foreach ($this->getUser()->getFriends() as $friend) {
                if ($friend->getId() == $user->getId()) {
                    $bool = "yes";
                }
            }

            // Does the user has already sent a friend request?
            foreach ($this->getUser()->getFriendRequests() as $friendRequest) {
                if ($friendRequest->getToUser()->getId() == $user->getId()) {
                    $bool = "requested";
                }
            }

            // Does the current user has already a request to be friend with the current user?
            foreach ($this->getUser()->getRequestedFriends() as $requestedFriend) {
                if ($requestedFriend->getFromUser()->getId() == $user->getId()) {
                    $bool = "accept";
                }
            }
        }

        // All that is rendered with the user show template sending Post List, Comment List and User.
        return $this->render('user/showUser.html.twig', array(
            'postList' => $postList,
            'user' => $user,
            'friend' => $bool,
            'scope' => 'user',
            'order' => 'DESC'
        ));
    }

    /**
     * Edit an user
     *
     * @Route("/{_locale}/edit/user", name="user_edit", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function userEditAction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $user = new User;

        // Retrieving previous parameters.
        $user->setFirstName($this->getUser()->getFirstName());
        $user->setLastName($this->getUser()->getLastName());
        $user->setEmail($this->getUser()->getEmail());
        $user->setUsername($this->getUser()->getUsername());
        $user->setAlt($this->getUser()->getAlt());

        // Creating the form.
        $formImage = $this->createFormBuilder($user)
            ->setAction($this->generateUrl('user_edit_image'))
            ->add('file', FileType::class)
            ->add('alt', TextType::class)
            ->add('conf_password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();
        $formName = $this->createFormBuilder($user)
            ->setAction($this->generateUrl('user_edit_name'))
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('conf_password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();
        $formUserName = $this->createFormBuilder($user)
            ->setAction($this->generateUrl('user_edit_username'))
            ->add('username', TextType::class)
            ->add('conf_password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();
        $formEmail = $this->createFormBuilder($user)
            ->setAction($this->generateUrl('user_edit_email'))
            ->add('email', RepeatedType::class, array(
                'type' => EmailType::class
            ))
            ->add('conf_password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();
        $formPassword = $this->createFormBuilder($user)
            ->setAction($this->generateUrl('user_edit_password'))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class
            ))
            ->add('conf_password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        // All that is rendered with the user edit template sending a From.
        return $this->render('user/editUser.html.twig', array(
            'form_image' => $formImage->createView(),
            'form_name' => $formName->createView(),
            'form_username' => $formUserName->createView(),
            'form_email' => $formEmail->createView(),
            'form_password' => $formPassword->createView()
        ));
    }

    /**
     * Edit an user image
     *
     * @Route("/{_locale}/edit/user/image", name="user_edit_image", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function userEditImageAction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $user = new User;

        // Retrieving previous parameters.
        $user->setFirstName($this->getUser()->getFirstName());
        $user->setLastName($this->getUser()->getLastName());
        $user->setEmail($this->getUser()->getEmail());
        $user->setUsername($this->getUser()->getUsername());
        $user->setAlt($this->getUser()->getAlt());

        $formImage = $this->createFormBuilder($user)
            ->add('file', FileType::class)
            ->add('alt', TextType::class)
            ->add('conf_password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();
        
        $formImage->handleRequest($request);

        if ($formImage->isSubmitted() && $formImage->isValid()) {
            $user = $formImage->getData();

            // We verify that the user submitted a correct password.
            if (password_verify($user->getConfPassword(), $this->getUser()->getPassword()) && $user->getFile() !== null) {
                $em = $this->getDoctrine()->getManager();

                // Then, we replace old datas.
                $this->getUser()->setAlt($user->getAlt());

                // We verify that the user hasn't already an image.
                if ($this->getUser()->getUrl() != '/ressources/icon.svg') {
                    // If it's the case, we remove it.
                    unlink(__dir__.'/../../public'.$this->getUser()->getUrl());
                }

                // Then, we upload the file.
                $extension = $user->getFile()->guessExtension();
                if (!$extension) {
                    $extension = 'png';
                }
                $user->getFile()->move(__dir__.'/../../public/usr_img/', $this->getUser()->getId().'.'.$extension);

                // And, we add the url.
                $this->getUser()->setUrl('/usr_img/'.$this->getUser()->getId().'.'.$extension);

                // And finaly, we save changes.
                $em->flush();

                // And we redirect user to home page.
                return $this->redirectToRoute('app_home');
            } else {
                return $this->redirectToRoute('user_edit');
            }
        }
    }

    /**
     * Edit an user name
     *
     * @Route("/{_locale}/edit/user/name", name="user_edit_name", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function userEditNameAction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $user = new User;

        // Retrieving previous parameters.
        $user->setFirstName($this->getUser()->getFirstName());
        $user->setLastName($this->getUser()->getLastName());
        $user->setEmail($this->getUser()->getEmail());
        $user->setUsername($this->getUser()->getUsername());
        $user->setAlt($this->getUser()->getAlt());

        $formName = $this->createFormBuilder($user)
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('conf_password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();
        
        $formName->handleRequest($request);

        if ($formName->isSubmitted() && $formName->isValid()) {
            $user = $formName->getData();

            // We verify that the user submitted a correct password.
            if (password_verify($user->getConfPassword(), $this->getUser()->getPassword())) {
                $em = $this->getDoctrine()->getManager();

                $this->getUser()->setFirstName($user->getFirstName());
                $this->getUser()->setLastName($user->getLastName());

                // And finaly, we save changes.
                $em->flush();

                // And we redirect user to home page.
                return $this->redirectToRoute('app_home');
            } else {
                return $this->redirectToRoute('user_edit');
            }
        }
    }

    /**
     * Edit an user username
     *
     * @Route("/{_locale}/edit/user/username", name="user_edit_username", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function userEditUserNameAction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $user = new User;

        // Retrieving previous parameters.
        $user->setFirstName($this->getUser()->getFirstName());
        $user->setLastName($this->getUser()->getLastName());
        $user->setEmail($this->getUser()->getEmail());
        $user->setUsername($this->getUser()->getUsername());
        $user->setAlt($this->getUser()->getAlt());

        $formUserName = $this->createFormBuilder($user)
            ->add('username', TextType::class)
            ->add('conf_password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $formUserName->handleRequest($request);

        if ($formUserName->isSubmitted() && $formUserName->isValid()) {
            $user = $formUserName->getData();

            // We verify that the user submitted a correct password.
            if (password_verify($user->getConfPassword(), $this->getUser()->getPassword())) {
                $em = $this->getDoctrine()->getManager();
                
                $this->getUser()->setUsername($user->getUsername());

                // And finaly, we save changes.
                $em->flush();

                // And we redirect user to home page.
                return $this->redirectToRoute('app_home');
            } else {
                return $this->redirectToRoute('user_edit');
            }
        }
    }

    /**
     * Edit an user email
     *
     * @Route("/{_locale}/edit/user/email", name="user_edit_email", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function userEditEmailAction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $user = new User;

        // Retrieving previous parameters.
        $user->setFirstName($this->getUser()->getFirstName());
        $user->setLastName($this->getUser()->getLastName());
        $user->setEmail($this->getUser()->getEmail());
        $user->setUsername($this->getUser()->getUsername());
        $user->setAlt($this->getUser()->getAlt());

        $formEmail = $this->createFormBuilder($user)
            ->add('email', RepeatedType::class, array(
                'type' => EmailType::class
            ))
            ->add('conf_password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();
    
        $formEmail->handleRequest($request);

        if ($formEmail->isSubmitted() && $formEmail->isValid()) {
            $user = $formEmail->getData();

            // We verify that the user submitted a correct password.
            if (password_verify($user->getConfPassword(), $this->getUser()->getPassword())) {
                $em = $this->getDoctrine()->getManager();
                
                $this->getUser()->setEmail($user->getEmail());

                // And finaly, we save changes.
                $em->flush();

                // And we redirect user to home page.
                return $this->redirectToRoute('app_home');
            } else {
                return $this->redirectToRoute('user_edit');
            }
        }
    }

    /**
     * Edit an user password
     *
     * @Route("/{_locale}/edit/user/password", name="user_edit_password", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function userEditPasswordAction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $user = new User;

        // Retrieving previous parameters.
        $user->setFirstName($this->getUser()->getFirstName());
        $user->setLastName($this->getUser()->getLastName());
        $user->setEmail($this->getUser()->getEmail());
        $user->setUsername($this->getUser()->getUsername());
        $user->setAlt($this->getUser()->getAlt());

        $formPassword = $this->createFormBuilder($user)
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class
            ))
            ->add('conf_password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $user = $formPassword->getData();

            // We verify that the user submitted a correct password.
            if (password_verify($user->getConfPassword(), $this->getUser()->getPassword())) {
                $em = $this->getDoctrine()->getManager();
                
                $this->getUser()->setPassword(password_hash($user->getPassword(), PASSWORD_ARGON2I));

                // And finaly, we save changes.
                $em->flush();

                // And we redirect user to home page.
                return $this->redirectToRoute('app_home');
            } else {
                return $this->redirectToRoute('user_edit');
            }
        }
    }
}
