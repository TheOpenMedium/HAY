<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Form\EditUserType;
use App\Controller\AjaxController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

        // Creating a new User as "interface".
        $user = new User;

        // Retrieving old informations.
        $user->setFirstName($this->getUser()->getFirstName());
        $user->setLastName($this->getUser()->getLastName());
        $user->setUsername($this->getUser()->getUsername());
        $user->setAlt($this->getUser()->getAlt());

        // Creating the form.
        $form = $this->createForm(EditUserType::class, $user);

        // Handling the request.
        $form->handleRequest($request);

        // Editing the user if a request have been sent.
        if ($form->isSubmitted()) {
            // Checking the password confirmation
            if (!password_verify($form->get('conf_password')->getData(), $this->getUser()->getPassword())) {
                return $this->redirectToRoute('user_edit');
            }
            // Handling the user's first name
            if ($form->getData()->getFirstName() != $this->getUser()->getFirstName()) {
                $this->getUser()->setFirstName($form->getData()->getFirstName());
            }
            // Handling the user's last name
            if ($form->getData()->getLastName() != $this->getUser()->getLastName()) {
                $this->getUser()->setLastName($form->getData()->getLastName());
            }
            // Handling the user's username
            if ($form->getData()->getUsername() != $this->getUser()->getUsername()) {
                $this->getUser()->setUsername($form->getData()->getUsername());
            }
            // Handling the user's image
            if ($form->get('file')->getData() != null) {
                // We verify that the user hasn't already an image.
                if ($this->getUser()->getUrl() != '/ressources/icon.svg') {
                    // If it's the case, we remove it.
                    unlink(__dir__.'/../../public'.$this->getUser()->getUrl());
                }

                // Then, we upload the file.
                $extension = $form->get('file')->getData()->guessExtension();
                if (!$extension) {
                    $extension = 'png';
                }
                $form->get('file')->getData()->move(__dir__.'/../../public/usr_img/', $this->getUser()->getId().'.'.$extension);

                // And, we add the url.
                $this->getUser()->setUrl('/usr_img/'.$this->getUser()->getId().'.'.$extension);
            }
            // Handling the user's image alt
            if ($form->getData()->getAlt() != $this->getUser()->getAlt()) {
                $this->getUser()->setAlt($form->getData()->getAlt());
            }
            // Handling the user's e-mail
            if ($form->getData()->getEmail() != $this->getUser()->getEmail() && $form->getData()->getEmail() != null) {
                $this->getUser()->setEmail($form->getData()->getEmail());
            }
            // Handling the user's password
            if ($form->getData()->getPassword() != $this->getUser()->getPassword() && $form->getData()->getPassword() != null) {
                $this->getUser()->setPassword(password_hash($form->getData()->getPassword(), PASSWORD_ARGON2I));
            }

            // And finaly, we save changes.
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // And we redirect user to home page.
            return $this->redirectToRoute('app_home');
        }

        // All that is rendered with the user edit template sending the Form.
        return $this->render('user/editUser.html.twig', [
            'edit_user' => $form->createView()
        ]);
    }
}
