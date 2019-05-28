<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Form\Extension\Core\Type;

class AuthorizationController extends Controller
{
    /**
     * @Route("/authorization")
     */
    public function manageAction(Request $request)
    {
        $yaml = Yaml::parseFile(__dir__.'/../../config/authorizations.yaml');
        $forms = [];
        foreach ($yaml as $role => $authorizations) {
            if ($role != "__META__") {
                foreach ($authorizations as $category => $values) {
                    $forms[$role][$category] = $this->createFormBuilder($values);
                    foreach ($yaml["__META__"][$category] as $key => $options) {
                        if (!\array_key_exists($key, $values)) {
                            $values[$key] = $options["default"];
                        }
                        $input_var = key_exists("input_var", $options) ? $options["input_var"] : [];
                        $forms[$role][$category] = $forms[$role][$category]->add($key, $options["input"], $input_var);
                    }
                    $forms[$role][$category] = $forms[$role][$category]->getForm();
                }
            }
        }

        return $this->render('authorization/index.html.twig', [
            'controller_name' => 'AuthorizationController',
        ]);
    }
}
