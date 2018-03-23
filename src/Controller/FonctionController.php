<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FonctionController extends Controller
{
    /**
     * @Route("/dev/styles", name="app_fonction_style")
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
