<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    /**
     * @Route("/{_locale}/report", name="report", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function indexAction()
    {
        return $this->render('report/index.html.twig');
    }
}
