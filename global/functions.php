<?php

/**
 * global functions
 * are used in frontend and backend
 * 
 */

include_once 'functions.posts.php';


/**
 * get all categories
 * order by cat_sort
 */

function fc_get_categories() {
	global $db_content;
	$categories = $db_content->select("fc_categories", "*",
	[
		"ORDER" => ["cat_sort" => "DESC"]
	]);	
	return $categories;
}


/**
 * get all comments
 * $filter = array()
 * $filter['type'] -> p|b|c
 */

function fc_get_comments($start=0,$limit=10,$filter) {
	
	global $db_content;
	
	$comments = $db_content->select("fc_comments", "*",[
			"AND" => [
			"comment_type" => $filter['type'],
			"comment_relation_id" => $filter['relation_id']
		],
			"LIMIT" => [$start,$limit],
			"ORDER" => ["comment_time" => "DESC"]
		
	]);
	
	
	return $comments;
	
	
}

?>