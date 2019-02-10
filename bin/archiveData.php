<?php

date_default_timezone_set('Europe/Oslo');

$ConfigData = parse_ini_file("/etc/ddc.conf");
$DataStorage = getenv('DATASTORAGE') ?: $ConfigData['DATASTORAGE'] ?: '/FileStore/DataLogging/MBusOgVer';

function zipAllWeeks(
	$path,
	$year)
{
	$path .= '/' . $year;
	if (file_exists($path) === false)
	{
		return;
	}

	$entries = scandir($path);
	foreach ($entries as $dir)
	{
		// Skip this and parent dir,
		// also skip if it isn't a directory
		// also skip if the entry isn't in the format of one or two digits (e.g.: week number)
		// also skip if this is the current week as it is still being processed
		if (($dir == '.') ||
		    ($dir == '..') || 
		    (is_dir($path . '/' . $dir) === false) ||
		    (preg_match('/^[0-9]?[0-9]$/', $dir) != 1) || 
		    (($year == date('Y')) && ($dir == date('W'))))
		{	
			continue;
		}

		// don't process if the .zip file already exists...
		// - this is not to overwrite a good file
		if (file_exists($path . '/' . $dir . '.zip'))
		{
			continue;
		}

		$cmd = 'cd ' . $path . ' && /usr/bin/zip -r ' . $dir . '.zip ' . $dir;
		exec($cmd);
		
		$cmd = 'rm -rf ' . $path . '/' . $dir;
		exec($cmd);
	}
}

// zip up all entries for this and previous year ... 
// - earlier years are assumed to already have been processed
// - previous year is so the last week of a year will be processed - i.e.: when it rolls over around new-years
zipAllWeeks($DataStorage, date('Y'));
zipAllWeeks($DataStorage, date('Y', mktime(0, 0, 0, date('m'), date('d'), date('Y')-1)));

?>
