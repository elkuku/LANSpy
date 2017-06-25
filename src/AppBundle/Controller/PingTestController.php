<?php

namespace AppBundle\Controller;

use AppBundle\Service\PingTest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PingTestController
 * @package AppBundle\Controller
 */
class PingTestController extends Controller
{
    /**
     * @Route("pingtest", name="pingtest")
     * @param Request  $request
     * @param PingTest $pingtest
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pingTestAction(Request $request, PingTest $pingtest)
    {
        $dateStart = $request->request->get('date_start', (new \DateTime())->format('Y-m-d'));
        $dateEnd   = $request->request->get('date_end', (new \DateTime())->format('Y-m-d'));

        return $this->render(
            'tests/pingtest.html.twig',
            [
                'actDate'   => (new \DateTime())->format('Ymd'),
                'dateStart' => $dateStart,
                'dateEnd'   => $dateEnd,
                'tests'     => $pingtest->readTests(new \DateTime($dateStart), new \DateTime($dateEnd)),
            ]
        );
    }

}
