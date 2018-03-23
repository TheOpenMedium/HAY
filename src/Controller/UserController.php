<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Status;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/{_locale}/show/user/{id}", name="user_show", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function userShowAction(User $user, $id)
    {
        // Fetching the status of the requested user.
        $statusList = $this->getDoctrine()->getRepository(Status::class)->findStatusByUser(10, $id);
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

        // All that is rendered with the user show template sending Status List, Comment List and User.
        return $this->render('user/showUser.html.twig', array(
            'commentList' => $commentList,
            'statusList' => $statusList,
            'user' => $user
        ));
    }
}
