<?php

require '../lib/Medoo.php';
use Medoo\Medoo;

$conn = false;

try {
	$db_mysql = new Medoo([

		'type' => 'mysql',
		'database' => $_POST['prefs_database_name'],
		'server' => $_POST['prefs_database_host'],
		'username' => $_POST['prefs_database_username'],
		'password' => $_POST['prefs_database_psw'],
	 
		'charset' => 'utf8',
		'port' => $_POST['prefs_database_port'],
	 
		'prefix' => $_POST['prefs_database_prefix']
	]);
	
	$conn = true;
	
} catch (Exception $e) {
  $conn = false;
  $fail_msg  = '<div class="alert alert-danger">Database Connection failed<hr>';
  $fail_msg .= print_r($e,true);
  $fail_msg .= '</div>';
}

if($_POST['prefs_database_name'] == '' || $_POST['prefs_database_host'] == '' || $_POST['prefs_database_username'] == '' || $_POST['prefs_database_psw'] == '') {
	$conn = false;
}
	
?>