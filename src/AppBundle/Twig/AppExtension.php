<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 21/06/17
 * Time: 19:43
 */

namespace AppBundle\Twig;

use Twig_Function;

class AppExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new Twig_Function('cDate', 'generate_lipsum'),
        );
    }
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('cDate', array($this, 'dateFilter')),
        );
    }

    public function dateFilter($string, $lang = 'es_ES', $pattern = 'd \'de\' MMMM \'del\' Y')
    {
        // Expected: YYYYMMDD
        preg_match('/(\d{4})(\d{2})(\d{2})/', $string, $m);

        if (4 != count($m)) {
            return $string;
        }

        $formatter = new \IntlDateFormatter($lang, \IntlDateFormatter::LONG, \IntlDateFormatter::LONG);
        $formatter->setPattern($pattern);

        return $formatter->format(new \DateTime(sprintf('%d-%d-%d', $m[1], $m[2], $m[3])));
    }
}
