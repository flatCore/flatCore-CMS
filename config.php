<?php

/**
 * flatCore default Configuration file
 * this file will be replaced with every update
 *
 * you can expand/overwrite this config file
 * by adding your own config.php to FC_CONTENT_DIR (/content/config.php)
 * but make sure you do not destroy the folder structure
 */


/* Default Language -> de|en */
$languagePack = "de";
$lang = array();

/* mod_rewrite -> off|permalink */
$fc_mod_rewrite = "permalink";


/* time offset for rss feeds in seconds */
$fc_rss_time_offset = '1800';

 
/* define Folder structure */
define('FC_CONTENT_DIR',  "content");
define('FC_ACP_DIR',  "acp");

/* Database informations for mysql */
include ('dbconfig.php');

/* SQlite or MySQL */
if(isset($db_host)){
    $fc_db_content = "mysql:host=".$db_host.";dbname=".$db_name.";charset=utf8", $db_user, $db_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8";
    $fc_db_user    = "mysql:host=".$db_host.";dbname=".$db_name.";charset=utf8", $db_user, $db_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8";
    $fc_db_stats   = "mysql:host=".$db_host.";dbname=".$db_name.";charset=utf8", $db_user, $db_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8";

}else{
/* Database Files */
    $fc_db_content 	= FC_CONTENT_DIR . "/SQLite/content.sqlite3";
    $fc_db_user 	= FC_CONTENT_DIR . "/SQLite/user.sqlite3";
    $fc_db_stats 	= FC_CONTENT_DIR . "/SQLite/flatTracker.sqlite3";
    $dbpref         = "fc_";
    define("DB_PREFIX", $dbpref);
}


/**
 * Folders for uploaded content
 * images and other files
 */
 
$img_path 	= FC_CONTENT_DIR . "/images";
$files_path = FC_CONTENT_DIR . "/files";

define('FC_CORE_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

$fc_inc_dir = dirname($_SERVER['SCRIPT_NAME']);

if($fc_inc_dir == "/") {
	$fc_inc_dir = "";
}

if($fc_inc_dir != "") {
	$fc_inc_dir = "/$fc_inc_dir";
	$fc_inc_dir = str_replace('//','/',$fc_inc_dir);
}

define('FC_INC_DIR',  $fc_inc_dir);


if(is_file(FC_CONTENT_DIR . '/config.php')){
	include(FC_CONTENT_DIR . '/config.php');
}

?>
