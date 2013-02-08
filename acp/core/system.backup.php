<?php

//prohibit unauthorized access
require("core/access.php");

echo '<fieldset>';
echo"<legend>Backup</legend>";
echo"<p>$lang[backup_description]</p>";
echo '</fieldset>';


$data_folder = "../" . FC_CONTENT_DIR . "/SQLite";


$dbfiles = glob("$data_folder/*.sqlite3");




foreach($dbfiles as $filename) {

	$db_file = basename($filename);
	$db_bytes = format_bytes(filesize("$filename"));
	$db_time = date("d.m.Y H:i:s", filemtime($filename));
	
	
	
	
	echo"<div class='floating-box bg-bright'>\r";
	
	echo"<h4 class='bold'>$db_file</h4>";
	echo"<p>$lang[filesize]: ~ $db_bytes<br />$lang[lastedit]:<br />$db_time</p>";
	echo"<p><a class='btn btn-success btn-mini' href=\"core/download.php?dl=$db_file\">$lang[download]</a></p>";
    
    
    echo"</div>\r";
    

}



echo"<div class='clear'></div>";






function format_bytes($bytes) {
   
   if ($bytes < 1024) {
   		return $bytes.' B';
   } elseif ($bytes < 1048576) {
   		return round($bytes / 1024, 2).' KB';
   } elseif ($bytes < 1073741824) {
   		return round($bytes / 1048576, 2).' MB';
   }
}


?>