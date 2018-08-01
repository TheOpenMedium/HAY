<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
     * @Route("/{_locale}/statistics/graph/{type}/{scope}/{n}", name="statistics_graph", requirements={
     *     "_locale": "%app.locales%"
     * })
     */
    public function graphAction(string $type, string $scope, int $n = 0)
    {
        \header('Content-Type: image/png');

        $max = \strtotime('-'.$n.' '.$scope);

        if ($scope == "year") {
            $min = \strtotime('-10 '.$scope, $max);
        } else if ($scope == "month") {
            $min = \strtotime('-12 '.$scope, $max);
        } else if ($scope == "day") {
            $min = \strtotime('-7 '.$scope, $max);
        }

        $statsNonFormatted = $this->container->get('doctrine')->getRepository(Statistics::class)->findByDateInterval(\date('Y-m-d', $max), \date('Y-m-d', $min));

        $stats = array();
        foreach ($statsNonFormatted as $stat) {
            if ($type == "visits") {
                $stats[] = $stat->getVisits();
            } else if ($type == "requests") {
                $stats[] = $stat->getRequests();
            } else if ($type == "new_users") {
                $stats[] = $stat->getNewUsers();
            } else if ($type == "new_posts") {
                $stats[] = $stat->getNewPosts();
            } else if ($type == "new_comments") {
                $stats[] = $stat->getNewComments();
            } else {
                throw new \Exception("Unknown graph type");
            }
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
        }

        $stats = \array_values($stats);

        // NOTE: Thanks to Andry Aimé for his great tutorial on how to create graphs with the GD library and PHP
        // SEE: https://andry.developpez.com/tutoriels/php/creation-graphes-statistiques-et-geometriques/?page=page_1 (french)

        // Getting minimal and maximal values
        $minValue = 999999999999999999999999;
        $maxValue = 0;

        foreach ($stats as $stat) {
            if ($stat < $minValue) {
                $minValue = $stat;
            }
            if ($stat > $maxValue) {
                $maxValue = $stat;
            }
        }

        // In case we have only one result
        if ($maxValue == $minValue) {
            $maxValue *= 2;
            $minValue = 0;
        }

        // Setting up parameters
        $width = \count($stats) * 50 + 90;
        $height = 400;
        $fontfile = __dir__ . '/../../public/fonts/amble/Amble-Regular.ttf';
        $xAxisHeight = 80;
        $yAxisLevels = 10;

        $image = \imagecreatetruecolor($width, $height);

        // Creating colors
        $background = \imagecolorallocate($image, 64, 64, 64); # Main Background color
        $lines = \imagecolorallocate($image, 85, 255, 153); # HAY color
        $linesLight = \imagecolorallocate($image, 164, 164, 164); # HAY ligth
        $text = \imagecolorallocate($image, 255, 255, 255); # White

        // Filling Background
        \imagefilledrectangle($image, 0, 0, $width, $height, $background);

        // If the minValue isn't 0, we raise the line lower position from 10
        $a = 0;
        $xAxisHeight2 = $xAxisHeight;
        if($minValue != 0)
        {
            $xAxisHeight += 10;
            $a = 10;
        }

        // Calculating axis
        $xAxis = ($width - 100) / \count($stats);
        $yAxis = ($height - $xAxisHeight - 20) / $yAxisLevels;

        $i = $minValue;
        $py = ($maxValue - $minValue) / $yAxisLevels;
        $stepY = $xAxisHeight;

        // Drawing grids and numbers
        while ($stepY < ($height - 19)) {
            \imagestring($image, 4, 10, $height - $stepY - 6, \round($i), $text);
            \imageline($image, 50, $height - $stepY, $width - 20, $height - $stepY, $linesLight);

            $stepY += $yAxis;
            $i += $py;
        }

        $j = -1;
        $stepX = 90;

        foreach ($stats as $statKeys => $stat) {
            $y = $height - (($stat - $minValue) * ($yAxis / $py)) - $xAxisHeight;

            // Drawing the text down
            \imagettftext($image, 10, 315, $stepX, $height - $xAxisHeight + 20, $text, $fontfile, $key[$statKeys]);
            // Drawing a line from the point to the bottom
            \imageline($image, $stepX, $height - $xAxisHeight + $a, $stepX, $y, $linesLight);
            // Drawing a point
            \imagefilledellipse($image, $stepX, $y, 6, 6, $lines);
            if ($j != -1) {
                // Drawing a line between two points
                \imageline($image, $stepX - $xAxis, $previous, $stepX, $y, $lines);
            }

            // Drawing the number next to the point
            \imagestring($image, 2, $stepX - 15, $y - 14, $stat, $text);

            $j = $stat;
            $previous = $y;
            $stepX += $xAxis;
        }

        // Drawing axis (x-axis then y-axis), we write them here, so we avoid them to be hidden by other lines.
        \imageline($image, 50, $height - $xAxisHeight2, $width - 10, $height - $xAxisHeight2, $lines);
        \imageline($image, 50, $height - $xAxisHeight2, 50, 10, $lines);

        // Creating image
        \imagepng($image);
        \imagedestroy($image);

        return new Response('', 200, array('Content-Type' => 'image/png'));
    }
}
