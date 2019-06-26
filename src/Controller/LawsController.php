<?php

namespace App\Controller;

use App\Entity\Laws;
use App\Form\LawsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class LawsController extends AbstractController
{
    /**
     * Show all laws
     *
     * @Route("/{_locale}/show/laws", name="laws", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function showLawsAction()
    {
        $parsedown = new \Parsedown();
        $parsedown->setSafeMode(true);

        $lawsList = $this->getDoctrine()->getRepository(Laws::class)->findAll();

        foreach ($lawsList as $laws) {
            $laws->setContent($parsedown->text($laws->getContent()));
        }

        return $this->render('laws/showLaws.html.twig', array(
            'lawsList' => $lawsList
        ));
    }

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

        $form = $this->createForm(LawsType::class, $laws);

        $form->handleRequest($request);

        // If he send Laws, the Laws are saved into database.
        if ($form->isSubmitted() && $form->isValid()) {
            $laws = $form->getData();
            $laws->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($laws);
            $em->flush();

            return $this->redirectToRoute('laws');
        }

        return $this->render('laws/newLaws.html.twig', array(
            'laws' => $form->createView()
        ));
    }

    /**
     * Edit laws
     *
     * @Route("/{_locale}/admin/edit/laws/{laws}", name="laws_edit", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function editLawsAction(Request $request, Laws $laws)
    {
        $form = $this->createForm(LawsType::class, $laws);

        $form->handleRequest($request);

        // If he send Laws, the Laws are saved into database.
        if ($form->isSubmitted() && $form->isValid()) {
            $laws = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('laws');
        }

        return $this->render('laws/newLaws.html.twig', array(
            'laws' => $form->createView()
        ));
    }

    /**
     * Delete laws
     *
     * @Route("/{_locale}/admin/delete/laws/{laws}", name="laws_delete", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function deleteLawsAction(Laws $laws)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($laws);
        $em->flush();

        return $this->redirectToRoute('laws');
    }
}
