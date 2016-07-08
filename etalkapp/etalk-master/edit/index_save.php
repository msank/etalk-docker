<?php
	date_default_timezone_set('Europe/Zurich');
	include('../_db.php');
	include('../_formutils.php');

	$types = array(
					'explanation' => 'Explication',
					'quotation' => 'Citation'
					);

	// Get folders ____________________________________________________________________
	$folders = array('' => '');
	if ($handle = opendir('../data')) {
		$dirs = scandir('../data');
		foreach ($dirs as $dir) {
			if (substr($dir, 0, 1)!='.') $folders[@++$i] = $dir;
		}
	}
	closedir($handle);

	// AJAX Handlers __________________________________________________________________
	if (isset($_REQUEST['f'])) {

		$results = array('status' => 'ok');
		switch ($_REQUEST['f']) {
			case 'getStructure':
				$structure = array();
				$content = db_s('sounds', array('dir' => $folders[$_REQUEST['dir']]), array('id' => 'ASC'));
				while ($file = db_fetch($content)) {
					if ($file['chaptering']=='section') {
						$structure[] = array('title' => $file['section_title'], 'p' => array());
					}
					else {
						if (count($structure)==0) {
							$structure = array(array('title' => '', 'p' => array(array('type' => $file['chaptering'], 'text' => $file['text']))));
						}
						else {
							$structure[count($structure)-1]['p'][] = array('type' => $file['chaptering'], 'text' => $file['text']);
						}
					}
				}
				$results['data'] = $structure;
				break;
			case 'saveSound':
				$datas = array(	'dir' => $folders[$_REQUEST['dir']],
								'text' => $_REQUEST['text'],
								'type' => $_REQUEST['type'],
								'entities' => $_REQUEST['entities'],
								'chaptering' => ($_REQUEST['chaptering']!=''?$_REQUEST['chaptering']:'continue'),
								'section_title' => $_REQUEST['section_title'],
								'file' => $_REQUEST['file'],
								'file_credits' => $_REQUEST['file_credits'],
								'file_link' => $_REQUEST['file_link'],
								);
#				if (isset($_REQUEST['u'])) {
#					$datas['file'] = $_REQUEST['u'];
#				}
				$content = db_s('sounds', array('id' => $_REQUEST['sound']));
				if ($f = db_fetch($content)) {
					db_u('sounds', array('id' => $f['id']), $datas);
				}
				else {
					$datas['id'] = $_REQUEST['sound'];
					db_i('sounds', $datas);
				}
				break;
			case 'saveMetas':
				$dir = $folders[$_REQUEST['dir']];
				$datas = array('title' => $_REQUEST['title'], 'author' => $_REQUEST['author'], 'date' => implode('-', array_reverse(explode('.', $_REQUEST['date']))));
				db_u('talks', array('dir' => $dir), $datas);
				break;
			default:break;
		}
		header("Content-Type: application/json; charset=utf-8");
		die(json_encode($results));
	}

	// Page Headers ============================================================================================================================================

	header("Cache-Control: no-cache");		#max-age=3600
	header("Content-Type: text/html; charset=utf-8");
	include('../_slimbox.php');
	include('../_jqueryui.php');
	echo '<!DOCTYPE HTML><html><head><title>eTalk Editor</title>';
		echo '<link rel="stylesheet" type="text/css" media="screen" href="/s/screen.css" />';
		echo @$metas;
		echo '<script src="/js/upload.min.js" type="text/javascript"></script>';
		echo '<script src="/js/editor.min.js" type="text/javascript"></script>';
	echo '</head>';
	echo '<body class="editor">';

	// Page Content ============================================================================================================================================
	echo '<header class="editor">';
		beginForm();
			printSelectInput('Répertoire', 'dir', @$_REQUEST['dir'], $folders, 'form', true);
		endForm();

		beginForm();
		printHiddenInput('f', 'saveMetas');
		if (@$_REQUEST['dir']!='') {
			printHiddenInput('dir', $_REQUEST['dir']);
			$sel = array('dir' => @$folders[$_REQUEST['dir']]);
			$talk = db_fetch(db_s('talks', $sel));
			if ($talk === false) {
				db_i('talks', $sel);
				$talk = db_fetch(db_s('talks', $sel));
			}
			echo '<div style="width:350px; float:right;">';
				printTextInput('Date', 'date', datetime('d.m.Y', $talk['date']), 10, 10);
				echo '<br/>';
				printTextInput('Thème', 'theme', $talk['theme'], 20, 255);
			echo '</div>';
			echo '<div style="width:660px; float:left;">';
				printTextInput('Intitulé', 'title', $talk['title'], 100, 255);
				echo '<br/>';
				printTextInput('Auteur', 'author', $talk['author'], 50, 255);
			echo '</div>';
		}
		echo '<div style="clear:both"></div><br/><br/><label>Outils :</label>';
		endForm();
		echo '<div id="tools">';
			echo '<div>';
				echo '<label>Séparateur de chapitres</label><div class="separator section" data-type="section"></div>';
			echo '</div><div>';
				echo '<label>Séparateur de paragraphes</label><div class="separator paragraph" data-type="paragraph"></div>';
			echo '</div><div>';
				echo '<button id="btnPreviewContents">Aperçu TdM</button>';
			echo '</div><div>';
				echo '<button id="btnSave">Enregistrer</button>';
			echo '</div><div>';
