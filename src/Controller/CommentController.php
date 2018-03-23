<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Status;
use App\Entity\Comment;
use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CommentController extends Controller
{
    /**
     * @Route("/{_locale}/comment/{id}/{_color}",
     *     defaults={"_color": "696"},
     *     name="comment",
     *     requirements={"_locale": "en|fr"}
     *     )
     */
    public function commentAction(Request $request, $id, $_color)
    {
        $com = new Comment();

        // Creating the Form if the user want to submit a comment.
        $comment = $this->createFormBuilder($com)
            ->add('comment', TextareaType::class)
            ->add('id_user', HiddenType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $comment->handleRequest($request);

        // If a form was submitted, the Form's data are retrieved.
        if ($comment->isSubmitted() && $comment->isValid()) {
            $sendComment = $comment->getData();
            $sendComment->setIdStatus($id);

            // Saving comment in database.
            $em = $this->getDoctrine()->getManager();
            $em->persist($sendComment);
            $em->flush();

            // Sending a notification (notification type '0').
            $notification = new Notification;

            $notification->setNotificationType(0);
            $notification->setIdUser($this->getUser()->getId());
            $notifContent = (strlen($sendComment->getComment()) > 40) ? substr($sendComment->getComment(), 0, 40) . "..." : $sendComment->getComment();
            $notification->setContent($notifContent);
            $notification->setUrl('app_status_show');
            $notification->setUrlId($sendComment->getIdStatus());

            $em = $this->getDoctrine()->getManager();
            $em->persist($notification);
            $em->flush();

            // Finally, redirecting to home page.
            return $this->redirect($this->generateUrl('app_index'));
        }

        // All that is rendered with the comment template sending a Form, Color of the status and the Status Id.
        return $this->render('comment/comment.html.twig', array(
            'comment' => $comment->createView(),
            'color' => $_color,
            'id' => $id
        ));
    }

    /**
     * @Route("/{_locale}/show/comment/{id}", name="comment_show", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function commentShowAction($id)
    {
        // Fetching the comment.
        $comment = $this->getDoctrine()->getRepository(Comment::class)->findCommentById($id);

        // All that is rendered with the comment show template sending the Comment.
        return $this->render('comment/showComment.html.twig', array(
            'comment' => $comment[0]
        ));
    }

    /**
     * @Route("/{_locale}/edit/comment/{id}", name="comment_edit", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function commentEditAction(Request $request, Comment $commentEdit)
    {
        $user = $this->getUser();

        // Checking that the author and the user are the same.
        if ($commentEdit->getIdUser() == $user->getId()) {
            $comment = new Comment();

            // Adding last values as default.
            $comment->setComment($commentEdit->getComment());

            // Creating a Form to edit comment.
            $form = $this->createFormBuilder($comment)
                ->add('comment', TextareaType::class)
                ->add('id_user', HiddenType::class)
                ->add('submit', SubmitType::class)
                ->getForm();

            $form->handleRequest($request);

            // If a comment was edited, the Form's data are retrieved.
            if ($form->isSubmitted() && $form->isValid()) {
                $comment = $form->getData();

                $em = $this->getDoctrine()->getManager();

                // And the comment is edited.
                $commentEdit->setComment($comment->getComment());

                $em->flush();

                return $this->redirectToRoute('app_index');
            }

            // All that is rendered with the comment edit template sending a Form.
            return $this->render('comment/editComment.html.twig', array(
                'comment' => $form->createView()
            ));
        } else {
            //If the user is not the author, he's redirected to home page.
            return $this->redirectToRoute('app_index');
        }
    }

    /**
     * @Route("/{_locale}/delete/comment/{id}", name="comment_delete", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function commentDeleteAction(Comment $comment, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        // Checking that the author and the user that want to delete the comment are the same.
        if ($comment->getIdUser() == $user->getId()) {
            // Removing the comment.
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        // User is redirected to home page.
        return $this->redirectToRoute('app_index');
    }
}
