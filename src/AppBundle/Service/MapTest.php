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
                $date = substr($iterator->getBasename(), 9, 10);

                foreach (file($iterator->getPathname()) as $line) {
                    $tests[$date][] =  json_decode($line);
                }
            }
        }

        return $tests;
    }
}
