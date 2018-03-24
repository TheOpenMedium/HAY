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
    public function userShowAction(/*User $user, */$id)
    {
        // Fetching the post of the requested user.
        $postList = $this->getDoctrine()->getRepository(Post::class)->findPostByUser(10, $id);
        $commentList = array();

        // If there is one Post or more :
        if ($postList) {
            // We replace new lines by the <br /> tag and we fetch from the database the 10 newer comments of each post.
            foreach ($postList as $post) {
                $content = $post[0]->getContent();
                $post[0]->setContent(preg_replace('#\n#', '<br />', $content));
                $commentList[] = $this->getDoctrine()->getRepository(Comment::class)->findComments(10, $post[0]->getId());
            }

            // Then, we replace new lines by the <br /> tag.
            foreach ($commentList as $commentPost) {
                if ($commentPost) {
                    foreach ($commentPost as $comment) {
                        $c = $comment[0]->getComment();
                        $comment[0]->setComment(preg_replace('#\n#', '<br />', $c));
                    }
                }
            }
        }

        // All that is rendered with the user show template sending Post List, Comment List and User.
        return $this->render('user/showUser.html.twig', array(
            'commentList' => $commentList,
            'postList' => $postList,
            'user' => $user
        ));
    }
}
