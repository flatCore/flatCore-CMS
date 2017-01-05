<?php

//$goto_install = array();

/**
 * function checkwritable()
 * check folders and files
 */

if(!defined('INSTALLER')) {
	header("location:../login.php");
	die("PERMISSION DENIED!");
}

function checkwritable($path) {

	global $goto_install;
	global $lang;
	
	echo '<div class="row">';
	echo '<div class="col-md-4"><p>'.$path.'</p></div>';
	echo '<div class="col-md-8">';
	if(!is_writable("$path")){
	
		echo '<p class="text-danger">' . $lang['permission_false'] . '</p>';
		$goto_install[] = "false";
	
	} else {
	
		echo '<p class="text-success">' . $lang['permission_true'] . '</p>';
		$goto_install[] = "true";
	
	}
	
	echo '</div>';
	echo '</div>';

}



function checkexistingdir($path) {

	global $goto_install;
	
	if(!is_dir("$path")){
		echo '<div class="row">';
		echo '<div class="col-md-4"><p>'.$path.'</p></div>';
		echo '<div class="col-md-8">';
		echo '<p class="text-danger">' . $lang['missing_folder'] . '</p>';
		$goto_install[] = "false";
		echo '</div>';
		echo '</div>';
	
	}

}





/* collecting files and folders */


$check_this[] = "../" . FC_CONTENT_DIR . "/";
$check_this[] = "../$img_path";
$check_this[] = "../$files_path";
$check_this[] = "../" . FC_CONTENT_DIR . "/avatars";
$check_this[] = "../" . FC_CONTENT_DIR . "/files";
$check_this[] = "../" . FC_CONTENT_DIR . "/plugins";
//$check_this[] = "../dbconfig.php";

$check_is_dir[] = "../modules/";
$check_is_dir[] = "../lib/";
$check_is_dir[] = "../styles/";
$check_is_dir[] = "../core/";
$check_is_dir[] = "../acp/";

/* minimum php version */

$needed_phpversion = "5.5";


echo"<h3>$lang[files_and_folders]</h3>";

foreach($check_this as $filepath){
	checkwritable("$filepath");
}

foreach($check_is_dir as $dir){
	checkexistingdir("$dir");
}

echo"<h3>$lang[system_requirements]</h3>";

$version = phpversion();

echo '<div class="row">';
echo '<div class="col-md-4">PHP Version</div>';
echo '<div class="col-md-8">';
	
if($version < $needed_phpversion) {
	echo '<div class="alert alert-danger">' . $lang['php_false'] . '</div>';
	$goto_install[] = "false";
} else {
	echo '<div class="alert alert-success">' . $lang['php_true'] . ' ('.$version.')</div>';
	$goto_install[] = "true";
}

echo '</div>';
echo '</div>';


echo '<div class="row">';
echo '<div class="col-md-4">'.$lang['label_database'].'</div>';
echo '<div class="col-md-8">';

if(in_array("pdo_sqlite", get_loaded_extensions())) {
	echo '<div class="alert alert-success">' . $lang['pdo_true'] . '</div>';
	$goto_install[] = "true";
	$pdo_sqlite = true;
} else {
	echo '<div class="alert alert-danger">' . $lang['pdo_false'] . '</div>';
	$goto_install[] = "false";
	$pdo_sqlite = false;
}

if(in_array("pdo_mysql", get_loaded_extensions())) {
	echo '<div class="alert alert-success">' . $lang['pdo_mysql_true'] . '</div>';
	$pdo_mysql = true;
} else {
	echo '<div class="alert alert-danger">' . $lang['pdo_mysql_false'] . '</div>';
	$pdo_mysql = false;
}

echo '</div>';
echo '</div>';


if(!in_array("false",$goto_install)) {

	echo '<hr><form action="index.php?step2" method="POST">';
	echo '<div class="row">';
	echo '<div class="col-md-4"></div>';
	echo '<div class="col-md-8">';

	if($pdo_sqlite == true) {
		echo '<a href="?step2&db=sqlite" class="btn btn-success btn-block">'.$lang['install_sqlite'].'</a>';
	} else {
		echo '<a href="#" class="btn btn-default btn-block disabled">'.$lang['install_sqlite'].'</a>';
	}
	if($pdo_mysql == true) {
		echo '<a href="?step2&db=mysql" class="btn btn-success btn-block">'.$lang['install_mysql'].'</a>';
	} else {
		echo '<a href="#" class="btn btn-default btn-block disabled">'.$lang['install_mysql'].'</a>';
	}
		

	echo '</div>';
	echo '</div>';
	
	echo"</form>";

}




?>