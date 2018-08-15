<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Laws;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ReportController extends Controller
{
    /**
     * @Route("/{_locale}/report/{type}/{id}", name="report", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function reportAction(Request $request, string $type, int $id)
    {
        if ($type == 'user') {
            $entity = $this->getDoctrine()->getRepository(User::class)->find($id);
        } else if ($type == 'post') {
            $entity = $this->getDoctrine()->getRepository(Post::class)->find($id);
        } else if ($type == 'comment') {
            $entity = $this->getDoctrine()->getRepository(Comment::class)->find($id);
        }

        $laws = $this->getDoctrine()->getRepository(Laws::class)->findAll();

        $report = new Report;
        $report->setReporter($this->getUser());
        $report->setLaw(0);
        if ($type == 'user') {
            $report->setReportedUser($entity);
        } else if ($type == 'post') {
            $report->setReportedUser($entity->getUser());
            $report->setReportedPost($entity);
        } else if ($type == 'comment') {
            $report->setReportedUser($entity->getUser());
            $report->setReportedComment($entity);
        }

        $reportForm = $this->createFormBuilder($report)
            ->add('law', HiddenType::class)
            ->add('emergency_level', IntegerType::class)
            ->add('reporter_msg', TextareaType::class, array('required' => false))
            ->add('submit', SubmitType::class)
            ->getForm();

        $reportForm->handleRequest($request);

        // If he send a Report, the Report is saved into database.
        if ($reportForm->isSubmitted() && $reportForm->isValid()) {
            $report = $reportForm->getData();

            // TODO: Sending notifications to reporter and reported

            $em = $this->getDoctrine()->getManager();
            $em->persist($report);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('report/index.html.twig', array(
            'report' => $reportForm->createView(),
            'entity' => $type,
            'id' => $id,
            'laws' => $laws
        ));
    }
}
