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
        // Fetching Notifications from database.
        $notifications = $this->getDoctrine()->getRepository(Notification::class)->findNotification($id_user);

        // All that is rendered with the notification template sending Notifications List.
        return $this->render('notification/notification.html.twig', array(
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

        // Checking that the user's notification and the current user are the same.
        if ($notification->getIdUser() == $user->getId()) {
            $em->remove($notification);
            $em->flush();
        }

        // Finally, redirecting to home page.
        return $this->redirectToRoute('app_index');
    }
}
