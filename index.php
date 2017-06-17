<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 17/06/17
 * Time: 12:16
 */

$basePath = '/home/elkuku/repos/lilhelpers';
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
			$parts = explode(' ', $line);

			if (2 === count($parts))
			{
				$tests[$date][formatDateString($parts[0])] = intval($parts[1]);
			}
            elseif (1 == count($parts))
			{
				$tests[$date][formatDateString(trim($parts[0]))] = 0;
			}
		}
	}
}

ksort($tests);
?>
<html>
<body>


<?php
foreach ($tests as $i => $test)
{
	echo '<canvas id="chart-' . $i . '" width="400" height="100"></canvas>';
}

?>
<script src="bower_components/chart.js/dist/Chart.bundle.min.js"></script>
<script>

    function drawChart(i, labels, data) {
        //var ctx = document.getElementById("chart-" + i);
        new Chart(document.getElementById("chart-" + i), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: i,
                    data: data
                }]
            },
            options: {
                scales: {
                    xAxes: [{
                        type: 'time',
                        time: {
                            displayFormats: {
                                minute: 'h:mm'
                            }
                        }
                    }]
                }
            }
        });
    }
	<?php
	foreach ($tests as $i => $test)
	{
		echo "drawChart($i, ['" . implode("','", array_keys($test)) . "'], [" . implode(",", $test) . "]);";
	}

	?>

</script>
</body>
</html>
