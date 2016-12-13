<?php
	
if(!defined('INSTALLER')) {
	header("location:login.php");
	die("PERMISSION DENIED!");
}

foreach($_POST as $key => $val) {
	$$key = strip_tags($val); 
}
if(isset($_GET['db']) && $_GET['db']=='insert'){
    include("php/selectdb.php");
}
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

?>