<?php
//error_reporting(E_ALL ^E_NOTICE);

$time_string_now = time();
$display_mode = 'list_posts';

/* defaults */
$posts_start = 0;
$posts_limit = (int) $fc_prefs['prefs_posts_entries_per_page'];
if($posts_limit == '') {
	$posts_limit = 10;
}
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

$tpl_nav_cats = fc_load_posts_tpl($fc_template,'nav-categories.tpl');
$tpl_nav_cats_item = fc_load_posts_tpl($fc_template,'nav-categories-item.tpl');

$all_categories = fc_get_categories();
$array_mod_slug = explode("/", $mod_slug);

$nav_categories_list = '';

foreach($all_categories as $cats) {
	
	$this_nav_cat_item = $tpl_nav_cats_item;
	$show_category_title = $cats['cat_description'];
	$show_category_name = $cats['cat_name'];
	$this_nav_cat_item = str_replace('{nav_item_title}', $show_category_title, $this_nav_cat_item);
	$this_nav_cat_item = str_replace('{nav_item_name}', $show_category_name, $this_nav_cat_item);
	$cat_link = '/'.$fct_slug.$cats['cat_name_clean'].'/';
	$this_nav_cat_item = str_replace('{nav_item_link}', $cat_link, $this_nav_cat_item);

	/* show only categories that match the language */
	if($page_contents['page_language'] !== $cats['cat_lang']) {
		continue;
	}
	
	if($cats['cat_name_clean'] == $array_mod_slug[0]) {
		// show only posts from this category
		$posts_filter['categories'] = $cats['cat_id'];
		$display_mode = 'list_posts_category';
		$selected_category_title = $cats['cat_name'];
		$this_nav_cat_item = str_replace('{nav_item_class}', 'active', $this_nav_cat_item);
		
		if($array_mod_slug[1] == 'p') {
			
			if(is_numeric($array_mod_slug[2])) {
				$posts_start = $array_mod_slug[2];
			} else {
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: /$fct_slug");
				header("Connection: close");
			}				
		}
	} else {
		
		$this_nav_cat_item = str_replace('{nav_item_class}', '', $this_nav_cat_item);
		
	}
	$nav_categories_list .= $this_nav_cat_item;
}

$tpl_nav_cats = str_replace('{nav_categories_items}', $nav_categories_list, $tpl_nav_cats);


/* pagination f.e. /p/2/ or /p/3/ .... */
if($array_mod_slug[0] == 'p') {
	
	if(is_numeric($array_mod_slug[1])) {
		$posts_start = $array_mod_slug[1];
	} else {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: /$fct_slug");
		header("Connection: close");	}
}

if($page_contents['page_type_of_use'] == 'display_post' AND $get_post_id == '') {
	/* we are on the post display page but we have no post id
	 * get a blog page and redirect
	 */
	
	$target_page = $db_content->get("fc_pages", "page_permalink", [
		"AND" => [
			"page_posts_categories[!]" => "",
			"page_language" => $page_contents['page_language']
		]
	]);

	
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: /$target_page");
	header("Connection: close");
}

/* redirect to external link */
if(isset($_GET['goto'])) {
	
	$get_link_by_id = (int) $_GET['goto'];
	$target_post = $db_posts->get("fc_posts", ["post_link","post_link_hits"], [
			"post_id" => $get_link_by_id
	]);
	
	$target_url = $target_post['post_link'];
	$upd_counter = $target_post['post_link_hits']+1;
	
	$update_counter = $db_posts->update("fc_posts", [
		"post_link_hits" => $upd_counter
	],[
		"post_id" => $get_link_by_id
	]);	
	
	$redirect = $target_url;		
	header("Location: $redirect");
	exit;
	
}

/* start post_attachment download */
if(isset($_POST['post_attachment'])) {
	
	if($_POST['post_attachment_external'] != '') {
		
		// external downloads
		
		$target_file = $db_posts->get("fc_posts", "*", [
			"post_file_attachment_external" => $_POST['post_attachment_external']
		]);
		
		$counter = $target_file['post_file_attachment_hits']+1;

		$update_file = $db_posts->update("fc_posts", [
			"post_file_attachment_hits" => $counter
		],[
			"post_file_attachment_external" => $_POST['post_attachment_external']
		]);
		
		$redirect = $_POST['post_attachment_external'];		
		header("Location: $redirect");
		exit;
		
	} else {
		
		// file downloads fron /content/files/
		
		$post_attachment = basename($_POST['post_attachment']);
		$get_target_file = '../content/files/'.$post_attachment;
		
		$target_file = $db_posts->get("fc_posts", "*", [
			"post_file_attachment" => $get_target_file
		]);
		
		$counter = $target_file['post_file_attachment_hits']+1;
		
		$update_file = $db_posts->update("fc_posts", [
			"post_file_attachment_hits" => $counter
		],[
			"post_file_attachment" => $get_target_file
		]);
		
		/* we take the filepath from the database, so we have no trouble if someone trying to inject evil filepath */
		$download_file = str_replace('../content/','./content/',$target_file['post_file_attachment']);
	
		if(is_file($download_file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: ' . mime_content_type($download_file));
			header('Content-Disposition: attachment; filename="'.basename($download_file).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($download_file));
			readfile($download_file);
			exit;
		}	
	}
}


switch ($display_mode) {
    case "list_posts":
        include 'posts-list.php';
        break;
    case "show_post":
        include 'posts-display.php';
        break;
    case "list_posts_category":
        include 'posts-list.php';
        break;
   default:
        include 'posts-list.php';
}


?>