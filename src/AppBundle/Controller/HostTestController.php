<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Host;
use AppBundle\Service\MapTest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HostTestController
 * @package AppBundle\Controller
 */
class HostTestController extends Controller
{
    /**
     * @Route("maptest", name="maptest")
     *
     * @param Request $request
     * @param MapTest $mapTest
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function hostTestAction(Request $request, MapTest $mapTest)
    {
        $dateStart = $request->request->get('date_start', (new \DateTime())->format('Y-m-d'));
        $dateEnd   = $request->request->get('date_end', (new \DateTime())->format('Y-m-d'));
        $testSuite   = $mapTest->readTests(new \DateTime($dateStart), new \DateTime($dateEnd));
        $macs        = $mapTest->getMacs($testSuite);
        $unknownMacs = $macs;
        $knownMacs   = [];
        $knownHosts  = [];

        foreach ($this->getDoctrine()
                     ->getRepository(Host::class)
                     ->findBy([], ['name' => 'desc']) as $host) {
            $knownHosts[$host->getMac()] = $host;
            foreach ($unknownMacs as $date => $macList) {
                if (array_key_exists($host->getMac(), $macList)) {
                    unset($unknownMacs[$date][$host->getMac()]);
                    $knownMacs[$date][$host->getName()] = $host;
                }
            }
        }

        $results = [];

        foreach ($testSuite as $date => $tests) {
            $result          = new \stdClass();
            $result->known   = [];
            $result->unknown = [];
            foreach ($tests as $test) {
                $result->headers[] = $date.' '.$test->time;
                $result->counts[]  = count($test->unknown);

                foreach ($macs[$date] as $mac => $macResults) {
                    if (array_key_exists($mac, $knownHosts)) {
                        $result->known[$knownHosts[$mac]->getName()][$test->time] = array_key_exists(
                            $test->time,
                            $macResults
                        ) ? 1 : 0;
                    } else {
                        $result->unknown[$mac][$test->time] = array_key_exists($test->time, $macResults) ? 1 : 0;
                    }
                }
            }

            $results[$date] = $result;
        }

        return $this->render(
            'tests/maptest.html.twig',
            [
                'actDate'   => (new \DateTime())->format('Y-m-d'),
                'dateStart' => $dateStart,
                'dateEnd'   => $dateEnd,
                'results'   => $results,
                'unknown'   => $unknownMacs,
                'knownMacs' => $knownMacs,
            ]
        );
    }

    /**
     * @Route("addhost", name="admin.addHost")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addHost(Request $request)
    {
        $host = (new Host())
            ->setMac($request->request->get('mac'))
            ->setName($request->request->get('name'))
            ->setVendor($request->request->get('vendor'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($host);
        $em->flush();

        $this->addFlash('success', 'Host has been added');

        return $this->redirectToRoute('maptest');
    }
}
