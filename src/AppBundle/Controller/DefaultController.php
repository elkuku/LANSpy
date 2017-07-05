<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Host;
use AppBundle\Service\MapTest;
use AppBundle\Service\PingTest;
use AppBundle\Service\SpeedTest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param PingTest  $pingTest
     * @param SpeedTest $speedTest
     *
     * @param MapTest   $hostsTest
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(PingTest $pingTest, SpeedTest $speedTest, MapTest $hostsTest)
    {
        $dateStart = new \DateTime('midnight');
        $dateEnd   = new \DateTime();
        $timezone  = new \DateTimeZone('America/Guayaquil');

        $dbHosts = $this->getDoctrine()
            ->getRepository(Host::class)
            ->findBy([], ['name' => 'desc']);

        return $this->render(
            'default/index.html.twig',
            [
                'pingTests'  => $pingTest->readTests($dateStart, $dateEnd),
                'speedTests' => $speedTest->convertResult($speedTest->readTests($dateStart, $dateEnd, $timezone)),
                'hostsTests' => $hostsTest->getResults($dateStart, $dateEnd, $dbHosts),
                'hostname' => trim(shell_exec('hostname')),
                'ip' => trim(shell_exec('hostname -I')),
            ]
        );
    }

    /**
     * @Route("about", name="about")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction()
    {
        return $this->render('default/about.html.twig');
    }
}
