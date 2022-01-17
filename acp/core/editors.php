<?php

/**
 * if we are editing pages, load the configuration from the selected theme
 * otherwise load the default theme configuration
 * if both do not work, load js/tinyMCE_config.js
 *
 */

$editor_tpl_folder = $fc_preferences['prefs_template'];

if(isset($page_template)) {
	$editor_tpl_folder = $page_template;
}

if($editor_tpl_folder == 'use_standard') {
	$prefs_template = $db_content->get("fc_preferences", "prefs_template", [
		"prefs_id" => 1
	]);
	$editor_tpl_folder = $prefs_template;
}

$editor_styles = '../styles/'.$editor_tpl_folder.'/css/editor.css';
$tinyMCE_config = '../styles/'.$editor_tpl_folder.'/js/tinyMCE_config.js';

if(!is_file("$editor_styles")) {
	$editor_styles = 'css/editor.css';
}

if(!is_file($tinyMCE_config)) {
	$tinyMCE_config = 'theme/js/tinyMCE_config.js';
}

$tinyMCE_config_contents = file_get_contents($tinyMCE_config);
?>