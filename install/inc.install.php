<?php
	
if(!defined('INSTALLER')) {
	header("location:login.php");
	die("PERMISSION DENIED!");
}

foreach($_POST as $key => $val) {
	$$key = strip_tags($val); 
}



if(isset($_POST['step3'])) {
	include("php/createDB.php");
} elseif(isset($_POST['step2'])) {
	include("php/form.php");
} else {
	include("php/checkup.php");
}

?>