<?php

/*
 * include this file in all acp scripts
 * to check the session login var 'user_class'
 *
*/


if($_SESSION['user_class'] != "administrator"){
	//move back to site
	header("location:../index.php");
	//or die
	die("PERMISSION DENIED!");
}

?>