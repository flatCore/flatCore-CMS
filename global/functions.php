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

?>