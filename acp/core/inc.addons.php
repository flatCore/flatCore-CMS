<?php
//error_reporting(E_ALL ^E_NOTICE);
//prohibit unauthorized access
require 'core/access.php';


/* delete addon */

if(isset($_POST['delete_addon'])) {
	fc_delete_addon($_POST['addon'],$_POST['type']);
	$all_mods = get_all_moduls();
	$all_plugins = get_all_plugins();
}


/**
 * list and access module
 * list plugins
 * list and access themes
 */

$addon_mode = 'list_modules';
$active_modules = 'active';
$active_plugins = '';
$active_themes = '';

/* access module */
if(isset($a) && (is_file("../modules/$sub/info.inc.php"))) {
	$addon_mode = 'access_module';
	unset($mod);
} else if($sub == 't') {
	$addon_mode = 'list_themes';
	$active_themes = 'active';
	$active_modules = '';
} else if($sub == 'p') {
	$addon_mode = 'list_plugins';
	$active_plugins = 'active';
	$active_modules = '';
} else if($sub == 'u') {
	$addon_mode = 'upload';
	$active_modules = '';
	$active_plugins = '';
	$active_themes = '';
	$active_upload = 'active';
}

if($sub == 'list' OR $sub == 'p' OR $sub == 'm' OR $sub == 't' OR $sub == 'u') {
	echo '<div class="subHeader">';
	
	if($_SESSION['drm_acp_sensitive_files'] == 'allowed') {
		echo '<div class="btn-group float-end" role="group">';
		echo '<a href="?tn=moduls&sub=u" class="btn btn-fc '.$active_upload.'">'.$icon['upload'].' '.$lang['btn_install'].'</a>';
		echo '</div>';
	}
	
	echo '<div class="btn-group" role="group">';
	echo '<a href="?tn=moduls&sub=m" class="btn btn-fc '.$active_modules.'">Module</a>';
	echo '<a href="?tn=moduls&sub=p" class="btn btn-fc '.$active_plugins.'">Plugins</a>';
	echo '<a href="?tn=moduls&sub=t" class="btn btn-fc '.$active_themes.'">Themes</a>';
	echo '</div>';
	echo '</div>';
}

/* list module */

if($addon_mode == 'list_modules') {
	include 'list.addons.php';
}

if($addon_mode == 'access_module') {
	include '../modules/'.$sub.'/info.inc.php';
	include '../modules/'.$sub.'/backend/'.$a.'.php';	
}


/* list themes */

if($addon_mode == 'list_themes') {
	include 'list.themes.php';
}


/* list plugins */

if($addon_mode == 'list_plugins') {
	include 'list.plugins.php';
}

/* upload/update addons */
if($addon_mode == 'upload') {
	if($_SESSION['drm_acp_sensitive_files'] == 'allowed') {
		include 'upload_addons.php';
	} else {
		echo '<div class="alert alert-danger">'.$lang['drm_no_access'].'</div>';
	}
	
}


?>