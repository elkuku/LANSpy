<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 21/06/17
 * Time: 17:49
 */

namespace AppBundle\Service;

/**
 * Class PingTest
 * @package AppBundle\Service
 */
class PingTest
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
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     *
     * @return array
     */
    public function readTests(\DateTime $startDate, \DateTime $endDate): array
    {
        $tests1 = [];
        $tests2 = [];

        foreach (new \DirectoryIterator($this->root) as $iterator) {
            if ($iterator->isDir()) {
                continue;
            }

            if (0 === strpos($iterator->getBasename(), 'pingtest2017')) {
                $date = substr($iterator->getBasename(), 8, 8);

                if (new \DateTime($this->formatDateString($date)) < $startDate) {
                    continue;
                }

                if (new \DateTime($this->formatDateString($date)) > $endDate) {
                    continue;
                }

                foreach (file($iterator->getPathname()) as $line) {
                    $parts = explode(' ', trim($line));

                    if (3 === count($parts)) {
                        $tests1[$date][$this->formatDateString($parts[0])] = intval($parts[1]);
                        $tests2[$date][$this->formatDateString($parts[0])] = intval($parts[2]);
                    } elseif (2 === count($parts)) {
                        $tests1[$date][$this->formatDateString($parts[0])] = intval($parts[1]);
                        $tests2[$date][$this->formatDateString($parts[0])] = 0;
                    } elseif (1 == count($parts)) {
                        $tests1[$date][$this->formatDateString($parts[0])] = 0;
                        $tests2[$date][$this->formatDateString($parts[0])] = 0;
                    }
                }
            }
        }

        ksort($tests1);
        ksort($tests2);

        $tests1 = array_reverse($tests1, true);
        $tests2 = array_reverse($tests2, true);

        return [$tests1, $tests2];
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
