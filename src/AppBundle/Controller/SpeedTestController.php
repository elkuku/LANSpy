<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Speed;
use AppBundle\Service\SpeedTest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HostTestController
 * @package AppBundle\Controller
 */
class SpeedTestController extends Controller
{
    /**
     * @Route("speedtest", name="speedtest")
     *
     * @param Request   $request
     * @param SpeedTest $speedTest
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param MapTest $mapTest
     *
     */
    public function speedTestAction(Request $request, SpeedTest $speedTest)
    {
        $dateStart = $request->request->get('date_start', (new \DateTime())->format('Y-m-d'));
        $dateEnd   = $request->request->get('date_end', (new \DateTime())->format('Y-m-d'));
        $testSuite = $speedTest->readTests(
            new \DateTime($dateStart),
            new \DateTime($dateEnd),
            new \DateTimeZone('America/Guayaquil')
        );

        $testResults = [];

        foreach ($testSuite as $date => $tests) {
            /* @type Speed $test */
            foreach ($tests as $dateTime => $test) {
                $testResults[$date]['dates'][] = $dateTime;
                $testResults[$date]['downloads'][] = $test->getDownload();
                $testResults[$date]['uploads'][] = $test->getUpload();
            }
        }

        return $this->render(
            'tests/speedtest.html.twig',
            [
                'actDate'   => (new \DateTime())->format('Y-m-d'),
                'dateStart' => $dateStart,
                'dateEnd'   => $dateEnd,
                'tests'     => $testResults,
            ]
        );
    }
}
