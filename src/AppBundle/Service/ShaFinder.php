<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 16/05/17
 * Time: 2:45
 */

namespace AppBundle\Service;

/**
 * Class ShaFinder
 * @package AppBundle\Service
 */
class ShaFinder
{
    private $sha = 'n/a';

    /**
     * ShaFinder constructor.
     *
     * @param string $root
     */
    public function __construct(string $root)
    {
        if (file_exists($root.'/sha.txt')) {
            $this->sha = file_get_contents($root.'/sha.txt');
        } elseif (file_exists($root.'/.git/refs/heads/master')) {
            $this->sha = file_get_contents($root.'/.git/refs/heads/master');
        }
    }

    /**
     * Get the current SHA.
     *
     * @return string
     */
    public function getSha(): string
    {
        return $this->sha;
    }
}
