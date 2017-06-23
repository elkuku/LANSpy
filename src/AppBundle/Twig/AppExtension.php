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
        return [
            new Twig_Function('mapTestToJS', [$this, 'mapTestToJS']),
        ];
    }
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('cDate', [$this, 'dateFilter']),
        ];
    }

    public function dateFilter($string, $lang = 'es_ES', $pattern = 'd \'de\' MMMM \'del\' Y')
    {
        // Expected: YYYYMMDD
        preg_match('/(\d{4})(\d{2})(\d{2})/', $string, $m);

        if (4 != count($m)) {
            preg_match('/(\d{4})-(\d{2})-(\d{2})/', $string, $m);
            if (4 != count($m)) {
                return $string;
            }
        }

        $formatter = new \IntlDateFormatter($lang, \IntlDateFormatter::LONG, \IntlDateFormatter::LONG);
        $formatter->setPattern($pattern);

        return $formatter->format(new \DateTime(sprintf('%d-%d-%d', $m[1], $m[2], $m[3])));
    }

    public function mapTestToJS($data): string
    {
        $dataSets = [];

        $dataSet = new \stdClass();

        $dataSet->label = 'Hosts';
        $dataSet->data = $data->counts;

        $dataSets[] = sprintf("{label:'Counts',data:[%s]}", implode(',', $data->counts));

        foreach ($data->known as $name => $tests) {
            $dataSets[] = sprintf("{label:'%s',data:[%s]}", $name, implode(',', $tests));
        }

        foreach ($data->unknown as $mac => $tests) {
            $dataSets[] = sprintf("{label:'%s',data:[%s]}", $mac, implode(',', $tests));
        }

        return sprintf('[%s]', implode(',', $dataSets));
    }
}
