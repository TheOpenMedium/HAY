<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Form\EditUserType;
use App\Form\ChildUserType;
use App\Controller\AjaxController;
use App\Controller\AppController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\SwitchUserToken;

/**
 * A controller related to the User entity
 *
 * List of actions:
 * * userShowAction(User $user)       -- user_show
 * * userEditAction(Request $request) -- user_edit
 * * userTagAction(Request $request)  -- user_tag
 */
class UserController extends AbstractController
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
        $this->denyAccessUnlessGranted('user.edit');

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

    /**
     * Tag a user
     *
     * @Route("/{_locale}/tag", name="user_tag", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function userTagAction(Request $request)
    {
        if (empty($_POST['tag'])) {
            throw new \Exception('POST data is missing.');
        }
        $tag = \explode('/', $_POST['tag']);
        unset($tag[0]);
        $tag = \array_values($tag);
        $result = [];
        $user;
        $roles = $this->getParameter('roles');
        foreach ($tag as $key => $value) {
            if ($key % 2 == 0) {
                if (!\in_array(\strtoupper($value), $roles['list'])) {
                    if (!empty($roles['alias'][\strtolower($value)])) {
                        $value = $roles['alias'][\strtolower($value)];
                    } else {
                        return new Response(\json_encode(FALSE));
                    }
                }
                $result['role'] = \strtoupper($value);
            } else {
                $result['id'][] = $value;
            }
        }
        foreach ($result['id'] as $key => $value) {
            if ($key != \sizeof($result['id']) - 1) {
                // TODO: This will be used for a future feature (communities and groups).
                return new Response(\json_encode(FALSE));
            } else {
                if ($value[0] == '#') {
                    $user = $this->getDoctrine()
                        ->getRepository(User::class)
                        ->find(\substr($value, 1));
                    if (empty($user)) {
                        return new Response(\json_encode());
                    }
                } elseif ($value[0] == '@') {
                    $user = $this->getDoctrine()
                        ->getRepository(User::class)
                        ->findOneByUsername(\substr($value, 1));
                    if (empty($user)) {
                        return new Response(\json_encode(FALSE));
                    }
                } else {
                    return new Response(\json_encode(FALSE));
                }
            }
        }
        if (empty($user)) {
            // TODO: This will be used for a future feature (role-based users tag).
            return new Response(\json_encode(FALSE));
        } elseif ($user) { // TODO: Check if user has the role
        } else {
            return new Response(\json_encode(FALSE));
        }
        return new Response(\json_encode(['url' => $this->generateUrl('user_show', ['id' => $user->getId()]), 'user' => [
            "id" => $user->getId(),
            "first_name" => $user->getFirstName(),
            "last_name" => $user->getLastName(),
            "username" => $user->getUsername(),
            "image" => $user->getUrl()
        ]]));
    }

    /**
     * Tag a user
     *
     * @Route("/{_locale}/create/child/user", name="user_child_creation", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function userChildCreationAction(Request $request)
    {
        $user = $this->getUser();
        
        if ($user->getIsChild()) {
            throw new AccessDeniedException();
        }
        
        $child = new User();
        $form = $this->createForm(ChildUserType::class, $child);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appController = new AppController();

            $child = $form->getData();
            $child->setId($appController->generateIdAction($this->getDoctrine()->getRepository(User::class), 7));
            $child->setPassword($user->getPassword());
            $child->setIsChild(true);
            $child->setMailConf(true);

            if ($form->get('is_page')->getData() == "true") {
                // Pages
                $child->setRoles(["ROLE_PAGE"]);
            } else {
                // Child Users
                $child->setRoles(["ROLE_USER"]);
            }
            
            $child->addParent($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($child);
            $em->flush();

            // And redirecting user to the home page.
            return $this->redirectToRoute('app_home');
        }

        // All that is rendered with the new child user template sending the Form.
        return $this->render('user/newChildUser.html.twig', [
            'child_user' => $form->createView()
        ]);
    }

    /**
     * Render the child user list of an user
     *
     * @Route("/{_locale}/child/user", name="user_child", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function childUserAction()
    {
        $user = $this->getUser();
        $parent = null;
        $childUsers = null;

        if ($user->getIsChild()) {
            $user = $this->getDoctrine()->getRepository(User::class)->find($this->get('security.token_storage')->getToken()->getOriginalToken()->getUser()->getId());
            $parent = $user;
        }

        // Getting Child Users of current user.
        $childUsers = $user->getChildren()->toArray();

        // Sorting that user's array by the first name with the "cmp" function
        if ($childUsers) {
            usort($childUsers, array('App\Controller\FriendController', 'cmp'));
        }

        // All that is rendered with the childUser template sending childUser List.
        return $this->render('users/childUser.html.twig', array(
            'parent' => $parent,
            'childUsers' => $childUsers
        ));
    }

    /**
     * Change the current user
     *
     * @Route("/{_locale}/change/user/{id}", name="user_change", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function changeUserAction(User $user)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $parent = $this->getUser();
        if ($parent->getIsChild()) {
            $parent = $this->getDoctrine()->getRepository(User::class)->find($this->get('security.token_storage')->getToken()->getOriginalToken()->getUser()->getId());
        }

        if (in_array("ROLE_PAGE", $user->getRoles()) ||
            !in_array($parent, array_merge($user->getParents()->toArray(), $user->getChildren()->toArray(), [$parent]))) {
            throw new AccessDeniedException();
        }

        $token_storage = $this->get('security.token_storage');
        $original_token = $token_storage->getToken();
        if ($original_token instanceof SwitchUserToken) {
            $original_token = $original_token->getOriginalToken();
        }
            
        $token = new SwitchUserToken($user, "none", "main", $user->getRoles(), $original_token);
        $token_storage->setToken($token);

        // Redirecting to home.
        return $this->redirectToRoute('app_home');
    }
}
