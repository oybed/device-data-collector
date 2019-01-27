<?php

define('DATASTORAGE', '/FileStore/DataLogging/MBusOgVer');

date_default_timezone_set('Europe/Oslo');

define('GENERAL_TIME', '1_1');

define('FYRROM_ENERGY1', '10_1');
define('FYRROM_POWER1', '10_2');
define('FYRROM_VOLUME1', '10_3');
define('FYRROM_TEMP1', '10_4');
define('FYRROM_TEMP2', '10_5');
define('FYRROM_TEMP3', '10_6');
define('FYRROM_TIME1', '10_7');

define('ENERGY_ENERGY1', '20_1');
define('ENERGY_POWER1', '20_2');
define('ENERGY_TIME1', '20_3');

define('WEATHER_OUTSIDETEMP', '30_1');
define('WEATHER_WINDCHILL', '30_2');
define('WEATHER_DEWPOINT', '30_3');
define('WEATHER_HUMIDITY', '30_4');
define('WEATHER_BAROMETER', '30_5');
define('WEATHER_WINDSPEED', '30_6');
define('WEATHER_WINDGUST', '30_7');
define('WEATHER_WINDDIRECTION', '30_8');
define('WEATHER_RAINRATE', '30_9');
define('WEATHER_RAINHRTOTAL', '30_10');
define('WEATHER_UV', '30_11');
define('WEATHER_SOLARRADIATION', '30_12');
define('WEATHER_ISS', '30_13');
define('WEATHER_TIME1', '30_14');

$options = getopt("aw:y:t:");

$data = array();
$Titles = array(
	GENERAL_TIME => 'Time',
	FYRROM_ENERGY1 => 'Energy (10kWh)',
	FYRROM_POWER1 => 'Power (100W)',
	FYRROM_VOLUME1 => 'Volume flow (m m^3/h)',
	FYRROM_TEMP1 => 'Flow temperature (1e-1 deg C)',
	FYRROM_TEMP2 => 'Return temperature (1e-1 deg C)', 
	FYRROM_TEMP3 => 'Temperature Difference (1e-2  deg C)',
	FYRROM_TIME1 => 'Time Point (time &amp; date)',
	ENERGY_ENERGY1 => 'Energy (10Wh)',
	ENERGY_POWER1 => 'Power (W)',
	ENERGY_TIME1 => 'Time Point (time &amp; date)',
	WEATHER_OUTSIDETEMP => 'Outside Temperature',
	WEATHER_WINDCHILL => 'Wind Chill',
	WEATHER_DEWPOINT => 'Dew Point',
	WEATHER_HUMIDITY => 'Humidity',
	WEATHER_BAROMETER => 'Barometer',
	WEATHER_WINDSPEED => 'Wind Speed',
	WEATHER_WINDGUST => 'Wind Gust',
	WEATHER_WINDDIRECTION => 'Wind Direction',
	WEATHER_RAINRATE => 'Rain Rate',
	WEATHER_RAINHRTOTAL => 'Rain Hourly Total',
	WEATHER_UV => 'UV',
	WEATHER_SOLARRADIATION => 'Solar Radiation',
	WEATHER_ISS => 'ISS Signal Quality',
	WEATHER_TIME1 => 'Time Point',
);

$week = '';
if (isset($options['w']) !== false)
{
	$week = $options['w'];
}

$year = '';
if (isset($options['y']) !== false)
{
	$year = $options['y'];
}

$time = '';
if (isset($options['t']) !== false)
{
	$time = $options['t'];
}

if (isset($options['t']) !== false)
{
	$path = DATASTORAGE;
	$path .= '/' . $year . '/' . $week;
	processFile($data, $path . '/fyrrom-' . $time . '.xml');
	processFile($data, $path . '/energy-' . $time . '.xml');
	processFile($data, $path . '/weather-' . $time . '.xml');
	writeTitlesToFile($path . '/ConsolidatedData.txt');
	writeDataToFile($data, $path . '/ConsolidatedData.txt');
}

if (isset($options['a']) !== false)
{
	processAllFiles($data);
}


