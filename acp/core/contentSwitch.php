<?php
require("core/access.php");

/**
 * we can choose between different content files
 * store your files as array in your own config.php
 * example: $fc_content_files array (	array (	'file'	=> 'content.sqlite3',	'desc'	=> 'Standard SQLite Database' ), array ( ... ) );
 */

if(!isset($_SESSION['fc_db_content'])) {
	$_SESSION['fc_db_content'] = FC_CONTENT_DIR . '/SQLite/'.$fc_content_files[0]['file'];
}

if(isset($_GET['switchContent'])) {
	$switchContentId = (int) $_GET['switchContent'];
	$switchContentFile = FC_CONTENT_DIR . '/SQLite/'.$fc_content_files[$switchContentId]['file'];
	
	if(is_file('../'.$switchContentFile)) {
		$_SESSION['fc_db_content'] = $switchContentFile;
	}
}

if(isset($_SESSION['fc_db_content'])) {
	if(is_file('../'.$_SESSION['fc_db_content'])) {
		$fc_db_content = $_SESSION['fc_db_content'];
	}
}


$fc_content_switch = '<a href="#" class="tooltip_bottom" data-toggle="collapse" data-target="#contentSwitchContainer"><span class="glyphicon glyphicon-collapse-down"></span> '.$fc_db_content.'</a>';

$fc_content_switch .= '<div id="contentSwitchContainer" class="collapse" style="height:auto;">';
$fc_content_switch .= '<div>';

$i=0;
foreach($fc_content_files as $files) {
	$fc_content_switch .= '<a class="btn btn-light btn-xs btn-block" href="acp.php?tn='.$_GET['tn'].'&amp;switchContent='.$i.'" title="'.$files['desc'].'">'.$files['file'].'</a>';
	$i++;
}


$fc_content_switch .= '</div>';
$fc_content_switch .= '</div>';

?>