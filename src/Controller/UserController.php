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
 * * userShowAction(User $user) -- user_show
 * * userEditAction(Request $request) -- user_edit
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
            'scope' => 'user'
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
        $form = $this->createFormBuilder($user)
            ->add('file', FileType::class)
            ->add('alt', TextType::class)
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('username', TextType::class, array('required' => false))
            ->add('email', RepeatedType::class, array(
                'type' => EmailType::class
            ))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class
            ))
            ->add('conf_password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        // If an user was edited, we retrieve data.
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            // We verify that the user submitted a correct password.
            if (password_verify($user->getConfPassword(), $this->getUser()->getPassword())) {
                $em = $this->getDoctrine()->getManager();

                // Then, we replace old datas.
                $this->getUser()->setFirstName($user->getFirstName());
                $this->getUser()->setLastName($user->getLastName());
                $this->getUser()->setEmail($user->getEmail());
                $this->getUser()->setUsername($user->getUsername());
                $this->getUser()->setAlt($user->getAlt());
                if (!password_verify($user->getConfPassword(), $this->getUser()->getPassword())) {
                    $this->getUser()->setPassword(password_hash($user->getPassword(), PASSWORD_ARGON2I));
                }

                // We see if he didn't upload an image.
                if ($user->getFile() !== null) {
                    // If it's the case, we verify that the user hasn't already an image.
                    if ($this->getUser()->getUrl() != '/ressources/icon.png') {
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
                }

                // And finaly, we save changes.
                $em->flush();

                // And we redirect user to home page.
                return $this->redirectToRoute('app_index');
            }
            else {
                // All that is rendered with the user edit template sending a From and the Error.
                return $this->render('user/editUser.html.twig', array(
                    'form' => $form->createView(),
                    'error' => 'error_password'
                ));
            }
        }

        // All that is rendered with the user edit template sending a From.
        return $this->render('user/editUser.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
