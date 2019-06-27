<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Notification;
use App\Form\CommentType;
use App\Controller\AppController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * A controller related to the Comment entity
 *
 * List of action:
 * * commentAction(Request $request, $id, $_color)             -- comment
 * * commentShowAction(Comment $comment)                       -- comment_show
 * * commentEditAction(Request $request, Comment $commentEdit) -- comment_edit
 * * commentDeleteAction(Comment $comment)                     -- comment_delete
 */
class CommentController extends AbstractController
{
    /**
     * Render the comment form
     *
     * @param Request $request The HTTP request
     * @param string $id The post id
     * @param string $_color The color user for rendering the comment form
     *
     * @Route("/{_locale}/comment/{id}",
     *     defaults={"_color": "696"},
     *     name="comment",
     *     requirements={"_locale": "%app.locales%"}
     *     )
     */
    public function commentAction(Request $request, $id)
    {
        $com = new Comment();

        // Creating the Form if the user want to submit a comment.
        $comment = $this->createForm(CommentType::class, $com);

        $comment->handleRequest($request);

        // If a form was submitted, the Form's data are retrieved.
        if ($comment->isSubmitted() && $comment->isValid()) {
            $appController = new AppController;

            $sendComment = $comment->getData();
            $sendComment->setId($appController->generateIdAction($this->getDoctrine()->getRepository(Comment::class), 10));
            $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
            $sendComment->setPost($post);
            $sendComment->setUser($this->getUser());

            // Saving comment in database.
            $em = $this->getDoctrine()->getManager();
            $em->persist($sendComment);
            $em->flush();

            // Sending a notification (notification type '0').
            $notification = new Notification;

            // TODO: A better choice of notification's user
            $notification->setId($appController->generateIdAction($this->getDoctrine()->getRepository(Notification::class), 10));
            $notification->setType(0);
            $notification->setUser($this->getUser());
            $notifContent = (strlen($sendComment->getComment()) > 40) ? substr($sendComment->getComment(), 0, 40) . "..." : $sendComment->getComment();
            $notification->setContent($notifContent);
            $notification->setUrl('post_show');
            $notification->setUrlId($sendComment->getPost()->getId());

            $em = $this->getDoctrine()->getManager();
            $em->persist($notification);
            $em->flush();

            // Finally, redirecting to home page.
            return $this->redirect($this->generateUrl('app_home'));
        }

        // All that is rendered with the comment template sending a Form, Color of the post and the Post Id.
        return $this->render('comment/newComment.html.twig', array(
            'comment' => $comment->createView(),
            'id' => $id
        ));
    }

    /**
     * Render a single comment
     *
     * @param Comment $comment The comment to render
     *
     * @Route("/{_locale}/show/comment/{id}", name="comment_show", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function commentShowAction(Comment $comment)
    {
        // All that is rendered with the comment show template sending the Comment.
        return $this->render('comment/showComment.html.twig', array(
            'comment' => $comment
        ));
    }

    /**
     * Render the comment edit page
     *
     * @param Request $request The HTTP request
     * @param Comment $commentEdit The comment to edit
     *
     * @Route("/{_locale}/edit/comment/{id}", name="comment_edit", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function commentEditAction(Request $request, Comment $comment)
    {
        $user = $this->getUser();

        // Checking that the author and the user are the same.
        if ($comment->getUser()->getId() == $user->getId()) {
            // Creating a Form to edit comment.
            $form = $this->createForm(CommentType::class, $comment);

            $form->handleRequest($request);

            // If a comment was edited, the Form's data are retrieved.
            if ($form->isSubmitted() && $form->isValid()) {
                $comment = $form->getData();

                $em = $this->getDoctrine()->getManager();

                $em->flush();

                return $this->redirectToRoute('app_home');
            }

            // All that is rendered with the comment edit template sending a Form.
            return $this->render('comment/editComment.html.twig', array(
                'comment' => $form->createView()
            ));
        } else {
            //If the user is not the author, he's redirected to home page.
            return $this->redirectToRoute('app_home');
        }
    }

    /**
     * Delete a comment from the database
     *
     * @param Comment $comment The comment to delete
     *
     * @Route("/{_locale}/delete/comment/{id}", name="comment_delete", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function commentDeleteAction(Comment $comment)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        // Checking that the author and the user that want to delete the comment are the same.
        if ($comment->getUser()->getId() == $user->getId()) {
            // Removing the comment.
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        // User is redirected to home page.
        return $this->redirectToRoute('app_home');
    }
}
