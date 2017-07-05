<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 21/06/17
 * Time: 17:49
 */

namespace AppBundle\Service;

use AppBundle\Entity\Speed;

/**
 * Class PingTest
 * @package AppBundle\Service
 */
class SpeedTest
{
    private $root;

    /**
     * PingTest constructor.
     *
     * @param string $root
     */
    public function __construct(string $root)
    {
        $this->root = $root.'/results';
    }

    /**
     * @param \DateTime     $startDate
     * @param \DateTime     $endDate
     * @param \DateTimeZone $timezone
     *
     * @return array
     */
    public function readTests(\DateTime $startDate, \DateTime $endDate, \DateTimeZone $timezone): array
    {
        $tests = [];

        foreach (new \DirectoryIterator($this->root) as $iterator) {
            if ($iterator->isDir()) {
                continue;
            }

            if (0 === strpos($iterator->getBasename(), 'speedtest')) {
                $date = substr($iterator->getBasename(), 10, 10);

                if (new \DateTime($this->formatDateString($date)) < $startDate) {
                    continue;
                }

                if (new \DateTime($this->formatDateString($date)) > $endDate) {
                    continue;
                }

                foreach (file($iterator->getPathname()) as $line) {
                    $test = json_decode($line);
                    if ($test) {
                        $speed = new Speed();
                        $speed->setTimestamp($test->timestamp);
                        $speed->setUpload($test->upload/1000);
                        $speed->setDownload($test->download/1000);
                        $speed->setSponsor($test->server->sponsor);

                        $dt = $speed->getTimestamp();
                        $dt->setTimezone($timezone);

                        $tests[$date][$dt->format('Y-m-d H:i')] = $speed;
                    }
                }
            }
        }

        ksort($tests);

        $tests = array_reverse($tests, true);

        return $tests;
    }

    /**
     * @param array $testSuite
     *
     * @return array
     */
    public function convertResult(array $testSuite):array
    {
        $results = [];

        foreach ($testSuite as $date => $tests) {
            /* @type Speed $test */
            foreach ($tests as $dateTime => $test) {
                $results[$date]['dates'][] = $dateTime;
                $results[$date]['downloads'][] = $test->getDownload();
                $results[$date]['uploads'][] = $test->getUpload();
            }
        }

        return $results;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function formatDateString(string $string): string
    {
        if (preg_match('/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})/', $string, $m)) {
            return $m[1].'-'.$m[2].'-'.$m[3].' '.$m[4].':'.$m[5];
        }

        if (preg_match('/(\d{4})(\d{2})(\d{2})/', $string, $m)) {
            return $m[1].'-'.$m[2].'-'.$m[3];
        }

        return $string;
    }
}