function processFile(
	&$data,
	$file)
{
	if (substr($file, strlen($file) - 4) != '.xml')
	{
		return;
	}

	if (file_exists($file) !== true)
	{
		echo "Failed to find XML file: $file \n";
		return;
	}

	$xml = false;
	if (filesize($file) !== 0)
	{
		$xml = simplexml_load_file($file);
	}

	if (strstr($file, 'fyrrom') !== false)
	{
		if ($xml === false)
		{
			$tmp = array(
				FYRROM_ENERGY1 => '',
				FYRROM_POWER1 => '',
				FYRROM_VOLUME1 => '',
				FYRROM_TEMP1 => '',
				FYRROM_TEMP2 => '',
				FYRROM_TEMP3 => '',
				FYRROM_TIME1 => '',
			);
		}
		else
		{
			$energy1 = $xml->xpath("//DataRecord[@id='1']");
			$power1 = $xml->xpath("//DataRecord[@id='3']");
			$volume1 = $xml->xpath("//DataRecord[@id='4']");
			$temp1 = $xml->xpath("//DataRecord[@id='5']");
			$temp2 = $xml->xpath("//DataRecord[@id='6']");
			$temp3 = $xml->xpath("//DataRecord[@id='7']");
			$time1 = $xml->xpath("//DataRecord[@id='8']");

			$tmp = array(
				FYRROM_ENERGY1 => $energy1[0]->Value,
				FYRROM_POWER1 => $power1[0]->Value,
				FYRROM_VOLUME1 => $volume1[0]->Value,
				FYRROM_TEMP1 => $temp1[0]->Value,
				FYRROM_TEMP2 => $temp2[0]->Value,
				FYRROM_TEMP3 => $temp3[0]->Value,
				FYRROM_TIME1 => $time1[0]->Value,
			);
		}
	}
	elseif (strstr($file, 'energy') !== false)
	{
		if ($xml === false)
		{
			$tmp = array(
				ENERGY_ENERGY1 => '',
				ENERGY_POWER1 => '',
				ENERGY_TIME1 => '',
			);
		}
		else
		{
			$energy1 = $xml->xpath("//DataRecord[@id='0']");
			$power1 = $xml->xpath("//DataRecord[@id='13']");
			$time1 = $xml->xpath("//DataRecord[@id='16']");
			$tmp = array(
				ENERGY_ENERGY1 => $energy1[0]->Value,
				ENERGY_POWER1 => $power1[0]->Value,
				ENERGY_TIME1 => $time1[0]->Value,
			);
		}
	}
	elseif (strstr($file, 'weather') !== false)
	{
		if ($xml === false)
		{
			$tmp = array(
				WEATHER_OUTSIDETEMP => '',
				WEATHER_WINDCHILL => '',
				WEATHER_DEWPOINT => '',
				WEATHER_HUMIDITY => '',
				WEATHER_BAROMETER => '',
				WEATHER_WINDSPEED => '',
				WEATHER_WINDGUST => '',
				WEATHER_WINDDIRECTION => '',
				WEATHER_RAINRATE => '',
				WEATHER_RAINHRTOTAL => '',
				WEATHER_UV => '',
				WEATHER_SOLARRADIATION => '',
				WEATHER_ISS => '',
				WEATHER_TIME1 => '',
			);
		}
		else
		{
			$tmp = $xml->xpath("/weather");
			$tmp = array(
				WEATHER_OUTSIDETEMP => $tmp[0]->outsidetemp,
				WEATHER_WINDCHILL => $tmp[0]->windchill,
				WEATHER_DEWPOINT => $tmp[0]->dewpoint,
				WEATHER_HUMIDITY => $tmp[0]->humidity,
				WEATHER_BAROMETER => $tmp[0]->barometer,
				WEATHER_WINDSPEED => $tmp[0]->windspeed,
				WEATHER_WINDGUST => $tmp[0]->windgust,
				WEATHER_WINDDIRECTION => $tmp[0]->winddirection,
				WEATHER_RAINRATE => $tmp[0]->rainrate,
				WEATHER_RAINHRTOTAL => $tmp[0]->rainhourtotal,
				WEATHER_UV => $tmp[0]->uv,
				WEATHER_SOLARRADIATION => $tmp[0]->solarradiation,
				WEATHER_ISS => $tmp[0]->iss,
				WEATHER_TIME1 => $tmp[0]->datetime,
			);
		}
	}

	$matches = array();
	preg_match('/.+-([0-9]+)\.xml$/', $file, $matches);
	$index = $matches[1];
	if (isset($data[$index]) === true)
	{
		$tmp = array_merge($data[$index], $tmp);
	}

	$data[$index] = $tmp;
}

function processDir(
	&$data,
	$path)
{
	if (is_dir($path) === false)
	{
		return;
	}

	$files = scandir($path);
	foreach($files as $f)
	{
		if (($f == '.') ||
		    ($f == '..'))
		{
			continue;
		}

		processFile($data, $path . '/' . $f);
	}
}

function processAllFiles(
	&$data,
	$year='',
	$week='')
{
	$path = DATASTORAGE;
	if (empty($year) === false)
	{
		$path .= '/' . $year;
	}
	else
	{
		$entries = scandir($path);
		foreach($entries as $y)
		{
			if (($y == '.') ||
			    ($y == '..'))
			{
				continue;
			}

			processAllFiles($data, $y, $week);
		}

		return;
	}

	if (empty($week) === false)
	{
		$path .= '/' . $week;
		processDir($data, $path);
	}
	else
	{
		$entries = scandir($path);
		foreach ($entries as $w)
		{
			$newpath = $path . '/' . $w;
			if (($w == '.') ||
			    ($w == '..') ||
			    (is_dir($newpath) === false))
			{
				continue;
			}

			processDir($data, $newpath);
			unlink($newpath . '/ConsolidatedData.txt');
			writeTitlesToFile($newpath . '/ConsolidatedData.txt');
			writeDataToFile($data, $newpath . '/ConsolidatedData.txt');
		}
	}
}

