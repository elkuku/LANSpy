<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 21/06/17
 * Time: 17:49
 */

namespace AppBundle\Service;

/**
 * Class MapTest
 * @package AppBundle\Service
 */
class MapTest
{
    private $root;

    /**
     * MapTest constructor.
     *
     * @param string $root
     */
    public function __construct(string $root)
    {
        $this->root = $root.'/results';
    }

    /**
     * @return array
     */
    public function readTests(): array
    {
        $tests = [];

        foreach (new \DirectoryIterator($this->root) as $iterator) {
            if ($iterator->isDir()) {
                continue;
            }

            if (0 === strpos($iterator->getBasename(), 'maptest-2017')) {
                $date = substr($iterator->getBasename(), 8, 10);

                foreach (file($iterator->getPathname()) as $line) {
                    $t = json_decode($line);

                    if (!$t) {
                        throw new \UnexpectedValueException('Can not decode '.$line);
                    }
                    $tests[$date][] = json_decode($line);
                }
            }
        }

        ksort($tests);

        $tests = array_reverse($tests);

        return $tests;
    }

    public function getMacs(array $testSuite)
    {
        $macs = [];

        foreach ($testSuite as $date => $tests) {
            foreach ($tests as $i => $test) {
                foreach ($test->unknown as $host) {
                    $macs[$date][$host->mac][$test->time] = $host;
                }
            }
        }

        return $macs;
    }
}
