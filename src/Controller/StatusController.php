<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Status;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class StatusController extends Controller
{
    /**
     * @Route("/{_locale}/show/status/{id}", name="status_show", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function statusShowAction($id)
    {
        // Retrieving statusList from the database.
        $statusList = $this->getDoctrine()->getRepository(Status::class)->findStatusById($id);
        $commentList = array();

        // If there is one Status or more :
        if ($statusList) {
            // We replace new lines by the <br /> tag and we fetch from the database the 10 newer comments of each status.
            foreach ($statusList as $status) {
                $content = $status[0]->getContent();
                $status[0]->setContent(preg_replace('#\n#', '<br />', $content));
                $commentList[] = $this->getDoctrine()->getRepository(Comment::class)->findComments(10, $status[0]->getId());
            }

            // Then, we replace new lines by the <br /> tag.
            foreach ($commentList as $commentStatus) {
                if ($commentStatus) {
                    foreach ($commentStatus as $comment) {
                        $c = $comment[0]->getComment();
                        $comment[0]->setComment(preg_replace('#\n#', '<br />', $c));
                    }
                }
            }
        }

        // All that is rendered with the status show template sending Status List and Comment List.
        return $this->render('status/showStatus.html.twig', array(
            'statusList' => $statusList,
            'commentList' => $commentList
        ));
    }

    /**
     * @Route("/{_locale}/edit/status/{id}", name="status_edit", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function statusEditAction(Request $request, Status $statusEdit)
    {
        $user = $this->getUser();

        // Checking that the author and the user are the same.
        if ($statusEdit->getIdUser() == $user->getId()) {
            $status = new Status();

            // Adding last values as default
            $status->setContent($statusEdit->getContent());
            $status->setColor($statusEdit->getColor());
            $status->setSize($statusEdit->getSize());
            $status->setFont($statusEdit->getFont());

            // Creating the form
            $form = $this->createFormBuilder($status)
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

            // If a status was edited, we retrieve data.
            if ($form->isSubmitted() && $form->isValid()) {
                $status = $form->getData();
                $status->setFont('SS');

                $em = $this->getDoctrine()->getManager();

                // We replace old datas.
                $statusEdit->setContent($status->getContent());
                $statusEdit->setColor($status->getColor());
                $statusEdit->setSize($status->getSize());
                $statusEdit->setFont($status->getFont());

                // And finaly, we save changes.
                $em->flush();

                // And we redirect user to home page.
                return $this->redirectToRoute('app_index');
            }

            $color = $statusEdit->getColor();
            $size = $statusEdit->getSize();

            // All that is rendered with the status edit template sending a From and the default Color and Size.
            return $this->render('status/editStatus.html.twig', array(
                'form' => $form->createView(),
                'color' => $color,
                'size' => $size
            ));
        } else {
            // If the user can't modify the status, he's redirected to home page.
            return $this->redirectToRoute('app_index');
        }
    }

    /**
     * @Route("/{_locale}/delete/status/{id}", name="status_delete", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function statusDeleteAction(Status $status, $id)
    {
        // Fetching the status and it's comments.
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $repository->findBy(['id_status' => $id]);

        $user = $this->getUser();

        // And delete it if the user and the author are the same.
        if ($status->getIdUser() == $user->getId()) {
            $entityManager->remove($status);

            // Then delete every comment related to the status.
            foreach ($comments as $comment) {
                $entityManager->remove($comment);
            }

            $entityManager->flush();
        }

        // Finally the user is redirected to home page.
        return $this->redirectToRoute('app_index');
    }
}
