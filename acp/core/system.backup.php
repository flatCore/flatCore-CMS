<?php

//prohibit unauthorized access
require 'core/access.php';

echo '<div class="subHeader">';
echo '<h3>Backup</h3>';
echo '</div>';

echo '<div class="alert alert-info">'.$lang['backup_description'].'</div>';

$data_folder = FC_CONTENT_DIR . "/SQLite";

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


if(isset($_GET['vac'])) {
	
	$vac_file = $data_folder.'/'.basename($_GET['vac']);
	
	
	
	if(is_file($vac_file)) {
		
		echo '<div class="well">';
		echo '<h4>VACUUM: '.$vac_file.' </h4>';
		echo '<p>'.$lang['filesize'].': '.readable_filesize(filesize("$vac_file")). ' -> ';
		
		$dbh = new PDO("sqlite:".$vac_file);
		$sth = $dbh->prepare("VACUUM");
		$sth->execute();
		$dbh = null;
		clearstatcache();
		
		echo readable_filesize(filesize("$vac_file")). '</p>';
		
		echo '</div>';
		
	}
}

echo '<div class="card p-3">';

echo '<table class="table table-sm table-hover">';
echo '<thead>';
echo '<tr>';
echo '<th>'.$lang['filename'].'</th>';
echo '<th>'.$lang['filesize'].'</th>';
echo '<th>'.$lang['lastedit'].'</th>';
echo '<th class="text-right"></th>';
echo '</thead>';
echo '<tr>';

foreach($dbfiles as $filename) {

	$db_file = basename($filename);
	$db_bytes = readable_filesize(filesize("$filename"));
	$db_time = date("d.m.Y H:i:s", filemtime($filename));
	
	$dload_link = "/acp/core/download.php?dl=$db_file";
	$delete_link = "acp.php?tn=system&sub=backup&delete=$db_file";
	
	echo '<tr>';
	echo '<td>'.$db_file.'</td>';
	echo '<td>'.$db_bytes.'</td>';
	echo '<td>'.$db_time.'</td>';
	echo '<td class="text-right">';

	echo '<form action="?tn=system&sub=backup" method="POST">';
	echo '<div class="btn-group" role="group">';
	echo '<a class="btn btn-fc btn-sm text-success" href="'.$dload_link.'">'.$icon['download'].' '.$lang['download'].'</a>';
	echo '<a class="btn btn-fc btn-sm" title="VACUUM" href="?tn=system&sub=backup&vac='.$db_file.'">'.$icon['compress'].'</a>';

	if(substr("$db_file", 0, 7) == 'logfile') {
		echo '<button type="submit" class="btn btn-fc btn-sm text-danger" name="delete">'.$icon['trash_alt'].'</button>';
	}
	
	echo '</div>';
	echo '<input  type="hidden" name="file" value="'.$db_file.'">';
	echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
	echo '</form>';

  echo '</td>';  
  echo '</tr>'; 

}

echo '</table>';

echo '</div>'; //card


?>