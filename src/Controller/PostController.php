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

/**
 * A controller related to the Post entity
 *
 * List of actions:
 * * postShowAction($id)                                  -- post_show
 * * postEditAction(Request $request, Post $postEdit) -- post_edit
 * * postDeleteAction(Post $post, $id)                -- post_delete
 */
class PostController extends Controller
{
    /**
     * Render a single post
     *
     * @param int $id The post id
     *
     * @Route("/{_locale}/show/post/{id}", name="post_show", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function postShowAction($id)
    {
        // Retrieving postList from the database.
        $postList = $this->getDoctrine()->getRepository(Post::class)->findPostById($id);
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

        // All that is rendered with the post show template sending Post List and Comment List.
        return $this->render('post/showPost.html.twig', array(
            'postList' => $postList,
            'commentList' => $commentList
        ));
    }

    /**
     * Render the post edit page
     *
     * @param Request $request The HTTP request
     * @param Post $postEdit The post to edit
     *
     * @Route("/{_locale}/edit/post/{id}", name="post_edit", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function postEditAction(Request $request/*, Post $postEdit*/)
    {
        $user = $this->getUser();

        // Checking that the author and the user are the same.
        if ($postEdit->getIdUser() == $user->getId()) {
            $post = new Post();

            // Adding last values as default
            $post->setContent($postEdit->getContent());
            $post->setColor($postEdit->getColor());
            $post->setSize($postEdit->getSize());
            $post->setFont($postEdit->getFont());

            // Creating the form
            $form = $this->createFormBuilder($post)
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
     * @param int $id The id of the post to delete
     *
     * @Route("/{_locale}/delete/post/{id}", name="post_delete", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function postDeleteAction(/*Post $post, */$id)
    {
        // Fetching the post and it's comments.
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $repository->findBy(['id_post' => $id]);

        $user = $this->getUser();

        // And delete it if the user and the author are the same.
        if ($post->getIdUser() == $user->getId()) {
            $entityManager->remove($post);

            // Then delete every comment related to the post.
            foreach ($comments as $comment) {
                $entityManager->remove($comment);
            }

            $entityManager->flush();
        }

        // Finally the user is redirected to home page.
        return $this->redirectToRoute('app_index');
    }
}
