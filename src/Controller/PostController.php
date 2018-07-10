<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A controller related to the Post entity
 *
 * List of actions:
 * * postShowAction($id)                                                                                              -- post_show
 * * postEditAction(Request $request, Post $postEdit)                                                                 -- post_edit
 * * postDeleteAction(Post $post)                                                                                     -- post_delete
 * * postGenerateAction($scope = "all", $order = "DESC", $limit = 10, $date = NULL, $from_id = NULL, $user_id = NULL) -- post_gen
 * * isNewPostsSended(int $last_id, string $scope = "all", int $user_id = NULL)                                       -- post_sended
 */
class PostController extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Render a single post
     *
     * @param Post $id The post to render
     *
     * @Route("/{_locale}/show/post/{id}", name="post_show", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function postShowAction(Post $post)
    {
        // We replace new lines by the <br /> tag.
        $content = $post->getContent();
        $post->setContent(preg_replace('#\n#', '<br />', $content));
        if ($post->getComments()) {
            foreach ($post->getComments() as $comment) {
                $c = $comment->getComment();
                $comment->setComment(preg_replace('#\n#', '<br />', $c));
            }
        }

        $postList[0] = $post;

        // All that is rendered with the post show template sending Post List and Comment List.
        return $this->render('post/showPost.html.twig', array(
            'postList' => $postList
        ));
    }

    /**
     * Render the post edit page
     *
     * @param Request $request The HTTP request
     * @param Post $postEdit The post to edit
     *
     * @Route("/{_locale}/edit/post/{id}", name="post_edit", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function postEditAction(Request $request, Post $postEdit)
    {
        $user = $this->getUser();

        // Checking that the author and the user are the same.
        if ($postEdit->getUser()->getId() == $user->getId()) {
            $post = new Post();

            // Adding last values as default.
            $post->setContent($postEdit->getContent());
            $post->setColor($postEdit->getColor());
            $post->setSize($postEdit->getSize());
            $post->setFont($postEdit->getFont());

            // Creating the form.
            $form = $this->createFormBuilder($post)
                ->add('content', TextareaType::class)
                ->add('color', ChoiceType::class, array(
                    'choices' => array(
                        '000', '222', '696', '999', 'DDD', 'FFF',
                        'E00', '72C', '008', '099', '0A0', 'F91',
                        'F00', 'D0F', '22F', '6DF', '0F0', 'FD0',
                        'F44', 'F2E', '08F', '0FF', 'BF0', 'EE0',
                        'F05', 'F6F', '0AE', '9FF', '5F9', 'FF0'
                    ),
                    'multiple' => false,
                    'expanded' => true
                ))
                ->add('size', IntegerType::class)
                ->add('submit', SubmitType::class)
                ->getForm();

            $form->handleRequest($request);

            // If a post was edited, we retrieve data.
            if ($form->isSubmitted() && $form->isValid()) {
                $post = $form->getData();
                $post->setFont('SS');

                $em = $this->getDoctrine()->getManager();

                // We replace old datas.
                $postEdit->setContent($post->getContent());
                $postEdit->setColor($post->getColor());
                $postEdit->setSize($post->getSize());
                $postEdit->setFont($post->getFont());

                // And finaly, we save changes.
                $em->flush();

                // And we redirect user to home page.
                return $this->redirectToRoute('app_index');
            }

            $color = $postEdit->getColor();
            $size = $postEdit->getSize();

            // All that is rendered with the post edit template sending a From and the default Color and Size.
            return $this->render('post/editPost.html.twig', array(
                'form' => $form->createView(),
                'color' => $color,
                'size' => $size
            ));
        } else {
            // If the user can't modify the post, he's redirected to home page.
            return $this->redirectToRoute('app_index');
        }
    }

    /**
     * Delete a post from the database
     *
     * @param Post $post The post to delete
     *
     * @Route("/{_locale}/delete/post/{id}", name="post_delete", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function postDeleteAction(Post $post)
    {
        $user = $this->getUser();

        // And delete it if the user and the author are the same.
        if ($post->getUser()->getId() == $user->getId()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        // Finally the user is redirected to home page.
        return $this->redirectToRoute('app_index');
    }

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
     * @example Date Interval string : "$HERE THE BEGING DATE$ ^HERE THE END DATE^" // Date can be in form of DATE or DATE TIME (use the sql syntax)
     * @example "$2018-8-28$ ^2018-12-5 8:00:30^" if you don't want to specify one of the two dates replace it by NULL "$2018-8-28$ ^NULL^"
     * 
     * @todo Creating the scopes values : "friends", "frd_and_frd", "subscribed", "sub_user", "sub_pages", "sub_groups", "moderation", "my_posts"
     * 
     * @return string $html The html used for rendering the post
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

            else if (!$limit && $from_id) {
                $postList = $this->container->get('doctrine')->getRepository(Post::class)->findPostWithNoLimitAndFromId($order, $from_id);
            }
        }

        else if ($scope == "user")
        {
            $postList = $this->container->get('doctrine')->getRepository(Post::class)->findPostByUser($user_id, $order, $limit);
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
     * Verifying if new posts have been sended
     * 
     * @param int $last_id The last post id sended
     * @param string $scope The post scope, @see above (postGenerateAction) for information about the scope parameter
     * @param int|null $user_id For the "user" scope
     * 
     * @example Use this function for Ajax with JavaScript
     * 
     * @return int|false $response The number of new posts sended or false if no post was sended
     * 
     * @Route("/{_locale}/new/post/{last_id}/{scope}/{user_id}", name="post_sended", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function isNewPostsSended(int $last_id, string $scope = "all", int $user_id = NULL)
    {
        $post = $this->postGenerateAction($scope, "DESC", NULL, NULL, $last_id, NULL);
        return (sizeof($post) > 0 ? sizeof($post) : false);
    }
}
