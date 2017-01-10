<?php

if(isset($page_template)) {
	$editor_tpl_folder = $page_template;
} else {
	$page_template = '';
}

if($page_template == "use_standard" OR $page_template == "") {

    if($db_type == 'sqlite') $db_host = CONTENT_DB;

	$dbh = dbconnect($db_type, $db_host, $db_user, $db_pass, $db_name);
	$sql = "SELECT prefs_template FROM ".DB_PREFIX."preferences WHERE prefs_id = 1";
	$result = dbquery($sql);
	$result = dbarray($result);
	$dbh = null;

	$editor_tpl_folder = "$result[prefs_template]";
}

$editor_styles = "../styles/$editor_tpl_folder/css/editor.css";

if(!is_file("$editor_styles")) {
	$editor_styles = "css/editor.css";
}


$tinyMCE_config = "../styles/$editor_tpl_folder/js/tinyMCE_config.js";

if(!is_file($tinyMCE_config)) {
	$tinyMCE_config = "js/tinyMCE_config.js";
}

$tinyMCE_config_contents = file_get_contents($tinyMCE_config);

?>
