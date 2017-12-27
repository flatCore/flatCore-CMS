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
	
	
	$dbh = new PDO("sqlite:$fc_db_content");

	if($mode == 'permalink') {
		$page_contents_sql = "SELECT * FROM fc_pages WHERE page_permalink = :page";
		$sth = $dbh->prepare($page_contents_sql);
		$sth->bindParam(':page', $page, PDO::PARAM_STR);
	} elseif ($mode == 'page_sort') {
		$page_contents_sql = "SELECT * FROM fc_pages WHERE page_sort = :page AND page_language = :languagePack";
		$sth = $dbh->prepare($page_contents_sql);
		$sth->bindParam(':page', $page, PDO::PARAM_STR);
		$sth->bindParam(':languagePack', $languagePack, PDO::PARAM_STR);
	} elseif ($mode == 'preview') {
		$page_contents_sql = "SELECT * FROM fc_pages_cache WHERE page_id_original = :page ORDER BY page_id DESC";
		$sth = $dbh->prepare($page_contents_sql);
		$sth->bindParam(':page', $page, PDO::PARAM_INT);
	} else {
		$page_contents_sql = "SELECT * FROM fc_pages WHERE page_id = :page";
		$sth = $dbh->prepare($page_contents_sql);
		$sth->bindParam(':page', $page, PDO::PARAM_INT);		
	}
	

	$sth->execute();
	$page_contents = $sth->fetch(PDO::FETCH_ASSOC);

	$prefs_sql = "SELECT * FROM fc_preferences WHERE prefs_status = 'active'";		
	
	$prefs = $dbh->query($prefs_sql)->fetch(PDO::FETCH_ASSOC);

	if($page_contents['page_language'] == '') {
		$page_contents['page_language'] = $languagePack;
	}

	if($_SESSION['user_class'] != 'administrator') {
		$nav_sql_filter = "WHERE page_status != 'draft' AND page_status != 'ghost' AND page_language = :language";
	} else  {
		$nav_sql_filter = "WHERE page_language = :language";
	}
	
	$nav_sql = "SELECT page_id, page_hash, page_language, page_linkname, page_permalink, page_title, page_sort, page_status
			  FROM fc_pages $nav_sql_filter ORDER BY page_sort";
	
	$sth = $dbh->prepare($nav_sql);
	$sth->bindParam(':language', $page_contents['page_language'], PDO::PARAM_STR);
	$sth->execute();
	
	$fc_nav = $sth->fetchAll();
	
	$dbh = null;
	
	$fc_nav = fc_array_multisort($fc_nav, 'lang', SORT_ASC, 'page_sort', SORT_ASC, SORT_NATURAL);
	
	$contents = array($page_contents,$fc_nav,$prefs);
	
	return $contents;
}

/**
 * check if given url is a shortlink
 * if applicable, immediately redirect to page permalink
 */

function fc_check_shortlinks($shortlink) {
		
	global $fc_db_content;
	
	$dbh = new PDO("sqlite:$fc_db_content");
	
	$page_sql = "SELECT page_permalink, page_permalink_short_cnt FROM fc_pages WHERE page_permalink_short = :shortlink";
	$sth = $dbh->prepare($page_sql);
	$sth->bindParam(':shortlink', $shortlink, PDO::PARAM_STR);
	$sth->execute();
	$page = $sth->fetch(PDO::FETCH_ASSOC);
	
	/* increase page_permalink_short_cnt
		 redirect to page_permalink	*/
		 
	if($page['page_permalink'] != '') {
				
		$count_sql = "UPDATE fc_pages SET page_permalink_short_cnt = :page_permalink_short_cnt WHERE page_permalink_short= :shortlink";
		$sth = $dbh->prepare($count_sql);
		$page_permalink_short_cnt = $page['page_permalink_short_cnt'] +1;
		$sth->bindParam(':page_permalink_short_cnt', $page_permalink_short_cnt, PDO::PARAM_STR);
		$sth->bindParam(':shortlink', $shortlink, PDO::PARAM_STR);
		$sth->execute();
				
		$redirect = '/'.$page['page_permalink'];
		header("location: $redirect",TRUE,301);	
		exit;
	}
	$dbh = null;
	
}

/**
 * check if given url is a funnel uri
 * if applicable, immediately redirect to page permalink
 */

function fc_check_funnel_uri($uri) {
		
	global $fc_db_content;
	
	$dbh = new PDO("sqlite:$fc_db_content");
	
	$page_sql = "SELECT page_permalink, page_funnel_uri FROM fc_pages WHERE page_funnel_uri LIKE :uri";
	$sth = $dbh->prepare($page_sql);
	$sth->bindValue(':uri', "%$uri%", PDO::PARAM_STR);
	$sth->execute();
	$page = $sth->fetch(PDO::FETCH_ASSOC);
	
		$page_funnel_uri = explode(',', $page['page_funnel_uri']);
		foreach($page_funnel_uri as $u) {

			if($u == $uri) {
				$redirect = '/'.$page['page_permalink'];
				header("location: $redirect",TRUE,301);
				exit;
			}
			
		}
		
	
	
}

?>