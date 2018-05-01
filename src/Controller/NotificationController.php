<?php

namespace App\Controller;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * A controller related to the Notification entity
 *
 * List of actions:
 * * notificationAction()                                 -- notification
 * * notificationDeleteAction(Notification $notification) -- notification_delete
 */
class NotificationController extends Controller
{
    /**
     * Render the notifications of an user
     *
     * @Route("/{_locale}/notification", name="notification", requirements={
     *     "_locale": "en|fr"
     * })
     */
    public function notificationAction()
    {
        // Getting Notifications of current user.
        $notifications = $this->getUser()->getNotifications();

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
        if ($notification->getUser()->getId() == $user->getId()) {
            $em->remove($notification);
            $em->flush();
        }

        // Finally, redirecting to home page.
        return $this->redirectToRoute('app_index');
    }
}
