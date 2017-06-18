<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 17/06/17
 * Time: 12:16
 */

require_once 'vendor/autoload.php';

$loader = new Twig_Loader_Filesystem('templates');

$twig = new Twig_Environment($loader, array(
	'cache' => 'cache',
));

$basePath = '/home/'.get_current_user().'/repos/lilhelpers';
$tests    = [];

function formatDateString($str)
{
	preg_match_all('/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})/', $str, $m);

	return $m[1][0] . '-' . $m[2][0] . '-' . $m[3][0] . ' ' . $m[4][0] . ':' . $m[5][0];

}

foreach (new DirectoryIterator($basePath) as $iterator)
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
				$tests[$date][formatDateString($parts[0])] = intval($parts[1]);
			}
            elseif (1 == count($parts))
			{
				$tests[$date][formatDateString($parts[0])] = 0;
			}
		}
	}
}

ksort($tests);

$tests = array_reverse($tests, true);

echo $twig->render('index.html.twig', array('name' => 'Fabien', 'tests' => $tests));
