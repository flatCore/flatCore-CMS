<?php

/**
 * Get contents of a current page (by $p)
 * @return array
 */

function get_content($page_id) {

	global $fc_db_content;
	
	$dbh = new PDO("sqlite:$fc_db_content");
	$sql = "SELECT * FROM fc_pages WHERE page_id = '$page_id'" ;
	$result = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	return $result;
}


/**
 * Get contents of a current page (by $page_sort and Language)
 * @return array
 */

function get_content_by_pagesort($page_sort) {

	global $fc_db_content;
	global $languagePack;
	
	$dbh = new PDO("sqlite:$fc_db_content");
	$sql = "SELECT * FROM fc_pages WHERE page_sort = '$page_sort' AND page_language = '$languagePack' " ;
	$result = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	return $result;
}


/**
 * Get contents of the a page (by $page_permalink)
 * @return array
 */

function get_content_by_permalink($page_permalink) {

	global $fc_db_content;
	global $languagePack;
	
	$dbh = new PDO("sqlite:$fc_db_content");
	$sql = "SELECT * FROM fc_pages WHERE page_permalink = '$page_permalink' " ;
	$result = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	return $result;
}




/**
 * Get contents from fc_pages_cache
 * for preview (by $preview)
 * @return array
 */

function get_content_for_preview($page_id) {

	global $fc_db_content;
	
	$dbh = new PDO("sqlite:$fc_db_content");
	$sql = "SELECT * FROM fc_pages_cache WHERE page_id_original = '$page_id' ORDER BY page_id DESC" ;
	$result = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
	$result[page_title] = "PREVIEW: $result[page_title]";
	$dbh = null;
	return $result;
}


/**
 * Get data from all pages you need
 * to build navigation or sitemap
 *
 * @return array
 */

function get_navigation_data() {

	global $fc_db_content;
	global $languagePack;
	
	$dbh = new PDO("sqlite:$fc_db_content");
	$sql = "SELECT page_id, page_language, page_linkname, page_permalink, page_title, page_sort, page_status
				  FROM fc_pages
				  WHERE page_status != 'draft' AND page_language = '$languagePack'
				  ORDER BY page_sort";
	 
	$result = $dbh->query($sql)->fetchAll();
	$dbh = null;
	
	return $result;
}


?>