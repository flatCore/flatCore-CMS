<?php

session_start();

use Medoo\Medoo;


define("FC_SOURCE", "frontend");
require '../config.php';

require FC_CORE_DIR.'/database.php';

if($_POST['input_name'] != '' && $_POST['input_mail'] != '' && $_POST['input_comment'] != '') {
	
	foreach($_REQUEST as $key => $val) {
		$$key = htmlspecialchars($val); 
	}
	
	$type = 'p';
	$comment_status = 2;
	$comment_time = time();
	
	if(is_numeric($_POST['page_id'])) {
		$type = 'p';
		$relation_id = (int) $_POST['page_id'];
	}
	
	if(is_numeric($_POST['post_id'])) {
		$type = 'b';
		$relation_id = (int) $_POST['post_id'];
	}
	
	
	
	$db_content->insert("fc_comments", [
		"comment_type" =>  $type,
		"comment_relation_id" =>  $relation_id,
		"comment_status" =>  $comment_status,
		"comment_time" =>  $comment_time,
		"comment_author" =>  $input_name,
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