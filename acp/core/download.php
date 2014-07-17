<?php
session_start();

require("../../config.php");
require("access.php");

/*
 * DOWNLOAD FILES
 * USED FOR BACKUP SQLITE DB FILES
*/



if($_GET["dl"]) {


	$filename = basename($_GET['dl']);
	$download = '../../' . FC_CONTENT_DIR . '/SQLite/'.$filename;
		
	if(is_file("$download")) {
	
		if(preg_match("/MSIE/i", $_SERVER["HTTP_USER_AGENT"]) ) { 
			header("Content-type: application/x-msdownload"); 
		} else { 
			header("Content-type: application/octet-stream"); 
		}
		header("Content-Length: ".filesize($download)); 
		header("Content-Discription: Backup"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); 
		header("Content-Disposition: attachment; filename=$filename");  
		readfile($download);
		exit;
	
	} else {
		die("<b>File not found!</b>");
	}

} else {
	die("*** No file ***");
}




?>
