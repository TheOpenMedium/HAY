<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AuthorizationController extends Controller
{
    /**
     * @Route("/{_locale}/admin/manage_authorization", name="authorization_manage", requirements={
     *     "_locale": "%app.locales%"
     * })
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
                        $forms[$role][$category] = $forms[$role][$category]->add($key, $options["input"], $input_var + ["required" => False]);
                    }
                    $forms[$role][$category] = $forms[$role][$category]->add('send', SubmitType::class)->getForm();
                }
            }
        }
        foreach ($forms as $role => $role_value) {
            foreach ($role_value as $category => $cat_value) {/*
                if ($cat_value->isSubmitted() && $cat_value->isValid()) {
                    $yaml[$role][$category] = $cat_value->getData();
                }*/
                $forms[$role][$category] = $cat_value->createView();
            }
        }

        /*
        $yaml = Yaml::dump($yaml, 99, 4);
        file_put_contents(__dir__.'/../../config/authorizations.yaml', $yaml);
        */
        return $this->render('authorization/manage.html.twig', [
            'forms' => $forms,
        ]);
    }

    public function manageSubmitAction(Request $request)
    {
        //
    }
}
