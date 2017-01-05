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


/* Database Files */
$fc_db_content 	= FC_CONTENT_DIR . "/SQLite/content.sqlite3";
$fc_db_user 	= FC_CONTENT_DIR . "/SQLite/user.sqlite3";
$fc_db_stats 	= FC_CONTENT_DIR . "/SQLite/flatTracker.sqlite3";


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


/* Database informations for mysql */
if(is_file('dbconfig.php')) {
	include ('dbconfig.php');
}
// database functions
include (FC_CORE_DIR.'core/functions/func_database.php');

if(!isset($db_host)){
    $dbpref         = "fc_";
    define("DB_PREFIX", $dbpref);
}


if(is_file(FC_CONTENT_DIR . '/config.php')){
	include(FC_CONTENT_DIR . '/config.php');
}

?>