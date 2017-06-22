<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 21/06/17
 * Time: 17:49
 */

namespace AppBundle\Service;


class PingTest
{
    private $root;

    public function __construct(string $root)
    {
        $this->root = $root.'/results';

    }
    public function hello()
    {
        return 'hellio';
    }

    /**
     * @return array
     */
    public function readTests(): array
    {
        $tests1 = [];
        $tests2 = [];
        foreach (new \DirectoryIterator($this->root) as $iterator) {
            if ($iterator->isDir()) {
                continue;
            }

            if (0 === strpos($iterator->getBasename(), 'pingtest2017')) {
                $date = substr($iterator->getBasename(), 8, 8);

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
        preg_match_all('/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})/', $string, $m);

        return $m[1][0].'-'.$m[2][0].'-'.$m[3][0].' '.$m[4][0].':'.$m[5][0];
    }
}
