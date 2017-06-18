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
use Twig_Loader_Filesystem;

class Application
{
	private $root;
	private $config;
	private $basePath;

	public function __construct($root)
	{
		$this->root = $root;
		$this->readConfig();

		$basePath = $this->config['root'];

		if (strpos($basePath, '{user}'))
		{
			$basePath = str_replace('{user}', get_current_user(), $basePath);
		}

		$this->basePath = $basePath;

	}
	public function execute()
	{
		return (new Twig_Environment(new Twig_Loader_Filesystem($this->root.'/templates')))
			->render('index.html.twig', ['tests' => $this->readTests()]);
	}

	private function formatDateString($str)
	{
		preg_match_all('/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})/', $str, $m);

		return $m[1][0] . '-' . $m[2][0] . '-' . $m[3][0] . ' ' . $m[4][0] . ':' . $m[5][0];
	}

	private function readTests()
	{
		foreach (new \DirectoryIterator($this->basePath) as $iterator)
		{
			if ($iterator->isDir())
			{
				continue;
			}

			if (0 === strpos($iterator->getBasename(), 'pingtest2017'))
			{
				$date = substr($iterator->getBasename(), 8, 8);

				foreach (file($iterator->getPathname()) as $line)
				{
					$parts = explode(' ', trim($line));

					if (2 === count($parts))
					{
						$tests[$date][$this->formatDateString($parts[0])] = intval($parts[1]);
					}
					elseif (1 == count($parts))
					{
						$tests[$date][$this->formatDateString($parts[0])] = 0;
					}
				}
			}
		}

		ksort($tests);

		$tests = array_reverse($tests, true);

		return $tests;
	}

	/**
	 * @return mixed
	 */
	public function getConfig()
	{
		return $this->config;
	}

	private function readConfig()
	{
		$path = $this->root.'/etc/config.yml';

		if (false == file_exists($path))
		{
			$path = $this->root.'/etc/config.dist.yml';

			if (false == file_exists($path))
			{
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
