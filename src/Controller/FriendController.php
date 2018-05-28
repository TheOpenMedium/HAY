<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\FriendRequest;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * A controller related to the FriendRequest entity
 *
 * List of actions:
 * * friendAction()              -- friend
 * * friendAddAction(User $user) -- friend_add
 *
 * List of functions:
 * * cmp($a, $b): List sorting function for usort()
 */
class FriendController extends Controller
{
    /**
     * Render the friend list of an user
     *
     * @Route("/{_locale}/friend", name="friend", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function friendAction()
    {
        $friends = null;

        // Getting Friends of current user.
        $friendObject = $this->getUser()->getFriends();

        // "Converting" the Doctrine's array object to a PHP's array object
        foreach ($friendObject as $key => $value) {
            $friends[$key] = $value;
        }

        // Sorting that user's array by the first name with the "cmp" function
        if ($friends) {
            usort($friends, array('App\Controller\FriendController', 'cmp'));
        }

        // All that is rendered with the friend template sending Friend List.
        return $this->render('users/friend.html.twig', array(
            'friends' => $friends
        ));
    }

    /**
     * List sorting function for usort()
     */
    public function cmp($a, $b)
    {
        return strcmp($a->getFirstName(), $b->getFirstName());
    }

    /**
     * Add a friend to the current user or send a friend request
     *
     * @param User $user The user's to add as a friend
     *
     * @Route("/{_locale}/add/friend/{id}", name="friend_add", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function friendAddAction(User $request, $id)
    {
        $user = $this->getUser();
        $bool = false;

        // Does the users are already friends?
        foreach ($user->getFriends() as $friend) {
            if ($friend->getId() == $request->getId()) {
                $bool = true;
            }
        }

        // Does the user has already sent a friend request?
        foreach ($user->getFriendRequests() as $friendRequest) {
            if ($friendRequest->getToUser()->getId() == $request->getId()) {
                $bool = true;
            }
        }

        // Be sure that the users are not already friend or the user did not already sent a request.
        if (!$bool) {
            $bool = false;

            // Does the current user has already a request to be friend with the requested user?
            foreach ($user->getRequestedFriends() as $requestedFriend) {
                if ($requestedFriend->getFromUser()->getId() == $request->getId()) {
                    $bool = true;
                    $rf = $requestedFriend;
                }
            }

            $em = $this->getDoctrine()->getManager();
            if ($bool) {
                // If it's the case, make these users friends and remove the request.
                $user->addFriend($request);
                $request->addFriend($user);

                // TODO: Sending notifications

                $request->removeFriendRequest($rf);
                $em->flush();
            } else {
                // Else, the user send a request.
                $newRequest = new FriendRequest;

                $newRequest->setFromUser($user);
                $newRequest->setToUser($request);

                $em->persist($newRequest);
                $em->flush();
            }

            // Then, redirect to user's page.
            return $this->redirectToRoute('user_show', array(
                'id' => $id
            ));
        }

        // Else, redirect to user's page.
        return $this->redirectToRoute('user_show', array(
            'id' => $id
        ));
    }
}
