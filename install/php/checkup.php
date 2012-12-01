<?php

//$goto_install = array();

/**
 * function checkwritable()
 * check folders and files
 */

function checkwritable($path) {

	global $goto_install;
	global $lang;
	
	echo"<div class='block'>";
	echo"<div class='left'>$path</div>";
	
	if(!is_writable("$path")){
	
		echo"<span class='red'>$lang[permission_false]</span>";
		$goto_install[] = "false";
	
	} else {
	
		echo"<span class='green'>$lang[permission_true]</span>";
		$goto_install[] = "true";
	
	}
	
	echo"<div style='clear: both;'></div>";
	echo"</div>";

}



function checkexistingdir($path) {

	global $goto_install;
	
	if(!is_dir("$path")){
		echo"<div class='block'>";
		echo"<div class='left'>$path</div>";
		echo"<span class='red'>$lang[missing_folder]</span>";
		$goto_install[] = "false";
		echo"<div style='clear: both;'></div>";
		echo"</div>";
	
	}

}





/* collecting files and folders */


$check_this[] = "../" . FC_CONTENT_DIR . "/";
$check_this[] = "../$img_path";
$check_this[] = "../$files_path";
$check_this[] = "../" . FC_CONTENT_DIR . "/avatars";
$check_this[] = "../" . FC_CONTENT_DIR . "/files";
$check_this[] = "../" . FC_CONTENT_DIR . "/plugins";


$check_is_dir[] = "../modules/";
$check_is_dir[] = "../lib/";
$check_is_dir[] = "../styles/";
$check_is_dir[] = "../core/";
$check_is_dir[] = "../acp/";

/* minimum php version */

$needed_phpversion = "5.2";


echo"<h2>$lang[files_and_folders]</h2>";

foreach($check_this as $filepath){
	checkwritable("$filepath");
}

foreach($check_is_dir as $dir){
	checkexistingdir("$dir");
}

echo"<h2>$lang[system_requirements]</h2>";

$version = phpversion();

echo"<div class='left'>PHP Version</div>";

if($version < $needed_phpversion) {
	echo"<span class='red'>$lang[php_false] ($needed_phpversion)</span>";
	$goto_install[] = "false";
} else {
	echo"<span class='green'>$lang[php_true] ($needed_phpversion)</span>";
	$goto_install[] = "true";
}


echo"<div class='left'>SQLITE</div>";

if (in_array("pdo_sqlite", get_loaded_extensions())) {
	echo"<span class='green'>$lang[pdo_true]</span>";
	$goto_install[] = "true";
} else {
	echo"<span class='red'>$lang[pdo_false]</span>";
	$goto_install[] = "false";
}




if(!in_array("false",$goto_install)) {

	echo"<hr><form class='formnav' action='index.php' method='POST'>
	<input type='submit' class='submit' name='step2' value='$lang[step] 2'>
	</form>";

}




?>