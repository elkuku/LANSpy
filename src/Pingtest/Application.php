<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 18/06/17
 * Time: 14:42
 */

namespace Pingtest;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Twig_Environment;
use Twig_Filter;
use Twig_Loader_Filesystem;

/**
 * Class Application
 * @package Pingtest
 */
class Application
{
    private $root;
    private $config;
    private $basePath;

    /**
     * Application constructor.
     *
     * @param string $root
     */
    public function __construct(string $root)
    {
        $this->root = $root;
        $this->readConfig();

        $basePath = $this->config['root'];

        if (strpos($basePath, '{user}')) {
            $basePath = str_replace('{user}', get_current_user(), $basePath);
        }

        $this->basePath = $basePath;
    }

    /**
     * @return string
     */
    public function execute(): string
    {
        $twig = new Twig_Environment(new Twig_Loader_Filesystem($this->root.'/templates'));

        $twig->addFilter(
            new Twig_Filter(
                'cDate',
                function ($string) {
                    // Expected: YYYYMMDD
                    preg_match('/(\d{4})(\d{2})(\d{2})/', $string, $m);
                    if (4 != count($m)) {
                        return $string;
                    }
                    $lang = 'es_ES';
                    $pattern = 'd \'de\' MMMM \'del\' Y';
                    $formatter = new \IntlDateFormatter($lang, \IntlDateFormatter::LONG, \IntlDateFormatter::LONG);
                    $formatter->setPattern($pattern);

                    return $formatter->format(new \DateTime(sprintf('%d-%d-%d', $m[1], $m[2], $m[3])));
                }
            )
        );

        return $twig->render(
            'index.html.twig',
            ['tests' => $this->readTests(), 'actDate' => (new \DateTime())->format('Ymd')]
        );
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
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

    /**
     * @return array
     */
    private function readTests(): array
    {
        $tests1 = [];
        $tests2 = [];
        foreach (new \DirectoryIterator($this->basePath) as $iterator) {
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
     * @throws \Exception
     */
    private function readConfig()
    {
        $path = $this->root.'/etc/config.yml';

        if (false == file_exists($path)) {
            $path = $this->root.'/etc/config.dist.yml';

            if (false == file_exists($path)) {
                throw new \Exception('No config file found');
            }
        }

        try {
            $this->config = Yaml::parse(file_get_contents($path));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }
    }
}
