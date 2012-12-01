<?php

/**
 * read the preferences
 */

try {
$dbh = new PDO("sqlite:$fc_db_content");

	$sql = "SELECT	prefs_id,
    				prefs_status,
    				prefs_pagetitle,
    				prefs_pagesglobalhead,
    				prefs_template,
    				prefs_template_layout,
    				prefs_pagesubtitle,
    				prefs_userregistration,
    				prefs_showloginform,
    				prefs_logfile
    		FROM fc_preferences
    		WHERE prefs_status = 'active' ";
   

$row = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);

$pref_pagetitle     		= stripslashes($row[prefs_pagetitle]);
$pref_pagesubtitle 			= stripslashes($row[prefs_pagesubtitle]);
$prefs_pagesglobalhead	= stripslashes($row[prefs_pagesglobalhead]);
$pref_template     			= $row[prefs_template];
$pref_template_layout		= $row[prefs_template_layout];
$pref_userregistration  = $row[prefs_userregistration];
$pref_showloginform     = $row[prefs_showloginform];
$pref_logfile      			= $row[prefs_logfile];

}

catch (PDOException $e) {
	echo 'Error: ' . $e->getMessage();
}

	
$dbh = null;




?>