<?php

namespace App\Controller;

use App\Entity\Laws;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LawsController extends Controller
{
    /**
     * Create new laws
     *
     * @Route("/{_locale}/admin/new/laws", name="laws_new", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function newLawsAction(Request $request)
    {
        $laws = new Laws;

        $form = $this->createFormBuilder($laws)
        ->add('title', TextType::class)
        ->add('content', TextareaType::class)
        ->add('submit', SubmitType::class)
        ->getForm();

        $form->handleRequest($request);

        // If he send Laws, the Laws are saved into database.
        if ($form->isSubmitted() && $form->isValid()) {
            $laws = $form->getData();
            $laws->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($laws);
            $em->flush();

            return $this->redirectToRoute('app_index');
        }

        return $this->render('laws/newLaws.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
