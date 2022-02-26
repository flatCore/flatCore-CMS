<?php

//prohibit unauthorized access
require 'core/access.php';


if(isset($_POST['save_prefs_language'])) {
	
	foreach($_POST as $key => $val) {
		$data[htmlentities($key)] = htmlentities($val);
	}
	fc_write_option($data,'fc');		
}

if(isset($_POST['save_hide_language'])) {
		
	$data['prefs_deactivated_languages'] = json_encode($_POST['hide_langs']);
	fc_write_option($data,'fc');
}


if(isset($_POST)) {
	/* read the preferences again */
	$fc_get_preferences = fc_get_preferences();
	
	foreach($fc_get_preferences as $k => $v) {
		$key = $fc_get_preferences[$k]['option_key'];
		$value = $fc_get_preferences[$k]['option_value'];
		$fc_preferences[$key] = $value;
	}
	
	foreach($fc_preferences as $k => $v) {
	   $$k = stripslashes($v);
	}
}


echo '<div class="row">';
echo '<div class="col-md-6">';

echo '<fieldset>';
echo '<legend>'.$lang['system_default_language'].'</legend>';
echo '<form action="acp.php?tn=system&sub=language" method="POST" class="form-horizontal">';

$get_all_languages = get_all_languages();

$select_default_language = '<select name="prefs_default_language" class="form-control custom-select">';
foreach($get_all_languages as $langs) {
	
	$selected = "";
	if($prefs_default_language == $langs['lang_folder']) {
		$selected = "selected";
	}
	
	$select_default_language .= '<option '.$selected.' value="'.$langs['lang_folder'].'">'.$langs['lang_desc'].'</option>';
}
$select_default_language .= '</select>';

echo '<div class="form-group">';
echo $select_default_language;
echo '</div>';

echo '<input type="submit" class="btn btn-save" name="save_prefs_language" value="'.$lang['save'].'">';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';
echo '</fieldset>';

echo '</div>';
echo '<div class="col-md-6">';

echo '<fieldset>';
echo '<legend>'.$lang['system_deactivate_languages'].'</legend>';
echo '<form action="acp.php?tn=system&sub=language" method="POST" class="form-horizontal">';

echo '<table class="table table-sm table-hover">';

$hidden_langs = json_decode($prefs_deactivated_languages);
foreach($get_all_languages as $langs) {
	
	$check = '';
	if(in_array($langs['lang_folder'],$hidden_langs)) {
		$check = 'checked';
	}
	
	echo '<tr>';
	echo '<td>';
	echo '<input type="checkbox" id="'.$langs['lang_folder'].'" class="form-check-input" name="hide_langs[]" value="'.$langs['lang_folder'].'" '.$check.'>';
	echo '</td>';
	echo '<td><label for="'.$langs['lang_folder'].'" class="d-block">'.$langs['lang_sign'].'</label></td>';
	echo '<td><label for="'.$langs['lang_folder'].'" class="d-block">'.$langs['lang_desc'].'</label></td>';
	echo '<td><label for="'.$langs['lang_folder'].'" class="d-block">'.$langs['lang_folder'].'</label></td>';
	echo '</tr>';
	
}

echo '</table>';

echo '<input type="submit" class="btn btn-save" name="save_hide_language" value="'.$lang['save'].'">';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';
echo '</fieldset>';

echo '</div>';
echo '</div>';




?>