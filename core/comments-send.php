<?php

session_start();

use Medoo\Medoo;


define("FC_SOURCE", "frontend");
require '../config.php';

require FC_CORE_DIR.'/database.php';

$prefs_comments_mode = $db_content->get("fc_preferences", "prefs_comments_mode", [
	"prefs_status" => "active"
]);

if($_POST['input_name'] != '' && $_POST['input_mail'] != '' && $_POST['input_comment'] != '') {
	
	foreach($_REQUEST as $key => $val) {
		$$key = htmlspecialchars(strip_tags($val)); 
	}
	
	$type = 'p';
	$comment_status = 2;
	
	if($prefs_comments_mode == 1) {
		$comment_status = 1;
	}
	
	$comment_time = time();
	
	if(is_numeric($_POST['page_id'])) {
		$type = 'p';
		$relation_id = (int) $_POST['page_id'];
	}
	
	if(is_numeric($_POST['post_id'])) {
		$type = 'b';
		$relation_id = (int) $_POST['post_id'];
	}

	if(strlen($input_name) > 30) {
		$input_name = substr($input_name, 0,30);
	}
	
	if(strlen($input_mail) > 50) {
		$input_mail = substr($input_mail, 0,50);
	}
		
	if(strlen($input_comment) > 500) {
		$input_comment = substr($input_comment, 0,500);
	}
	
	
	$input_comment = nl2br($input_comment);
	
	
	$db_content->insert("fc_comments", [
		"comment_type" =>  $type,
		"comment_relation_id" =>  $relation_id,
		"comment_status" =>  $comment_status,
		"comment_time" =>  $comment_time,
		"comment_author" =>  $input_name,
		"comment_author_mail" =>  $input_mail,
		"comment_text" =>  $input_comment
	]);
	
	$insert_id=$db_content->id();
	
	if($insert_id > 0) {
		echo '<div class="alert alert-success">{msg_comment_save}</div>';
	} else {
		echo '<div class="alert alert-danger">{msg_comment_error}</div>';
	}

	
}



?>