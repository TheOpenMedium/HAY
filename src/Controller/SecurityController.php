<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{
    /**
     * @Route("/{_locale}/root", name="security_root", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function rootAction()
    {
        return $this->render('security/root.html.twig');
    }

    /**
     * @Route("/{_locale}/admin", name="security_admin", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function adminAction()
    {
        return $this->render('security/admin.html.twig');
    }

    /**
     * @Route("/{_locale}/mod", name="security_mod", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function modAction()
    {
        return $this->render('security/mod.html.twig');
    }

    /**
     * @Route("/{_locale}/trans", name="security_trans", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function transAction()
    {
        return $this->render('security/trans.html.twig');
    }

    /**
     * @Route("/{_locale}/design", name="security_design", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function designAction()
    {
        return $this->render('security/design.html.twig');
    }

    /**
     * @Route("/{_locale}/dev", name="security_dev", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function devAction()
    {
        return $this->render('security/dev.html.twig');
    }

    /**
     * @Route("/{_locale}/helper", name="security_helper", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function helperAction()
    {
        return $this->render('security/helper.html.twig');
    }
}
