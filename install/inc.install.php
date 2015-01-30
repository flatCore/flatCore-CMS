<?php
	
if(!defined('INSTALLER')) {
	header("location:login.php");
	die("PERMISSION DENIED!");
}

$step = '1'; //default

foreach($_POST as $key => $val) {
	$$key = strip_tags($val); 
}

if($step == '1') {
	include("php/checkup.php");
} elseif($step == '2') {
	include("php/form.php");
} elseif($step == '3') {
	include("php/createDB.php");
}

?>