<?php
	
if(!defined('INSTALLER')) {
	header("location:login.php");
	die("PERMISSION DENIED!");
}

foreach($_POST as $key => $val) {
	$$key = strip_tags($val); 
}

if($prefs_database_host == '') {
	$prefs_database_host = 'localhost';
}

if($prefs_database_port == '') {
	$prefs_database_port = '3306';
}

/* default inc file */
$inc = 'php/init_checkup.php';
$active1 = 'active';

if(isset($_POST['step2']) OR isset($_POST['check_user_data'])) {
	$inc = 'php/init_add_user.php';
	$active1 = 'active';
	$active2 = 'active';
}

if(isset($_POST['step3'])) {
	$inc = 'php/init_add_page_data.php';
	$active1 = 'active';
	$active2 = 'active';
	$active3 = 'active';
}

if(isset($_POST['step4']) OR isset($_POST['check_connection'])) {
	$inc = 'php/init_database.php';
	$active1 = 'active';
	$active2 = 'active';
	$active3 = 'active';
	$active4 = 'active';
}

if(isset($_POST['install_mysql']) OR isset($_POST['install_sqlite'])) {
	$inc = 'php/createDB.php';
	$active1 = 'active active-shine';
	$active2 = 'active active-shine';
	$active3 = 'active active-shine';
	$active4 = 'active active-shine';
}


echo '<ul id="progress">';
echo '<li class="'.$active1.'">'.$lang['btn_check_system'].'</li>';
echo '<li class="'.$active2.'">'.$lang['btn_enter_user'].'</li>';
echo '<li class="'.$active3.'">'.$lang['btn_enter_page_infos'].'</li>';
echo '<li class="'.$active4.'">'.$lang['btn_enter_database'].'</li>';
echo '</ul>';

include $inc;

?>