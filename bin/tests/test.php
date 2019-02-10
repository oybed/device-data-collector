<?php

$string = 'fyrrom-123456789.xml';

$matches = array();
preg_match('/.+-([0-9]+)\.xml$/', $string, $matches);

var_dump($matches);

?>
