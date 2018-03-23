<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * A controller related to the Notification entity
 *
 * List of actions:
 * * notificationAction($id_user)                         -- notification
 * * notificationDeleteAction(Notification $notification) -- notification_delete
 */
class NotificationController extends Controller
{
    /**
     * Render the notifications of a user
     *
     * @param int $id_user The user id
     *
     * @Route("/{_locale}/notification/{id_user}", name="notification", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function notificationAction($id_user)
    {
        // Fetching Notifications from database.
        $notifications = $this->getDoctrine()->getRepository(Notification::class)->findNotification($id_user);

        // All that is rendered with the notification template sending Notifications List.
        return $this->render('notification/notification.html.twig', array(
            'notifications' => $notifications
        ));
    }

    /**
     * Delete a notification
     *
     * @param Notification $notification The notification to delete
     *
     * @Route("/{_locale}/delete/notification/{id}", name="notification_delete", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function notificationDeleteAction(Notification $notification)
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
