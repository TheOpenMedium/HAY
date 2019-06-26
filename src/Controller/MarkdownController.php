<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MarkdownController extends AbstractController
{
    /**
     * Render markdown
     *
     * @Route("/{_locale}/render/markdown", name="markdown_render", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function markdownRenderingAction()
    {
        $parsedown = new \ParsedownHAYFlavored();
        $parsedown->setSafeMode(true)
                  ->setBreaksEnabled(true)
                  ->setMarkupEscaped(true)
                  ->setUrlsLinked(true);

        return new Response($parsedown->text($_POST["markdown"]));
    }
}
