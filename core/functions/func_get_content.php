<?php

/*
Get contents of a current page (by $p)
*/

function get_content($page_id) {

global $fc_db_content;

$dbh = new PDO("sqlite:$fc_db_content");

$sql = "SELECT * FROM fc_pages WHERE page_id = '$page_id'" ;
	
	
$result = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);


return $result;


}


/*
Get contents of a current page (by $page_sort and Language)
*/

function get_content_by_pagesort($page_sort) {

global $fc_db_content;
global $languagePack;

$dbh = new PDO("sqlite:$fc_db_content");

$sql = "SELECT * FROM fc_pages WHERE page_sort = '$page_sort' AND page_language = '$languagePack' " ;
	
	
$result = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);


return $result;


}


/*
Get contents of the a page (by $page_permalink)
*/

function get_content_by_permalink($page_permalink) {

global $fc_db_content;
global $languagePack;

$dbh = new PDO("sqlite:$fc_db_content");

$sql = "SELECT * FROM fc_pages WHERE page_permalink = '$page_permalink' " ;
	
	
$result = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);


return $result;


}




/* Get contents for preview (by $preview) */

function get_content_for_preview($page_id) {

global $fc_db_content;

$dbh = new PDO("sqlite:$fc_db_content");

$sql = "SELECT * FROM fc_pages_cache WHERE page_id_original = '$page_id' ORDER BY page_id DESC" ;
	
	
$result = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);

$result[page_title] = "PREVIEW: $result[page_title]";

return $result;


}


?>