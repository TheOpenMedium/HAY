<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Status;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/{_locale}/show/user/{id}", name="user_show", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function userShowAction(Request $request, User $user, $id)
    {
        $statusList = $this->getDoctrine()->getRepository(Status::class)->findStatusByUser(10, $id);
        $commentList = array();

        if ($statusList) {
            foreach ($statusList as $status) {
                $content = $status[0]->getContent();
                $status[0]->setContent(preg_replace('#\n#', '<br />', $content));
                $commentList[] = $this->getDoctrine()->getRepository(Comment::class)->findComments(10, $status[0]->getId());
            }

            foreach ($commentList as $commentStatus) {
                if ($commentStatus) {
                    foreach ($commentStatus as $comment) {
                        $c = $comment[0]->getComment();
                        $comment[0]->setComment(preg_replace('#\n#', '<br />', $c));
                    }
                }
            }
        }

        return $this->render('user/showUser.html.twig', array(
            'commentList' => $commentList,
            'statusList' => $statusList,
            'user' => $user
        ));
    }
}
