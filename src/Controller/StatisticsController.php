<?php

namespace App\Controller;

use App\Entity\Statistics;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class StatisticsController extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @Route("/{_locale}/statistics/record/visits", name="statistics_record_visits", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function recordVisitsAction()
    {
        $em = $this->container->get('doctrine')->getEntityManager();

        $stats = $this->container->get('doctrine')->getRepository(Statistics::class)->findByDate(\date('Y-m-d'));

        if (empty($stats[0])) {
            $stats[0] = new Statistics;
            $em->persist($stats[0]);
        }

        if (empty($_SESSION)) { session_start(); }
        if (empty($_SESSION['visited'])) {
            $_SESSION['visited'] = true;

            $visits = $stats[0]->getVisits();
            $visits++;
            $stats[0]->setVisits($visits);
        }

        $requests = $stats[0]->getRequests();
        $requests++;
        $stats[0]->setRequests($requests);

        $em->flush();

        return new Response('true');
    }

    /**
     * @param string $type The graph values type / available values : "visits", "requests", "new_users", "new_posts", "new_comments"
     * @param string $scope The graph values scope / available values : "year" (10 years), "month" (12 months), "day" (7 days)
     * @param int $n Used for setting the number of scope before the actual day
     * @example if $n = 5 and $scope = "month" then the fetched datas are dated from actual month (like 7, july) - 5 (2, february)
     *
     * @Route("/{_locale}/statistics/graph/{scope}/{n}", name="statistics_graph", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function graphAction(Request $request, TranslatorInterface $translator, string $scope, int $n = 0)
    {
        $max = \strtotime('-'.$n.' '.$scope);

        if ($scope == "year") {
            $min = \strtotime('-10 '.$scope, $max);
            $range = 10;
        } else if ($scope == "month") {
            $min = \strtotime('-12 '.$scope, $max);
            $range = 12;
        } else if ($scope == "day") {
            $min = \strtotime('-7 '.$scope, $max);
            $range = 7;
        }

        $statsNonFormatted = $this->container->get('doctrine')->getRepository(Statistics::class)->findByDateInterval(\date('Y-m-d', $max), \date('Y-m-d', $min));

        $stats = [
            $translator->trans("visits") => [],
            $translator->trans("requests") => [],
            $translator->trans("new_users") => [],
            $translator->trans("new_posts") => [],
            $translator->trans("new_comments") => []
        ];

        $colors = [
            $translator->trans("visits") => "ff0000",
            $translator->trans("requests") => "00ff00",
            $translator->trans("new_users") => "0000ff",
            $translator->trans("new_posts") => "ffff00",
            $translator->trans("new_comments") => "ff00ff"
        ];

        if ($scope == "year") {
            $dateformat = 'Y';
        } elseif ($scope == "month") {
            $dateformat = 'F Y';
        } elseif ($scope == "day") {
            $dateformat = 'l Y-m-d';
        }
        for ($i=0; $i < $range; $i++) {
            $labels[$i] = \date_format(\date_create('@'.\strtotime('-'.$i.' '.$scope, $max)), $dateformat);
        }
        $labels = array_reverse($labels);
        foreach ($stats as $key => $value) {
            foreach ($labels as $label) {
                $stats[$key][$label] = 0;
            }
        }
        foreach ($statsNonFormatted as $key => $stat) {
            $date = \date($dateformat, $stat->getDate()->getTimestamp());
            $stats[$translator->trans("visits")][$date] += $stat->getVisits();
            $stats[$translator->trans("requests")][$date] += $stat->getRequests();
            $stats[$translator->trans("new_users")][$date] += $stat->getNewUsers();
            $stats[$translator->trans("new_posts")][$date] += $stat->getNewPosts();
            $stats[$translator->trans("new_comments")][$date] += $stat->getNewComments();
        }

        foreach ($stats as $key => $stat) {
            $stats[$key] = array_values($stat);
        }

        if ($request->get('updating') != true) {
            return $this->render('statistics/index.html.twig', [
                'stats' => $stats,
                'labels' => $labels,
                'colors' => $colors
            ]);
        } else {
            return new Response(json_encode(["stats" => $stats, "labels" => $labels, "colors" => $colors]), 200, array('Content-Type' => 'application/json'));
        }
    }
}
