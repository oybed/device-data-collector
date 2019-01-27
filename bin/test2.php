<?php

		$xml = simplexml_load_file('/FileStore/DataLogging/MBusOgVer/2014/38/weather-1410839462.xml');
                        $outsidetemp = $xml->xpath("/weather");
                        $windchill = $xml->xpath("//windchill");
                        $dewpoint = $xml->xpath("//dewpoint");
                        $humidity = $xml->xpath("//humidity");
                        $barometer = $xml->xpath("//barometer");
                        $windspeed = $xml->xpath("//windspeed");
                        $windgust = $xml->xpath("//windgust");
                        $winddirection = $xml->xpath("//winddirection");
                        $rainrate = $xml->xpath("//rainrate");
                        $rainhourtotal = $xml->xpath("//rainhourtotal");
                        $uv = $xml->xpath("//uv");
                        $solarradiation = $xml->xpath("//solarradiation");
                        $iss = $xml->xpath("//iss");
                        $time1 = $xml->xpath("//datetime");

	var_dump($outsidetemp);

	echo "Temp: " . $outsidetemp[0]->outsidetemp . "\n";

?>
