<?php
session_start();
error_reporting(0);

define('FC_SOURCE', 'frontend');
include_once '../config.php';
include_once '../database.php';
include_once '../global/functions.posts.php';

$time = time();

if($_POST['val']) {
	
	/* check who is voting */

	if($_SESSION['user_id'] != '') {
		$sender_id = $_SESSION['user_id'];
		$sender_name = $_SESSION['user_nick'];
	} else {
		// anonymous voter
		
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $ip = $_SERVER['REMOTE_ADDR'];
		}
		
		$ip = md5($ip);
		$sender_id = '';
		$sender_name = $ip;		
	}


	$event_data = explode('-',$_POST['val']);
	
	/* post id */
	$event_relation_id = (int) $event_data[1];
	$sender_type = 'evc';
	
	
	$db_content->insert("fc_comments", [
		"comment_relation_id" => $event_relation_id,
		"comment_type" => $sender_type,
		"comment_time" => $time,
		"comment_author" => $sender_name,
		"comment_author_id" => $sender_id
	]);


	/* get the new counters */
	
	$nbr_of_confirmations = fc_get_event_confirmation_data($event_relation_id);

	echo json_encode($nbr_of_confirmations);

}
?>