function writeTitlesToFile(
	$filename)
{
	global $Titles;

	if (file_exists($filename) === true)
	{
		return;
	}

	$tmp = '';
	$tmp .= $Titles[GENERAL_TIME] . "\t";
	$tmp .= $Titles[FYRROM_ENERGY1] . "\t";
	$tmp .= $Titles[FYRROM_POWER1] . "\t";
	$tmp .= $Titles[FYRROM_VOLUME1] . "\t";
	$tmp .= $Titles[FYRROM_TEMP1] . "\t";
	$tmp .= $Titles[FYRROM_TEMP2] . "\t";
	$tmp .= $Titles[FYRROM_TEMP3] . "\t";
	$tmp .= $Titles[FYRROM_TIME1] . "\t";
	$tmp .= $Titles[ENERGY_ENERGY1] . "\t";
	$tmp .= $Titles[ENERGY_POWER1] . "\t";
	$tmp .= $Titles[ENERGY_TIME1] . "\t";
	$tmp .= $Titles[WEATHER_OUTSIDETEMP] . "\t";
	$tmp .= $Titles[WEATHER_WINDCHILL] . "\t";
	$tmp .= $Titles[WEATHER_DEWPOINT] . "\t";
	$tmp .= $Titles[WEATHER_HUMIDITY] . "\t";
	$tmp .= $Titles[WEATHER_BAROMETER] . "\t";
	$tmp .= $Titles[WEATHER_WINDSPEED] . "\t";
	$tmp .= $Titles[WEATHER_WINDGUST] . "\t";
	$tmp .= $Titles[WEATHER_WINDDIRECTION] . "\t";
	$tmp .= $Titles[WEATHER_RAINRATE] . "\t";
	$tmp .= $Titles[WEATHER_RAINHRTOTAL] . "\t";
	$tmp .= $Titles[WEATHER_UV] . "\t";
	$tmp .= $Titles[WEATHER_SOLARRADIATION] . "\t";
	$tmp .= $Titles[WEATHER_ISS] . "\t";
	$tmp .= $Titles[WEATHER_TIME1];
	$tmp .= "\n";

	file_put_contents($filename, $tmp);
}

function writeDataToFile(
	$data,
	$filename)
{
	$tmp = '';

	foreach($data as $time => $entry)
	{
		// $excelDate = 25569 + ($time / 86400);
		// $excelDate = date('r', $time);
		$excelDate = date('d M Y H:i:s', $time);
		$tmp .= $excelDate . "\t";
		$tmp .= $entry[FYRROM_ENERGY1] . "\t";
		$tmp .= $entry[FYRROM_POWER1] . "\t";
		$tmp .= $entry[FYRROM_VOLUME1] . "\t";
		$tmp .= $entry[FYRROM_TEMP1] . "\t";
		$tmp .= $entry[FYRROM_TEMP2] . "\t";
		$tmp .= $entry[FYRROM_TEMP3] . "\t";
		$tmp .= $entry[FYRROM_TIME1] . "\t";
		$tmp .= $entry[ENERGY_ENERGY1] . "\t";
		$tmp .= $entry[ENERGY_POWER1] . "\t";
		$tmp .= $entry[ENERGY_TIME1] . "\t";
		$tmp .= $entry[WEATHER_OUTSIDETEMP] . "\t";
		$tmp .= $entry[WEATHER_WINDCHILL] . "\t";
		$tmp .= $entry[WEATHER_DEWPOINT] . "\t";
		$tmp .= $entry[WEATHER_HUMIDITY] . "\t";
		$tmp .= $entry[WEATHER_BAROMETER] . "\t";
		$tmp .= $entry[WEATHER_WINDSPEED] . "\t";
		$tmp .= $entry[WEATHER_WINDGUST] . "\t";
		$tmp .= $entry[WEATHER_WINDDIRECTION] . "\t";
		$tmp .= $entry[WEATHER_RAINRATE] . "\t";
		$tmp .= $entry[WEATHER_RAINHRTOTAL] . "\t";
		$tmp .= $entry[WEATHER_UV] . "\t";
		$tmp .= $entry[WEATHER_SOLARRADIATION] . "\t";
		$tmp .= $entry[WEATHER_ISS] . "\t";
		$tmp .= $entry[WEATHER_TIME1];
		$tmp .= "\n";
	}

	file_put_contents($filename, $tmp, FILE_APPEND);	
}

?>
