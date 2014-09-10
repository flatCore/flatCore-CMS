<?php

//prohibit unauthorized access
require("core/access.php");

echo '<h3>Backup</h3>';
echo '<div class="alert alert-info">'.$lang['backup_description'].'</div>';

$data_folder = "../" . FC_CONTENT_DIR . "/SQLite";

/* delete (only) logfiles */
if(isset($_GET['delete'])) {
	$delete_file = basename($_GET['delete']);
	if((is_file("$data_folder/$delete_file")) && (substr("$delete_file", 0, 7) == 'logfile')) {
		if(unlink("$data_folder/$delete_file")) {
			echo '<div class="alert alert-success">'.$lang['msg_file_delete'].'</div>';
		} else {
			echo '<div class="alert alert-danger">'.$lang['msg_file_delete_error'].'</div>';
		}
	} else {
		echo '<div class="alert alert-danger">'.$lang['msg_file_delete_error'].'</div>';
	}
}

$dbfiles = glob("$data_folder/*.sqlite3");

echo"<div id='container'>";
echo"<div id='masonry-container'>";

foreach($dbfiles as $filename) {

	$db_file = basename($filename);
	$db_bytes = readable_filesize(filesize("$filename"));
	$db_time = date("d.m.Y H:i:s", filemtime($filename));
	
	$dload_link = "/acp/core/download.php?dl=$db_file";
	$delete_link = "acp.php?tn=system&sub=backup&delete=$db_file";
	
	echo '<div class="masonry-item">';
	echo '<div class="masonry-item-inner">';
	echo '<h4>'.$db_file.'</h4>';
	echo "<p>$lang[filesize]: ~ $db_bytes<br />$lang[lastedit]:<br />$db_time</p>";
	echo '<div class="btn-group btn-group-justified">';
	echo '<a class="btn btn-success btn-xs" href="'.$dload_link.'"><span class="glyphicon glyphicon-cloud-download"></span> '.$lang['download'].'</a>';
	if(substr("$db_file", 0, 7) == 'logfile') {
		echo '<a href="'.$delete_link.'" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"></span> '.$lang['delete'].'</a>';
	}
	echo '</div>';
  echo '</div>';
  echo '</div>'; 

}

echo '</div>'; // masonry-container
echo '</div>';
echo '<div class="clearfix"></div>';




?>