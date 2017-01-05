<?php
	
if(!defined('INSTALLER')) {
	header("location:login.php");
	die("PERMISSION DENIED!");
}

foreach($_POST as $key => $val) {
	$$key = strip_tags($val); 
}

$inc = 'checkup.php';

if(isset($_GET['step2']) && $_GET['db'] == 'mysql') {
	$inc = 'dbform.php'; /* Form for MySQL data */
}

if(isset($_GET['step2']) && $_GET['db'] == 'sqlite') {
	$inc = 'form.php'; /* Form for Administrator Data */
}

if(isset($_GET['step3'])) {
	$inc = 'create_dbconfig.php'; /* Form for MySQL data */
}

if(isset($_GET['step4'])) {
	$inc = 'form.php'; /* Form for Administrator Data */
}

if(isset($_GET['step5'])) {
	$inc = 'createDB.php'; /* create SQLite or MySQL Database and fill in Basic Data */
}


include('php/'.$inc);

/*
if(isset($_GET['step5'])) {
	if(strlen($_POST['psw']) < 8) {
		echo '<div class="alert alert-danger">';
		echo '<p>'.$lang['password_too_short'].'</p>';
		echo '<p><a href="javascript:history.back()" class="btn btn-default">'.$lang['pagination_backward'].'</a></p>';
		echo '</div>';
	} else {
		include("php/createDB.php");
	}
    // Administrator Account
} elseif(isset($_GET['step4'])) {
	include("php/form.php");
    } elseif(isset($_GET['step3'])) {
	include("php/create_dbconfig.php");
} elseif(isset($_GET['step2'])) {
	include("php/dbform.php");
} else {
	include("php/checkup.php");
}
*/

?>