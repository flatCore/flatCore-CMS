<?php

if(isset($page_template)) {
	$editor_tpl_folder = $page_template;
} else {
	$page_template = '';
}

if($page_template == "use_standard" OR $page_template == "") {

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "SELECT prefs_template FROM fc_preferences WHERE prefs_id = 1";
	$result = $dbh->query($sql);
	$result = $result->fetch(PDO::FETCH_ASSOC);
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

echo '<script language="javascript" type="text/javascript">';
echo "$tinyMCE_config_contents";
echo '</script>';

?>