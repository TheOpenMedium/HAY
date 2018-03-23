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
     *     name="app_comment",
     *     requirements={"_locale": "en|fr"}
     *     )
     */
    public function commentAction(Request $request, $id, $_color)
    {
        $com = new Comment();

        $comment = $this->createFormBuilder($com)
            ->add('comment', TextareaType::class)
            ->add('id_user', HiddenType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $comment->handleRequest($request);

        if ($comment->isSubmitted() && $comment->isValid()) {
            $sendComment = $comment->getData();
            $sendComment->setIdStatus($id);

            $em = $this->getDoctrine()->getManager();
            $em->persist($sendComment);
            $em->flush();

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

            return $this->redirect($this->generateUrl('app_index'));
        }

        return $this->render('comment.html.twig', array(
            'comment' => $comment->createView(),
            'color' => $_color,
            'id' => $id
        ));
    }

    /**
     * @Route("/{_locale}/show/comment/{id}", name="app_comment_show", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function commentShowAction(Request $request, $id)
    {
        $comment = $this->getDoctrine()->getRepository(Comment::class)->findCommentById($id);

        return $this->render('showComment.html.twig', array(
            'comment' => $comment[0]
        ));
    }

    /**
     * @Route("/{_locale}/edit/comment/{id}", name="app_comment_edit", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function commentEditAction(Request $request, Comment $commentEdit)
    {
        $user = $this->getUser();

        if ($commentEdit->getIdUser() == $user->getId()) {
            $comment = new Comment();

            $comment->setComment($commentEdit->getComment());

            $form = $this->createFormBuilder($comment)
                ->add('comment', TextareaType::class)
                ->add('id_user', HiddenType::class)
                ->add('submit', SubmitType::class)
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $comment = $form->getData();

                $em = $this->getDoctrine()->getManager();

                $commentEdit->setComment($comment->getComment());

                $em->flush();

                return $this->redirectToRoute('app_index');
            }

            return $this->render('editComment.html.twig', array(
                'comment' => $form->createView()
            ));
        } else {
            return $this->redirectToRoute('app_index');
        }
    }

    /**
     * @Route("/{_locale}/delete/comment/{id}", name="app_comment_delete", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function commentDeleteAction(Request $request, Comment $comment, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        if ($comment->getIdUser() == $user->getId()) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_index');
    }
}
