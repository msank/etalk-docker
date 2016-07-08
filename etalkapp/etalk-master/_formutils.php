<?php

	include_once('_phptoolbox.php');

	$GLOBALS['ctrl_id'] = 0;	// Unique identifier for form controles, mainly used for JS

	function beginForm($method='get', $action='', $multipart=false) {
		echo '<form id="'.('form'.$GLOBALS['ctrl_id']).'" method="'.$method.'" action="'.($action=''?$_SERVER['PHP_SELF']:'').'" '.($multipart?' enctype="multipart/form-data"':'').'>';
	}
	function endForm() {
		echo '</form>';
	}

	function printTextInput($title, $field, $default, $size, $maxchars=0, $comment='', $script='') {
		$GLOBALS['ctrl_id']++;
		if ($title!='') { echo '<label for="i'.$GLOBALS['ctrl_id'].'">'.$title.' </label>'; }
		echo '<input type="text" id="i'.$GLOBALS['ctrl_id'].'" name="'.$field.'" value="'.htmlspecialchars($default).'" size="'.$size.'" '.($maxchars>0?'maxlength="'.$maxchars.'"':'').' '.str_replace('$ID', $GLOBALS['ctrl_id'], $script).'/>';
		if ($comment != '') { echo '<span class="form_comment">'.$comment.'</span>'; }
		return 'i'.$GLOBALS['ctrl_id'];
	}

	function printPasswordInput($title, $field, $default, $size, $maxchars=0, $comment='') {
		$GLOBALS['ctrl_id']++;
		if ($title!='') { echo '<label for="i'.$GLOBALS['ctrl_id'].'">'.$title.' </label>'; }
		echo '<input type="password" id="i'.$GLOBALS['ctrl_id'].'" name="'.$field.'" value="'.htmlspecialchars($default).'" size="'.$size.'" '.($maxchars>0?'maxlength="'.$maxchars.'"':'').'/>';
		if ($comment != '') { echo '<span class="form_comment">'.$comment.'</span>'; }
		return 'i'.$GLOBALS['ctrl_id'];
	}

	function printTextArea($title, $field, $default, $cols, $rows) {
		$GLOBALS['ctrl_id']++;
		if ($title!='') { echo '<label class="form" for="i'.$GLOBALS['ctrl_id'].'">'.$title.' </label>'; }
		echo '<textarea id="i'.$GLOBALS['ctrl_id'].'" name="'.$field.'" cols="'.$cols.'" rows="'.$rows.'">'.stripslashes($default).'</textarea>';
		return 'i'.$GLOBALS['ctrl_id'];
	}

	function printSelectInput($title, $field, $default, $options, $autosubmit=false) {
		$GLOBALS['ctrl_id']++;
		if ($title!='') { echo '<label for="i'.$GLOBALS['ctrl_id'].'">'.$title.'</label> '; }
		echo '<select id="i'.$GLOBALS['ctrl_id'].'" class="styled" name="'.$field.'"'.($autosubmit?' onchange="javascript:this.form.submit();"':'').'>';
		foreach ($options as $option => $label) {
			echo '<option value="'.htmlspecialchars($option).'" '.($option==$default?' selected="selected"':'').'>'.$label.'</option>';
		}
		echo '</select>';
		return 'i'.$GLOBALS['ctrl_id'];
	}

	function printUploadInput($title, $field, $default='', $allowedTypes=array(), $path='./', $autoRename=true, $comment='') {
		$id = ++$GLOBALS['ctrl_id'];
		if ($title!='') { echo '<label for="i'.$GLOBALS['ctrl_id'].'">'.$title.' </label>'; }
		echo '<div id="i'.$GLOBALS['ctrl_id'].'" class="fu">';
			echo '<input type="hidden" id="del'.$GLOBALS['ctrl_id'].'" name="deleteFile[]" value="" />';
			echo '<span id="rep'.$GLOBALS['ctrl_id'].'" class="rep">';
				echo '<div class="field"></div>';
				echo '<input type="button" value="Replace" onclick="resetFile(\''.$GLOBALS['ctrl_id'].'\');" />';
			echo '</span>';
			echo '<span id="sel'.$GLOBALS['ctrl_id'].'" class="sel">';
				// Parameters _____________________________________________
				echo '<span class="hidden" id="allowedTypes'.$id.'">'.json_encode($allowedTypes).'</span>';
				echo '<input type="hidden" id="fNamePolicy'.$GLOBALS['ctrl_id'].'" value="'.($autoRename?'auto':'file').'" />';
				echo '<input id="fFileName'.$id.'" type="hidden" name="'.$field.'" value="" />';
				echo '<input id="fFileType'.$id.'" type="hidden" name="'.$field.'_T" value="" />';
				// File select + infos ____________________________________
				echo '<span id="fc'.$id.'"><input type="file" name="fileToUpload" id="f'.$id.'" onchange="fileSelected(\''.$id.'\');"/></span>';	# multiple="multiple"
				echo '<div id="info'.$id.'" class="fu_fileInfo"></div>';
				// Upload monitor _________________________________________
				echo '<div id="t'.$id.'" class="fu_progress">';
					echo '<img id="icon'.$id.'" src="" width="16" height="16" alt="..." />';
					echo '<div id="progressLabel'.$id.'" class="progressValue">&nbsp;</div>';
					echo '<div class="progressBar"><div id="progressBar'.$id.'" class="progressLevel"></div></div>';
				echo '</div>';
			echo '</span>';
		echo '</div>';
		return 'fFileName'.$id;	#'i'.$GLOBALS['ctrl_id']
	}

	function printHiddenInput($field, $value) {
		$GLOBALS['ctrl_id']++;
		echo '<input type="hidden" id="i'.$GLOBALS['ctrl_id'].'" name="'.$field.'" value="'.htmlspecialchars($value).'" />';
		return 'i'.$GLOBALS['ctrl_id'];
	}

	function printSubmitInput($field, $title, $alignLabel=false) {
		if ($alignLabel) { echo '<label>&nbsp;</label>'; }
		echo '<input type="submit" name="'.$field.'" value="'.htmlspecialchars($title).'" />';
	}

?>