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
}


echo '<ul id="progress">';
echo '<li class="'.$active1.'">'.$lang['btn_check_system'].'</li>';
echo '<li class="'.$active2.'">'.$lang['btn_enter_user'].'</li>';
echo '<li class="'.$active3.'">'.$lang['btn_enter_page_infos'].'</li>';
echo '<li class="'.$active4.'">'.$lang['btn_enter_database'].'</li>';
echo '</ul>';

include $inc;

/*
if(isset($_POST['step3'])) {
	if(strlen($_POST['psw']) < 8) {
		echo '<div class="alert alert-danger">';
		echo '<p>'.$lang['password_too_short'].'</p>';
		echo '<p><a href="javascript:history.back()" class="btn btn-default">'.$lang['pagination_backward'].'</a></p>';
		echo '</div>';
	} else {
		include 'php/createDB.php';
	}
} else if(isset($_POST['step2']) OR isset($_POST['check_database'])) {
	include 'php/form.php';
} else {
	include 'php/checkup.php';
}
*/

?>