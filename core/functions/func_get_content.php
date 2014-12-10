<?php


/**
 * get contents of the current page (default by (int) $p)
 * get contents for navigation
 * get preferences
 *
 * @return array
 */

function get_content($page, $mode = 'p') {

	global $fc_db_content;
	global $languagePack;
	
	$page_contents_sql = "SELECT * FROM fc_pages WHERE page_id = '$page'" ;	
	if($mode == 'page_sort') {
		$page_contents_sql = "SELECT * FROM fc_pages WHERE page_sort = '$page' AND page_language = '$languagePack' " ;
	}
	if($mode == 'permalink') {
		$page_contents_sql = "SELECT * FROM fc_pages WHERE page_permalink = '$page' " ;
	}
	if($mode == 'preview') {
		$page_contents_sql = "SELECT * FROM fc_pages_cache WHERE page_id_original = '$page' ORDER BY page_id DESC" ;
	}
					  
	$prefs_sql = "SELECT * FROM fc_preferences WHERE prefs_status = 'active' ";
	
	$dbh = new PDO("sqlite:$fc_db_content");
	$page_contents = $dbh->query($page_contents_sql)->fetch(PDO::FETCH_ASSOC);
	$prefs = $dbh->query($prefs_sql)->fetch(PDO::FETCH_ASSOC);

	if($page_contents['page_language'] == '') {
		$page_contents['page_language'] = $languagePack;
	}

	if($_SESSION['user_class'] != 'administrator') {
		$nav_sql_filter = "WHERE page_status != 'draft' AND page_status != 'ghost' AND page_language = '$page_contents[page_language]'";
	} else  {
		$nav_sql_filter = "WHERE page_language = '$page_contents[page_language]'";
	}
	$nav_sql = "SELECT page_id, page_language, page_linkname, page_permalink, page_title, page_sort, page_status
			  FROM fc_pages
			  $nav_sql_filter
			  ORDER BY page_sort";
	
	$fc_nav = $dbh->query($nav_sql)->fetchAll();
	
	$dbh = null;
	
	$contents = array($page_contents,$fc_nav,$prefs);
	
	return $contents;
}

/**
 * check if given url is a shortlink
 * if applicable, immediately redirect to page permalink
 */

function fc_check_shortlinks($shortlink) {
		
	global $fc_db_content;
	
	$page_sql = "SELECT page_permalink, page_permalink_short_cnt FROM fc_pages WHERE page_permalink_short = '$shortlink' " ;
	$dbh = new PDO("sqlite:$fc_db_content");
	$page = $dbh->query($page_sql)->fetch(PDO::FETCH_ASSOC);
	
	/* increase page_permalink_short_cnt
		 redirect to page_permalink	*/
		 
	if($page['page_permalink'] != '') {
		$page_permalink_short_cnt = $page['page_permalink_short_cnt'] +1;
		$dbh->exec("UPDATE fc_pages SET page_permalink_short_cnt=$page_permalink_short_cnt WHERE page_permalink_short='$shortlink'");
		
		$redirect = 'http://'.$_SERVER['HTTP_HOST'].'/'.$page['page_permalink'];
		
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: $redirect"); 	
		exit;
	}
	$dbh = null;
	
}

?>