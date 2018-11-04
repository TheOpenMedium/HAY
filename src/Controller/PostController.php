<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Controller\SurveyController;
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
 * * postShowAction($id)                              -- post_show
 * * postEditAction(Request $request, Post $postEdit) -- post_edit
 * * postDeleteAction(Post $post)                     -- post_delete
 */
class PostController extends Controller
{
    /**
     * Render a single post
     *
     * @param Post $post The post to render
     *
     * @Route("/{_locale}/show/post/{id}", name="post_show", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function postShowAction(Post $post)
    {
        // We replace new lines by the <br /> tag.
        $content = $post->getContent();
        $post->setContent(\ParsedownHAYFlavored::instance()
            ->setSafeMode(true)
            ->setBreaksEnabled(true)
            ->setMarkupEscaped(true)
            ->setUrlsLinked(true)
            ->text($content));
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
    public function postEditAction(Request $request, SurveyController $sc, Post $post)
    {
        $user = $this->getUser();

        // Checking that the author and the user are the same.
        if ($post->getUser()->getId() == $user->getId()) {
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

                $post = $sc->surveyCheckPostAction($post);
                $em = $this->getDoctrine()->getManager();

                // And finaly, we save changes.
                $em->flush();

                // And we redirect user to home page.
                return $this->redirectToRoute('app_home');
            }

            $color = $post->getColor();
            $size = $post->getSize();

            // All that is rendered with the post edit template sending a From and the default Color and Size.
            return $this->render('post/editPost.html.twig', array(
                'form' => $form->createView(),
                'color' => $color,
                'size' => $size
            ));
        } else {
            // If the user can't modify the post, he's redirected to home page.
            return $this->redirectToRoute('app_home');
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
        return $this->redirectToRoute('app_home');
    }
}
