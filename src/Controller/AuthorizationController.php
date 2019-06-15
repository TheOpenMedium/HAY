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
        $yaml = $this->getParameter("authorizations");
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
        $file_content = Yaml::parseFile(__dir__.'/../../config/config.yaml');
        $yaml = $file_content["parameters"]["authorizations"];
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
        $file_content["parameters"]["authorizations"] = $yaml;
        $file_content = Yaml::dump($file_content, 99, 4);
        file_put_contents(__dir__.'/../../config/config.yaml', $file_content);
        return $this->redirectToRoute('administration');
    }
}
