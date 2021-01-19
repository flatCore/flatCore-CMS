<?php

/**
 * list all pages
 * used in tinyMCE's links popup
 *
 *	{title: 'My page', value: '/my_page/'}, ...
 */

require '../../lib/Medoo.php';
use Medoo\Medoo;

include '../../config.php';
include 'functions.php';

$counter = 0;

if(is_file('../../config_database.php')) {
	include '../../config_database.php';
	$db_type = 'mysql';
	
	$database = new Medoo([

		'database_type' => 'mysql',
		'database_name' => "$database_name",
		'server' => "$database_host",
		'username' => "$database_user",
		'password' => "$database_psw",
	 
		'charset' => 'utf8',
		'port' => $database_port,
	 
		'prefix' => DB_PREFIX
	]);
	
	$db_content = $database;
	
} else {
	$db_type = 'sqlite';
	$db_content = new Medoo([
		'database_type' => 'sqlite',
		'database_file' => CONTENT_DB
	]);	
}

$page_data = $db_content->select("fc_pages", [
	"page_permalink",
	"page_title"
]);

foreach($page_data as $page) {
	$pages[$counter]['title'] = $page['page_title']. '-> (/'.$page['page_permalink'].')';
  $pages[$counter]['value'] = '/'.$page['page_permalink'];
	$counter++;
}

header('Content-type: text/javascript');
header('pragma: no-cache');
header('expires: 0');
echo json_encode($pages);

?>