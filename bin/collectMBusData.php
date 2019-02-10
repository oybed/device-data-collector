<?php

define('CONSOLIDATE_CMD', '/usr/local/bin/consolidateData.php');
define('MBUS_CMD', '/usr/local/bin/mbus-tcp-request-data');

$ConfigData = parse_ini_file("/etc/ddc.conf");

$WeatherDataFile = getenv('WEATHERDATA_FILE') ?: $ConfigData['WEATHERDATA_FILE'] ?: '/var/www/html/weewx/xmldata.xml';
$DataStorage = getenv('DATASTORAGE') ?: $ConfigData['DATASTORAGE'] ?: '/FileStore/DataLogging/MBusOgVer';
$MbusDeviceAddress = getenv('MBUS_GW_ADDRESS') ?: $ConfigData['MBUS_GW_ADDRESS'] ?: '192.168.1.10';
$MbusDevicePort = getenv('MBUS_GW_PORT') ?: $ConfigData['MBUS_GW_PORT'] ?: '10001';

$devices = array(
	'ENERGY' => array(
		'id' => '10',
		'name' => 'energy'),
	'FYRROM' => array(
		'id' => '20',
		'name' => 'fyrrom'),
	);

date_default_timezone_set('Europe/Oslo');

$dateYear = date('Y');
$dateWeek = date('W');
# $dateFormat = date('d-M-Y-His');
$dateFormat = date('U');

$dataPath = $DataStorage . '/' . $dateYear . '/' . $dateWeek;
if (file_exists($dataPath) === false)
{
	mkdir($dataPath, 0777, true);
}

if (file_exists($WeatherDataFile) === true)
{
	rename($WeatherDataFile, $dataPath . '/weather-' . $dateFormat . '.xml');
}

foreach ($devices as $dev => $data)
{
	$cmd = MBUS_CMD . ' ' . $MbusDeviceAddress . ' ' . $MbusDevicePort . ' ' . $data['id']  . ' > ' . $dataPath . '/' . $data['name'] . '-' . $dateFormat . '.xml';
	exec($cmd);
}

$cmd = 'php ' . CONSOLIDATE_CMD . ' -y ' . $dateYear . ' -w ' . $dateWeek . ' -t ' . $dateFormat;
exec($cmd);


?>
