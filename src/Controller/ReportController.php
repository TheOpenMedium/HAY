<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Laws;
use App\Form\ReportType;
use App\Form\ProcessReportType;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

        $reportForm = $this->createForm(ReportType::class, $report);

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

    /**
     * @Route("/{_locale}/mod/list/report/{filter}/{limit}", name="report_list", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function reportListAction(string $filter = 'emergency', int $limit = 10)
    {
        if ($filter == 'emergency') {
            $reports = $this->getDoctrine()->getRepository(Report::class)->findBy(
                ['validated' => NULL],
                ['emergency_level' => 'DESC'],
                $limit
            );
        } elseif ($filter == 'recent') {
            $reports = $this->getDoctrine()->getRepository(Report::class)->findBy(
                ['validated' => NULL],
                ['date' => 'DESC'],
                $limit
            );
        } elseif ($filter == 'oldest') {
            $reports = $this->getDoctrine()->getRepository(Report::class)->findBy(
                ['validated' => NULL],
                ['date' => 'ASC'],
                $limit
            );
        } elseif ($filter == 'contested') {
            $reports = $this->getDoctrine()->getRepository(Report::class)->findBy(
                ['contested' => true],
                ['date' => 'ASC'],
                $limit
            );
        } elseif ($filter == 'needhelp') {
            $reports = $this->getDoctrine()->getRepository(Report::class)->findBy(
                ['needhelp' => true],
                ['date' => 'ASC'],
                $limit
            );
        } elseif ($filter == 'validated') {
            $reports = $this->getDoctrine()->getRepository(Report::class)->findBy(
                ['validated' => true],
                ['date' => 'DESC'],
                $limit
            );
        } elseif ($filter == 'closed') {
            $criteria = new Criteria;

            $criteria->where($criteria->expr()->neq('validated', NULL));
            $criteria->orderBy(['date' => 'DESC']);
            $criteria->setMaxResults($limit);

            $reports = $this->getDoctrine()->getRepository(Report::class)->matching($criteria);
        } elseif ($filter == 'all') {
            $reports = $this->getDoctrine()->getRepository(Report::class)->findBy(
                [],
                ['date' => 'DESC'],
                $limit
            );
        } else {
            throw new \Exception("This filter doesn't exist");
        }

        return $this->render('report/reportList.html.twig', array(
            'reports' => $reports
        ));
    }

    /**
     * @Route("/{_locale}/mod/advlist/report", name="report_adv_list", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function advReportListAction()
    {
        // code...
    }

    /**
     * @Route("/{_locale}/mod/report/process/{report}", name="report_process", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function processReportAction(Request $request, Report $report)
    {
        $reportForm = $this->createForm(ProcessReportType::class, $report);

        $reportForm->handleRequest($request);

        // If he send a Report, the Report is saved into database.
        if ($reportForm->isSubmitted() && $reportForm->isValid()) {
            $report = $reportForm->getData();
            $report->addModerator($this->getUser());
            if ($report->getValidated() === NULL) {
                $report->setNeedhelp(true);
            } else {
                $report->setNeedhelp(NULL);
            }

            // TODO: Sending notifications to reporter and reported

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('administration_admin');
        }

        return $this->render('report/reportForm.html.twig', array(
            'report' => $reportForm->createView(),
            'id' => $report->getId()
        ));
    }

    /**
     * @Route("/{_locale}/mod/report/render/entity/{type}/{id}", name="report_render_entity", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function renderEntityReportAction($type, $id)
    {
        if ($type == 'post') {
            $entity = $this->getDoctrine()->getRepository(Post::class)->find($id);

            return $this->render('post/postDisplay.html.twig', array('postList' => [$entity]));
        } else if ($type == 'comment') {
            $entity = $this->getDoctrine()->getRepository(Comment::class)->find($id);

            return $this->render('comment/commentDisplay.html.twig', array('comment' => $entity));
        }
    }
}
