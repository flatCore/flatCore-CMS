<?php
	
if(!defined('INSTALLER')) {
	header("location:login.php");
	die("PERMISSION DENIED!");
}

foreach($_POST as $key => $val) {
	$$key = strip_tags($val); 
}

if(isset($_POST['step3'])) {
	if(strlen($_POST['psw']) < 8) {
		echo '<div class="alert alert-danger">';
		echo '<p>'.$lang['password_too_short'].'</p>';
		echo '<p><a href="javascript:history.back()" class="btn btn-default">'.$lang['pagination_backward'].'</a></p>';
		echo '</div>';
	} else {
		include("php/createDB.php");
	}
} elseif(isset($_POST['step2'])) {
	include("php/form.php");
} else {
	include("php/checkup.php");
}

?>