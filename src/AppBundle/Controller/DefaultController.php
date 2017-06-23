<?php

namespace AppBundle\Controller;

use AppBundle\Service\MapTest;
use AppBundle\Service\PingTest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render(
            'default/index.html.twig',
            [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            ]
        );
    }

    /**
     * @Route("pingtest", name="pingtest")
     */
    public function pingtestAction(PingTest $pingtest)
    {
        return $this->render(
            'tests/pingtest.html.twig',
            [
                'tests'   => $pingtest->readTests(),
                'actDate' => (new \DateTime())->format('Ymd'),
            ]
        );
    }

    /**
     * @Route("maptest", name="maptest")
     * @param MapTest $mapTest
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function maptestAction(MapTest $mapTest)
    {
        $testSuite = $mapTest->readTests();
        $macs      = $mapTest->getMacs($testSuite);

        $results = [];

        foreach ($testSuite as $date => $tests) {
            $result = new \stdClass();
            foreach ($tests as $test) {
                $result->headers[] = $date.' '.$test->time;
                $result->counts[]  = count($test->unknown);

                foreach ($macs[$date] as $mac => $macResults) {
                    $result->macs[$mac][$test->time] = array_key_exists($test->time, $macResults) ? 1 : 0;
                }
            }

            $results[$date] = $result;
        }

        return $this->render(
            'tests/maptest.html.twig',
            [
                'macs'      => $macs,
                'results'   => $results,
                'actDate'   => (new \DateTime())->format('Y-m-d'),
            ]
        );
    }
}
