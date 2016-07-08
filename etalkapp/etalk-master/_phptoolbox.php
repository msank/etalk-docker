<?php

// ===== Pop an alert message using javascript:alert() =====

function alert($message) {
   printJS('alert("'.str_replace("\"", "\\\"", $message).'");');
}

function datetime($syntax, $datetime) {
    $year = substr($datetime,0,4);
    $month = substr($datetime,5,2);
    $day = substr($datetime,8,2);
    $hour = substr($datetime,11,2);
    $min = substr($datetime,14,2);
    $sec = substr($datetime,17,2);

    return date($syntax, mktime($hour,$min,$sec,$month,$day,$year));
}

function urlStr($str) {
	// Supprime les caractères non-ASCII, remplace les espaces par des underscores et retourne le string en caractères minuscules
	$extension = fileExtension($str);
	$str = substr($str, 0, strlen($str)-strlen($extension)-1);
	$url = strtolower( preg_replace('/[^\a-zA-Z0-9_]/', '', str_replace(' ', '_', stripslashes(trim($str)))) );
	return ($url.'.'.$extension);
}

function fileExtension($str) {
	$name_parts = explode('.', $str);
	return $name_parts[count($name_parts)-1];
}

function hashName($file, $origName, $length=22) {
	$hash = str_replace('/', '-', substr(base64_encode(md5_file($file, true)), 0, $length));
	return $hash.'.'.fileExtension($origName);
}

function printJS($code, $debugMode=false) {
	if (!$debugMode) {
		preg_replace('/(^[\/]{2}[^\n]*)¦([\n]{1,}[\/]{2}[^\n]*)/', '', $code);		// Strip comments
		$code = str_replace("\t", '', $code);								// Strip formatting
		$code = str_replace("\n", ' ', $code);								// Strip line breaks
	}
	print('<script type="text/javascript" charset="utf-8"> // <![CDATA[ '."\n".$code."\n".'// ]]></script>');
}
function markdown($str) {
	return preg_replace('/(\*)(.+)(\*)/','<b>$2</b>', $str);
}