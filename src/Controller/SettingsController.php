<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * A controller related to the User's settings
 *
 * List of actions:
 * * settingsEditAction(string $setting, string $value) -- settings_edit
 */
class SettingsController extends AbstractController
{
    /**
     * @Route("/{_locale}/edit/settings/{setting}/{value}", name="settings_edit", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function settingsEditAction(string $setting, string $value)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $user = $this->getUser();
        $settings = $user->getSettings();

        if (!$settings) {
            $user->setSettings(array());
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $user = $this->getUser();
            $settings = $user->getSettings();
        }

        $settings[$setting] = $value;
        $user->setSettings($settings);
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $user = $this->getUser();
        $settings = $user->getSettings();

        if ($settings[$setting] == $value) {
            return new Response("true");
        } else {
            return new Response("false");
        }
    }
}
