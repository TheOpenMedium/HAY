<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Statistics;

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
     * WARNING: HERE BE THE DRAGONS... 🐉🐲
     *
     * @Route("/{_locale}/statistics/graph/{scope}/{n}", name="statistics_graph", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function graphAction(Request $request, string $scope, int $n = 0)
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
            "visits" => [],
            "requests" => [],
            "new_users" => [],
            "new_posts" => [],
            "new_comments" => []
        ];

        $colors = [
            "visits" => "ff0000",
            "requests" => "00ff00",
            "new_users" => "0000ff",
            "new_posts" => "ffff00",
            "new_comments" => "ff00ff"
        ];

        if ($scope == "year") {
            // code...
        } elseif ($scope == "month") {
            // code...
        } elseif ($scope == "day") {
            for ($i=0; $i < $range; $i++) {
                $labels[$i] = \date_format(\date_create('@'.\strtotime('-'.$i.' '.$scope, $max)), 'l Y-m-d');
            }
            $labels = array_reverse($labels);
            foreach ($stats as $key => $value) {
                foreach ($labels as $label) {
                    $stats[$key][$label] = 0;
                }
            }
            foreach ($statsNonFormatted as $key => $stat) {
                $date = \date('l Y-m-d', $stat->getDate()->getTimestamp());
                $stats["visits"][$date] = $stat->getVisits();
                $stats["requests"][$date] = $stat->getRequests();
                $stats["new_users"][$date] = $stat->getNewUsers();
                $stats["new_posts"][$date] = $stat->getNewPosts();
                $stats["new_comments"][$date] = $stat->getNewComments();
            }
        }
        foreach ($stats as $key => $stat) {
            $stats[$key] = array_values($stat);
        }

        /*$stats = array();
        foreach ($statsNonFormatted as $stat) {
            $stats["visits"][] = $stat->getVisits();
            $stats["requests"][] = $stat->getRequests();
            $stats["new_users"][] = $stat->getNewUsers();
            $stats["new_posts"][] = $stat->getNewPosts();
            $stats["new_comments"][] = $stat->getNewComments();
        }

        $last = [-1, ""];
        if ($scope == "year") {
            for ($i=0; $i < \count($stats); $i++) {
                $key[$i] = \date_format($statsNonFormatted[$i]->getDate(), 'Y');
            }
            foreach ($statsNonFormatted as $k => $v) {
                $l = \date_format($v->getDate(), 'Y');
                if ($last[0] == -1) {
                    $last[0] = $k;
                    $last[1] = $l;
                } else {
                    if ($last[1] == $l) {
                        $stats[$last[0]] += $stats[$k];
                        unset($stats[$k]);
                    } else {
                        $last[0] = $k;
                        $last[1] = $l;
                    }
                }
            }
        } else if ($scope == "month") {
            for ($i=0; $i < \count($stats); $i++) {
                $key[$i] = \date_format($statsNonFormatted[$i]->getDate(), 'F');
            }
            foreach ($statsNonFormatted as $k => $v) {
                $l = \date_format($v->getDate(), 'm');
                if ($last[0] == -1) {
                    $last[0] = $k;
                    $last[1] = $l;
                } else {
                    if ($last[1] == $l) {
                        $stats[$last[0]] += $stats[$k];
                        unset($stats[$k]);
                    } else {
                        $last[0] = $k;
                        $last[1] = $l;
                    }
                }
            }
        } else if ($scope == "day") {
            for ($i=0; $i < \count($stats); $i++) {
                $key[$i] = \date_format($statsNonFormatted[$i]->getDate(), 'l');
            }
        }*/

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
