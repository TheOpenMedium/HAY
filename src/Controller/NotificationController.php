<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends Controller
{
    /**
     * @Route("/{_locale}/notification/{id_user}", name="notification", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function notificationAction(Request $request, $id_user)
    {
        $notifications = $this->getDoctrine()->getRepository(Notification::class)->findNotification($id_user);

        return $this->render('notification.html.twig', array(
            'notifications' => $notifications
        ));
    }

    /**
     * @Route("/{_locale}/delete/notification/{id}", name="notification_delete", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function notificationDeleteAction(Request $request, Notification $notification, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        if ($notification->getIdUser() == $user->getId()) {
            $em->remove($notification);
            $em->flush();
        }

        return $this->redirectToRoute('app_index');
    }
}
