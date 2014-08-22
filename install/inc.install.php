<?php

$step = '1';

if(isset($_POST['step1'])) {
	$step = '1';
}

if(isset($_POST['step2'])) {
	$step = '2';
}

if(isset($_POST['step3'])) {
	$step = '3';
}


if($step == '1') {
	include("php/checkup.php");
}

if($step == '2') {
	include("php/form.php");
}

if($step == '3') {
	include("php/createDB.php");
}





?>