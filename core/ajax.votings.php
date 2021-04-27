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
		$voter_id = $_SESSION['user_id'];
		$voter_name = $_SESSION['user_nick'];
	} else {
		// anonymous voter
		$voter_id = '';
		$voter_name = fc_generate_anonymous_voter();		
	}

	$voting_data = explode('-',$_POST['val']);
	
	/* post id */
	$vote_relation_id = (int) $voting_data[2];
	
	$check_voter = fc_check_voter_legitimacy($vote_relation_id,$voter_name);
	
	if($check_voter == false) {
		exit();
	}
	
	if($voting_data[0] == 'dn') {
		$vote_type = 'dnv'; // down vote
	} else {
		$vote_type = 'upv'; // up vote
	}
	
	
	$db_content->insert("fc_comments", [
		"comment_relation_id" => $vote_relation_id,
		"comment_type" => $vote_type,
		"comment_time" => $time,
		"comment_author" => $voter_name,
		"comment_author_id" => $voter_id
	]);


	/* get the new counters */
	
	$votes = fc_get_voting_data('post',$vote_relation_id);

	echo json_encode($votes);

}
?>