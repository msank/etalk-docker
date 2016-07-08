<?php

if ($_SERVER['HTTP_HOST']=='127.0.0.1'||$_SERVER['HTTP_HOST']=='etalk.cc') {
	$GLOBALS['DBHost'] = 'localhost';
	$GLOBALS['DBUser'] = 'php';
	$GLOBALS['DBPass'] = 'polipo98';
}
else {
	$GLOBALS['DBHost'] = '172.17.0.2';
	$GLOBALS['DBUser'] = 'root';
	$GLOBALS['DBPass'] = '1234';
}
$GLOBALS['DBName'] = 'etalk';

define('DB_ENCODING', 'UTF8');
