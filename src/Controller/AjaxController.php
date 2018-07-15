<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A controller related ***mainly*** to Ajax
 * 
 * List of actions:
 * * postGenerateAction($scope = "all", $order = "DESC", $limit = 10, $date = NULL, $from_id = NULL, $user_id = NULL) -- post_gen
 * * postRenderingAction($postList)
 * * isNewPostsSendedAction(int $last_id, string $scope = "all", int $user_id = NULL)                                 -- post_sended
 * * newPostsRenderedAction(int $last_id, string $scope = "all", int $user_id = NULL)                                 -- post_rendered
 * 
 * DOCUMENTATION : How to use this class with Ajax
 * 
 * The first function (@see postGenerateAction) is used to save code and has many options that you can
 * use (it's a flexible function). You can see it's own documentation, it's very detailed. This function
 * is also used for other reasons than Ajax (for fetching a post list for example), it also able posts
 * to be flexible, so when you changes things in the post list it changes absolutely all post lists.
 * 
 * The second function (@see postRenderingAction) is also used to save code. IT CAN'T BE ACCESSED BY URL.
 * 
 * The third function (@see isNewPostsSendedAction) is used to verify how many posts have been sent since
 * the specified ID. This function is used instead of postGenerateAction directly because JavaScript need
 * a url (@see templates/post/newPosts.html.twig). And we can't pass to the router through the url a null
 * type, it detect as a string ("null"). AND, the postGenerateAction, has this following prototype:
 * `postGenerateAction($scope = "all", $order = "DESC", $limit = 10, $date = NULL, $from_id = NULL,
 * $user_id = NULL);` as you can see, there's the $limit and the $date parameter that we need to be null.
 * And, in all cases, we would have needed to pass through a function to return a `Response()` with a
 * `json_encode()`.
 * 
 * The fourth function (@see newPostsRenderedAction) has ***exactly*** the same parameter than the
 * previous one, but this time, instead of returning the number of new posts, it return a html code
 * containing the new posts and the last post ID, seperated by a slash '/', to modify the $last_id
 * parameter in the JavaScript request URLs. @see templates/post/newPosts.html.twig for more informations.
 */
class AjaxController extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // The Post Entity part

    /**
     * Generate a (group of) post(s)
     * 
     * @param string $scope The scope for fetching posts / available values : "all", "user" (id specified by $user_id)
     * @param string $order The order for fetching posts / available values : "DESC", "ASC"
     * @param int|null $limit The limit for fetching posts / available values : int, NULL (VERY DANGEROUS, USE AJAX INSTEAD!)
     * @param string $date The date interval for fetching posts / available values : string (see the examples), NULL
     * @param int|null $from_id The id from where we begin fetching (used mainly for ajax) (the specified id is included) / available values : int, NULL
     * @param int|null $user_id The user's id for fetching only his posts (only with $scope "user") / available values : int, NULL
     * 
     * @example Date Interval string : "^HERE THE BEGING DATE^ $HERE THE END DATE$" // Date can be in form of DATE or DATE TIME (use the sql syntax)
     * @example "^2018-8-28^ $2018-12-5 8:00:30$" if you don't want to specify one of the two dates replace it by NULL "^2018-8-28^ $NULL$"
     * 
     * @todo Creating the scopes values : "friends", "frd_and_frd", "subscribed", "sub_users", "sub_pages", "sub_groups", "moderation", "my_posts"
     * @todo Configuring the $date propertie
     * 
     * @return Post[] $postList The post list
     * 
     * @Route("/{_locale}/generate/post/{scope}/{order}/{limit}/{date}/{from_id}/{user_id}", name="post_gen", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function postGenerateAction(string $scope = "all", string $order = "DESC", ?int $limit = 10, string $date = NULL, ?int $from_id = NULL, ?int $user_id = NULL)
    {
        // Fetching Post.

        if ($scope == "all")
        {
            if ($limit) {
                $postList = $this->container->get('doctrine')->getRepository(Post::class)->findPost($order, $limit);
            }

            // Mainly for Ajax
            else if (!$limit && $from_id) {
                // Ajax CAN'T work with an ASC order... for obvious reasons... (You can't send posts back in time)
                if ($order == "DESC") {
                    $postList = $this->container->get('doctrine')->getRepository(Post::class)->findPostWithNoLimitAndFromId($from_id);
                }
            }
        }

        else if ($scope == "user")
        {
            if ($limit) {
                $postList = $this->container->get('doctrine')->getRepository(Post::class)->findPostByUser($user_id, $order, $limit);
            }

            // Mainly for Ajax
            else if (!$limit && $from_id) {
                // Ajax CAN'T work with an ASC order... for obvious reasons... (You can't send posts back in time)
                if ($order == "DESC") {
                    $postList = $this->container->get('doctrine')->getRepository(Post::class)->findPostByUserWithNoLimitAndFromId($user_id, $from_id);
                }
            }
        }
    
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
        
        return $postList;
    }

    /**
     * Rendering post list to html
     * 
     * @param Post[] $postList The post list to render
     * 
     * @example Use this function for Ajax with Javascript
     * 
     * @return string $html The html code with rendered post list
     * 
     * NOTE : CAN'T BE ACCESSED BY URL
     */
    public function postRenderingAction($postList)
    {
        return $this->render('post/postDisplay.html.twig', array('postList' => $postList));
    }

    /**
     * Verifying if new posts have been sended
     * 
     * @param int $last_id The last post id sended
     * @param string $scope The post scope, @see above (postGenerateAction) for information about the scope parameter
     * @param string $order The order for fetching posts / available values : "DESC", "ASC"
     * @param int|null $user_id For the "user" scope
     * 
     * @example Use this function for Ajax with JavaScript (because NULL type, can't be passed with url, it detect it as a string, @see the class documentation)
     * 
     * @return Response The response to a http request so JavaScript can understand it !
     * 
     * @Route("/{_locale}/new/post/{last_id}/{scope}/{order}/{user_id}", name="post_sended", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function isNewPostsSendedAction(int $last_id, string $scope = "all", string $order = "DESC", ?int $user_id = NULL)
    {
        $post = $this->postGenerateAction($scope, $order, NULL, NULL, $last_id, $user_id);
        return new Response(sizeof($post));
    }

    /**
     * Rendering new posts @see isNewPostsSendedAction for more information on how to use this function
     * 
     * @return string $html The html code with rendered post list
     * 
     * @Route("/{_locale}/html/new/post/{last_id}/{scope}/{order}/{user_id}", name="post_rendered", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function newPostsRenderedAction(int $last_id, string $scope = "all", string $order = "DESC", ?int $user_id = NULL)
    {
        $postList = $this->postGenerateAction($scope, $order, NULL, NULL, $last_id, $user_id);
        return new Response($postList[0]->getId() . '/' . $this->postRenderingAction($postList)->getContent());
    }
}
