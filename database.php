<?php

if(!defined('FC_SOURCE')) {
	die("PERMISSION DENIED!");
}

require FC_CORE_DIR.'/lib/Medoo.php';
use Medoo\Medoo;



if(is_file(FC_CORE_DIR.'/config_database.php')) {
	include FC_CORE_DIR.'/config_database.php';
	
	$db_type = 'mysql';
	 
	$database = new Medoo([
		'type' => 'mysql',
		'database' => "$database_name",
		'host' => "$database_host",
		'username' => "$database_user",
		'password' => "$database_psw",
		'charset' => 'utf8',
		'port' => $database_port,
		'prefix' => DB_PREFIX
	]);
	
	$db_content = $database;
	$db_user = $database;
	$db_statistics = $database;
	$db_posts = $database;
	
} else {
	
	$db_type = 'sqlite';
	
	define("CONTENT_DB", "$fc_db_content");
	define("USER_DB", "$fc_db_user");
	define("STATS_DB", "$fc_db_stats");
	define("INDEX_DB", "$fc_db_index");
	define("POSTS_DB", "$fc_db_posts");
	
	$db_content = new Medoo([
		'type' => 'sqlite',
		'database' => CONTENT_DB
	]);
	
	$db_user = new Medoo([
		'type' => 'sqlite',
		'database' => USER_DB
	]);
	
	$db_statistics = new Medoo([
		'type' => 'sqlite',
		'database' => STATS_DB
	]);
	$db_posts = new Medoo([
		'type' => 'sqlite',
		'database' => POSTS_DB
	]);
	
}

require_once FC_CORE_DIR . '/core/functions.php';
require_once FC_CORE_DIR . '/global/functions.php';

?>