<?php

//prohibit unauthorized access
require("core/access.php");

echo '<h3>Backup</h3>';
echo '<div class="alert alert-info">'.$lang['backup_description'].'</div>';

$data_folder = "../" . FC_CONTENT_DIR . "/SQLite";

/* delete (only) logfiles */
if(isset($_POST['delete'])) {
	$delete_file = basename($_POST['file']);
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
	echo '<form action="?tn=system&sub=backup" method="POST">';
	echo '<div class="btn-group btn-group-justified" role="group">';
	echo '<div class="btn-group" role="group">';
	//echo '<input type="submit" class="btn btn-success btn-xs" name="dl" value="'.$lang['download'].'">';
	echo '<a class="btn btn-success btn-xs" href="'.$dload_link.'"><span class="glyphicon glyphicon-cloud-download"></span> '.$lang['download'].'</a>';
	echo '</div>';
	if(substr("$db_file", 0, 7) == 'logfile') {
		echo '<div class="btn-group" role="group">';
		echo '<input type="submit" class="btn btn-danger btn-xs" name="delete" value="'.$lang['delete'].'">';
		echo '</div>';
	}
	echo '</div>';
	echo '<input  type="hidden" name="file" value="'.$db_file.'">';
	echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
	echo '</form>';
  echo '</div>';
  echo '</div>'; 

}

echo '</div>'; // masonry-container
echo '</div>';
echo '<div class="clearfix"></div>';




?>