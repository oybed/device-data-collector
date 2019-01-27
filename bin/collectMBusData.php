<?php

define('WEATHERDATA_FILE', '/var/www/html/weewx/xmldata.xml');
define('DATASTORAGE', '/FileStore/DataLogging/MBusOgVer');
define('CONSOLIDATE_CMD', '/root/bin/consolidateData.php');
define('MBUS_CMD', '/usr/local/bin/mbus-tcp-request-data');
define('MBUS_GW', '192.168.20.70');
define('MBUS_GW_PORT', '10001');
define('ENERGY_ID', '10');
define('FYRROM_ID', '20');


$devices = array(
	'ENERGY' => array(
		'id' => '10',
		'name' => 'energy'),
	'FYRROM' => array(
		'id' => '20',
		'name' => 'fyrrom'),
	);

date_default_timezone_set('Europe/Oslo');

$dataPath = DATASTORAGE . '/' . date('Y') . '/' . date('W');
if (file_exists($dataPath) === false)
{
	mkdir($dataPath, 0777, true);
}

# $dateFormat = date('d-M-Y-His');
$dateFormat = date('U');
if (file_exists(WEATHERDATA_FILE) === true)
{
	rename(WEATHERDATA_FILE, $dataPath . '/weather-' . $dateFormat . '.xml');
}

foreach ($devices as $dev => $data)
{
	$cmd = MBUS_CMD . ' ' . MBUS_GW . ' ' . MBUS_GW_PORT . ' ' . $data['id']  . ' > ' . $dataPath . '/' . $data['name'] . '-' . $dateFormat . '.xml';
	exec($cmd);
}

$cmd = 'php ' . CONSOLIDATE_CMD . ' -y ' . date('Y') . ' -w ' . date('W') . ' -t ' . $dateFormat;
exec($cmd);


?>
