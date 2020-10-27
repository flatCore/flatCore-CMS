<?php
//error_reporting(E_ALL ^E_NOTICE);

$time_string_now = time();
$display_mode = 'list_posts';

/* defaults */
$posts_start = 0;
$posts_limit = $fc_prefs['prefs_posts_entries_per_page'];
$posts_order = 'id';
$posts_direction = 'DESC';
$posts_filter = array();

$str_status = '1';
if($_SESSION['user_class'] == 'administrator') {
	$str_status = '1-2';
}

$posts_filter['languages'] = $page_contents['page_language'];
$posts_filter['types'] = $page_contents['page_posts_types'];
$posts_filter['status'] = $str_status;
$posts_filter['categories'] = $page_contents['page_posts_categories'];


if(substr("$mod_slug", -5) == '.html') {
	$get_post_id = (int) basename(end(explode("-", $mod_slug)));
	$display_mode = 'show_post';	
}

$all_categories = fc_get_categories();
$array_mod_slug = explode("/", $mod_slug);


foreach($all_categories as $cats) {	
	if($cats['cat_name_clean'] == $array_mod_slug[0]) {
		// show only posts from this category
		$posts_filter['categories'] = $cats['cat_id'];
		
		if($array_mod_slug[1] == 'p' && is_numeric($array_mod_slug[2])) {
			$posts_start = $array_mod_slug[2];
		}
		
	}
}

/* pagination f.e. /p/2/ or /p/3/ .... */
if($array_mod_slug[0] == 'p' && is_numeric($array_mod_slug[1])) {
	$posts_start = $array_mod_slug[1];
}


switch ($display_mode) {
    case "list_posts":
        include 'posts-list.php';
        break;
    case "show_post":
        include 'posts-display.php';
        break;
   default:
        include 'posts-list.php';
}


?>