#				echo '<button value="">Annuler les modifications</button>';
			echo '</div>';
		echo '</div>';
		echo '<div id="previewContents"><div></div></div>';
	echo '</header>';
	// =============================================================================================
	echo '<section id="fileEdit">';
		echo '<a href="#" onclick="return closeImageForm(false);" class="closeBox">&times;</a>';
		echo '<h1>Propriétés de l’image</h1>';
		echo '<div>';
		beginForm('post');
			$fileUploadInputId = printUploadInput('Fichier', 'u', '', array('image/x-png', 'image/png', 'image/jpeg', 'image/pjpeg'), 'tmp/');
			echo '<br/>';
			printTextInput('Copyright', 'file_credits', '', 81);
			echo '<br/>';
			printTextInput('Lien web', 'file_link', '', 81);
			echo '<br/>';
			echo '<a href="#" onclick="return closeImageForm(true);" class="btn">OK</a>';
		endForm();
		echo '</div>';
	echo '</section>';
	echo '<section id="page">';
	if (@$_REQUEST['dir']!='' && substr($_REQUEST['dir'], 0, 1)!='.') {
		$allFiles = array();
		$r_allFiles = db_s('sounds', array('dir' => $folders[$_REQUEST['dir']]));
		while ($f = db_fetch($r_allFiles)) {
			$allFiles[$f['id']] = $f;
		}
		$i=1;
		$files = scandir('../data/'.$folders[$_REQUEST['dir']]);
		foreach ($files as $file) {
			$filePath = $folders[$_REQUEST['dir']].'/'.$file;
			if (substr($file, 0, 1)!='.'&&!is_dir('../data/'.$filePath)) {
				unset($allFiles[$filePath]);
				$content = db_s('sounds', array('id' => $filePath));
				$sound = db_fetch($content);
				beginForm('post');
				printHiddenInput('f', 'saveSound');
				printHiddenInput('dir', $_REQUEST['dir']);
				echo '<div class="pSepTarget '.($sound['chaptering']).'">';
					printHiddenInput('chaptering', $sound['chaptering']);
					printTextInput('', 'section_title', $sound['section_title'], 100, 255);
					echo '<img src="/i/close.png" />';
				echo '</div>';
				echo '<table><tr>';
					echo '<td class="number">'.($i).'</td>';
					echo '<td>';
					if (preg_match('/Firefox/i',$_SERVER['HTTP_USER_AGENT'])) {
						echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="200" height="20">';
						echo '<param name="movie" value="../simplay_player.swf?audiofile=../'.$filePath.'&configpath=simplaydata/config.xml">';
						echo '<param name="quality" value="high">';
						echo '<embed src="../simplay_player.swf?audiofile=../'.$filePath.'&configpath=simplaydata/config.xml" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="200" height="20"></embed></object>';
					}
					else {
						echo '<audio controls="controls" preload="none" src="../data/'.$filePath.'"><source src="../data/'.$filePath.'" type="audio/mp3" />HTML5 Only!</audio>';
					}
					echo '<br/><br/><small>'.$file.'</small><br/></td>';
					echo '<td>';
						printHiddenInput('sound', $filePath);
						printTextArea('', 'text', $sound['text'], 60, 3);
						echo '<br/>';
						printHiddenInput('file', $sound['file']);
						printHiddenInput('file_credits', $sound['file_credits']);
						printHiddenInput('file_link', $sound['file_link']);
						echo '<a href="#" onclick="return showImageForm(\''.$fileUploadInputId.'\', this);" class="btn '.($sound['file']==''?'':'filled').'">Image...</a>';
#						printUploadInput('', 'u', $sound['file'], array('image/x-png', 'image/png', 'image/jpeg', 'image/pjpeg'), 'tmp/');
					echo '</td><td>';
						printTextArea('Liens:', 'entities', $sound['entities'], 20, 3);
						echo '<br/>';
						printSelectInput('Type:', 'type', $sound['type'], $types);
					echo '</td>';
				echo '</tr></table>';
				endForm();
				$i++;
			}
		}
		// removed unused file records
		foreach ($allFiles as $id => $f) {
			db_d('sounds', array('id' => $id));
		}
		echo '</table>';
	}
	echo '</section>';

	echo '</body></html>';

?>