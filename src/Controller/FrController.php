<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class FrController extends Controller
{
    /**
     * @Route("/fr/")
     */
    public function indexAction()
    {
        return $this->render('base.html.twig');
    }
}