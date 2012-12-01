<?php

/**
 * Configuration file
 */


/* define Folder structure */

define('FC_CONTENT_DIR',  "content");
define('FC_ACP_DIR',  "acp");


/* Default Language -> de|en */

$languagePack = "de";


/* mod_rewrite -> off|permalink */

$fc_mod_rewrite = "permalink";

/* Systemmessages, E-Mail & Name */

$fc_mailer_adr	= "you@example.com";
$fc_mailer_name	= "flatCore Administrator";



/**
 * from here make no more changes
 * except you know what you do
 *
 * $fc_db_content -> content database
 * $fc_db_user -> user database
 * $fc_db_stats -> database for logs and reports
 */

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

$fc_inc_dir = dirname($_SERVER[SCRIPT_NAME]);

if($fc_inc_dir == "/") {
	$fc_inc_dir = "";
}

if($fc_inc_dir != "") {
	$fc_inc_dir = "/$fc_inc_dir";
	$fc_inc_dir = str_replace('//','/',$fc_inc_dir);
}

define('FC_INC_DIR',  $fc_inc_dir);

?>