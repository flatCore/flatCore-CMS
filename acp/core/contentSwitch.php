<?php
	
require 'core/access.php';

/**
 * we can choose between different content files
 * store your files as array in your own config.php
 * example: $fc_content_files array (	array (	'file'	=> 'content.sqlite3',	'desc'	=> 'Standard SQLite Database' ), array ( ... ) );
 */

if(!isset($_SESSION['fc_db_content'])) {
	$_SESSION['fc_db_content'] = FC_CONTENT_DIR . '/SQLite/'.$fc_content_files[0]['file'];
}

if(isset($_POST['switchContent'])) {
	$switchContentId = (int) $_POST['switchContent'];
	$switchContentFile = FC_CONTENT_DIR . '/SQLite/'.$fc_content_files[$switchContentId]['file'];
	
	if(is_file($switchContentFile)) {
		$_SESSION['fc_db_content'] = $switchContentFile;
	}
}

if(isset($_SESSION['fc_db_content'])) {
	if(is_file($_SESSION['fc_db_content'])) {
		$fc_db_content = $_SESSION['fc_db_content'];
	}
}


$fc_content_switch = '<button class="btn btn-fc btn-sm" data-bs-target="#contentSwitchContainer" data-bs-toggle="collapse">'.$icon['angle_down'].' '.basename($fc_db_content).'</button>';

$fc_content_switch .= '<div id="contentSwitchContainer" class="collapse">';
$fc_content_switch .= '<div class="well well-sm">';

$i=0;
foreach($fc_content_files as $files) {
	$btn_class = 'btn-outline-secondary';
	
	if($files['file'] == basename($fc_db_content)) {
		$btn_class = 'btn-secondary';
	}

    $fc_content_switch .= '<form action="?tn=dashboard" method="POST" class="d-inline me-2">';
    $fc_content_switch .= '<button type="submit" name="switchContent" value="'.$i.'" class="btn '.$btn_class.'">'.$icon['database'].' '.$files['file'].'</button>';
    $fc_content_switch .= $hidden_csrf_token;
    $fc_content_switch .= '</form>';
    $i++;
}


$fc_content_switch .= '</div>';
$fc_content_switch .= '</div>';

?>