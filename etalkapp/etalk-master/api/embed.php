<?php

	include('../_phptoolbox.php');

	// HTTP headers ________________________________________________________________________
	date_default_timezone_set('Europe/Zurich');
	header('Vary: Accept');
	header('Content-Type: application/x-javascript; charset=utf-8');

	// Convenience methods _________________________________________________________________
	echo 'function _sA(t,a,v){t.setAttribute(a,v);}';	// Add an attribute on t node
	echo 'function _w(t){document.write(t);}';			// Shorthand for document.write

	// Add the website's main CSS __________________________________________________________
	echo 'var _s=document.createElement("link");_sA(_s,"rel","stylesheet");_sA(_s,"type","text/css");_sA(_s,"href","http://'.$_SERVER['HTTP_HOST'].'/s/screen.css");';
	echo 'document.getElementsByTagName("head")[0].appendChild(_s);';

	// Actually output the iframe __________________________________________________________
	$ref = explode('/', $_REQUEST['ref']);
	echo '_w(\'<div class="etalk-embed" style="width:'.$_REQUEST['w'].'px;">\');';
		echo '_w(\'<iframe src="http://'.$_SERVER['HTTP_HOST'].'/index.php?embed=&dir='.$ref[0].'#'.$ref[1].'" style="height:'.$_REQUEST['h'].'px;"></iframe>\');';
		if ($_REQUEST['d']=='true') {
			include('../_db.php');
			$talk = db_fetch(db_s('talks', array('dir' => $ref[0])));
			$host = 'http://'.$_SERVER['HTTP_HOST'];
			echo '_w(\'<legend><a href="'.$host.'?dir='.addslashes($ref[0].'#'.$ref[1]).'"><img src="'.$host.'/i/go.png" alt="->" class="btn" /></a><h1>eTalk</h1><p>'.addslashes($talk['title']).'</p><p>'.addslashes($talk['author'].' | '.datetime('d.m.Y', $talk['date'])).'</p></legend>\');';
		}
	echo '_w(\'</div>\');';
