<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
    public function authorizationManageAction(Request $request)
    {
        $yaml = Yaml::parseFile(__dir__.'/../../config/authorizations.yaml');
        $forms = [];
        foreach ($yaml as $role => $authorizations) {
            if ($role != "__META__") {
                foreach ($authorizations as $category => $values) {
                    foreach ($yaml["__META__"][$category] as $key => $options) {
                        if (!\array_key_exists($key, $values)) {
                            $values[$key] = $options["default"];
                        }
                    }
                    $forms[$role][$category] = $this->container->get('form.factory')->createNamedBuilder($role.'_'.$category, FormType::class, $values, ['action' => $this->generateUrl('authorization_manage_submit', ['role' => $role, 'category' => $category])]);
                    foreach ($yaml["__META__"][$category] as $key => $options) {
                        $input_var = key_exists("input_var", $options) ? $options["input_var"] : [];
                        $forms[$role][$category] = $forms[$role][$category]->add($key, $options["input"], $input_var + ["required" => False]);
                    }
                    $forms[$role][$category] = $forms[$role][$category]->add('send', SubmitType::class)->getForm()->createView();
                }
            }
        }

        return $this->render('authorization/manage.html.twig', [
            'forms' => $forms,
        ]);
    }

    /**
     * @Route("/{_locale}/admin/manage_authorization/submit/{role}/{category}", name="authorization_manage_submit", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function authorizationManageSubmitAction(Request $request, string $role, string $category)
    {
        $yaml = Yaml::parseFile(__dir__.'/../../config/authorizations.yaml');
        $form = $this->container->get('form.factory')->createNamedBuilder($role.'_'.$category, FormType::class, $yaml[$role][$category], ['action' => $this->generateUrl('authorization_manage_submit', ['role' => $role, 'category' => $category])]);
        foreach ($yaml["__META__"][$category] as $key => $options) {
            $input_var = key_exists("input_var", $options) ? $options["input_var"] : [];
            $form = $form->add($key, $options["input"], $input_var + ["required" => False]);
        }
        $form = $form->add('send', SubmitType::class)->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $yaml[$role][$category] = $form->getData();
        }
        $yaml = Yaml::dump($yaml, 99, 4);
        file_put_contents(__dir__.'/../../config/authorizations.yaml', $yaml);
        return $this->redirectToRoute('security_admin');
    }

    public function isAuthorized($authorization, $value = True)
    {
        $yaml = Yaml::parseFile(__dir__.'/../../config/authorizations.yaml');
        $role_hierarchy = Yaml::parseFile(__dir__.'/../../config/packages/security.yaml')['security']['role_hierarchy'];
        $roles = $this->getUser()->getRoles();
        foreach ($roles as $role) {
            if (isset($yaml[$role][$authorization])) {
                // var == var
                if ($yaml[$role][$authorization] == $value) {
                    return True;
                }
                // [var, foo] == var
                if (gettype($yaml[$role][$authorization]) == 'array' && gettype($value) == 'string') {
                    if (in_array($value, $yaml[$role][$authorization])) {
                        return True;
                    }
                }
                // var == [var, bar]
                if (gettype($yaml[$role][$authorization]) == 'string' && gettype($value) == 'array') {
                    if (in_array($yaml[$role][$authorization], $value)) {
                        return True;
                    }
                }
                // [var, foo] == [var, bar]
                if (gettype($yaml[$role][$authorization]) == 'array' && gettype($value) == 'array') {
                    if (!empty(array_intersect($yaml[$role][$authorization], $value))) {
                        return True;
                    }
                }
            }
        }
        foreach ($roles as $temp_role) {
            while (isset($role_hierarchy[$temp_role])) {
                $temp_role = $role_hierarchy[$temp_role];
                $temp_role = (gettype($temp_role) != 'array') ? array($temp_role) : $temp_role;
                foreach ($temp_role as $role) {
                    if (isset($yaml[$role][$authorization])) {
                        // var == var
                        if ($yaml[$role][$authorization] == $value) {
                            return True;
                        }
                        // [var, foo] == var
                        if (gettype($yaml[$role][$authorization]) == 'array' && gettype($value) == 'string') {
                            if (in_array($value, $yaml[$role][$authorization])) {
                                return True;
                            }
                        }
                        // var == [var, bar]
                        if (gettype($yaml[$role][$authorization]) == 'string' && gettype($value) == 'array') {
                            if (in_array($yaml[$role][$authorization], $value)) {
                                return True;
                            }
                        }
                        // [var, foo] == [var, bar]
                        if (gettype($yaml[$role][$authorization]) == 'array' && gettype($value) == 'array') {
                            if (!empty(array_intersect($yaml[$role][$authorization], $value))) {
                                return True;
                            }
                        }
                    }
                }
            }
        }
        return False;
    }
}
