<?php

//prohibit unauthorized access
require("core/access.php");

echo '<fieldset>';
echo '<legend>Backup</legend>';
echo '<p>'.$lang['backup_description'].'</p>';
echo '</fieldset>';


$data_folder = "../" . FC_CONTENT_DIR . "/SQLite";


$dbfiles = glob("$data_folder/*.sqlite3");


echo"<div id='container'>";
echo"<div id='masonry-container'>";

foreach($dbfiles as $filename) {

	$db_file = basename($filename);
	$db_bytes = readable_filesize(filesize("$filename"));
	$db_time = date("d.m.Y H:i:s", filemtime($filename));
	
	echo '<div class="masonry-item">';
	echo '<div class="masonry-item-inner">';
	echo '<h4>'.$db_file.'</h4>';
	echo "<p>$lang[filesize]: ~ $db_bytes<br />$lang[lastedit]:<br />$db_time</p>";
	echo '<p><a class="btn btn-success btn-sm btn-block" href="/acp/core/download.php?dl='.$db_file.'"><span class="glyphicon glyphicon-cloud-download"></span> '.$lang['download'].'</a></p>';
  echo '</div>';
  echo '</div>'; 

}

echo '</div>'; // masonry-container
echo '</div>';
echo '<div class="clearfix"></div>';




?>