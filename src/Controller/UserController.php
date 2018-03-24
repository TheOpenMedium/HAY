<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * A controller related to the User entity
 *
 * List of actions:
 * * userShowAction(User $user, $id) -- user_show
 */
class UserController extends Controller
{
    /**
     * Render a user page
     *
     * @param User $user The user to show
     * @param int $id The user id
     *
     * @Route("/{_locale}/show/user/{id}", name="user_show", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function userShowAction(User $user)
    {
        // Fetching Post.
        $postList = $user->getPosts();;

        // If there is one Post or more:
        if ($postList) {
            // We replace new lines by the <br /> tag.
            foreach ($postList as $post) {
                $content = $post->getContent();
                $post->setContent(preg_replace('#\n#', '<br />', $content));
                if ($post->getComments()) {
                    foreach ($post->getComments() as $comment) {
                        $c = $comment->getComment();
                        $comment->setComment(preg_replace('#\n#', '<br />', $c));
                    }
                }
            }
        }

        // All that is rendered with the user show template sending Post List, Comment List and User.
        return $this->render('user/showUser.html.twig', array(
            'postList' => $postList,
            'user' => $user
        ));
    }
}
