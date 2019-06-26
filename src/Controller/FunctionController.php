<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * A controller that have functions used by other actions
 *
 * List of functions:
 * * styleAction() -- fonction_style
 */
class FunctionController extends AbstractController
{
    /**
     * Function to retrieve the stylesheets
     *
     * @Route("/dev/styles", name="fonction_style")
     */
    public function styleAction()
    {
        // An experimental function to retrieve stylesheets.
        $return = "";
        if($handle = opendir('styles')) {
            while (false != ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $return .= "        <link rel=\"stylesheet\" href=\"/styles/".$entry."\" type=\"text/css\"/>\n";
                }
            }
            closedir($handle);
        }
        return new Response($return);
    }
}
