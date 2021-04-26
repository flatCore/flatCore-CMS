<?php
	
/**
 * get posts
 */
	
function fc_get_post_entries($start=0,$limit=10,$filter) {
	
	global $db_posts;
	global $db_type;
	global $time_string_start;
	global $time_string_end;
	global $time_string_now;
	global $fc_preferences;
	global $fc_labels;
	
	if(FC_SOURCE == 'frontend') {
		global $fc_prefs;
	}
		
	$limit_str = 'LIMIT '. (int) $start;
	
	if($limit == 'all') {
		$limit_str = '';
	} else {
		$limit_str .= ', '. (int) $limit;
	}
	
	
	/**
	 * order and direction
	 * we ignore $order and $direction
	 */

	$order = "ORDER BY post_fixed ASC, sortdate DESC, post_priority DESC, post_id DESC";
	
	if(FC_SOURCE == 'frontend') {
		if($filter['types'] == 'e') {
			$order = 'ORDER BY post_fixed DESC, sortdate_events ASC, post_priority DESC';
		}
	}
	
	if($direction == 'ASC') {
		$direction = 'ASC';
	} else {
		$direction = 'DESC';
	}
		
	/* set filters */
	$sql_filter_start = 'WHERE post_id IS NOT NULL ';
	
	/* language filter */
	$sql_lang_filter = "post_lang IS NULL OR ";
	$lang = explode('-', $filter['languages']);
	foreach($lang as $l) {
		if($l != '') {
			$sql_lang_filter .= "(post_lang LIKE '%$l%') OR ";
		}		
	}
	$sql_lang_filter = substr("$sql_lang_filter", 0, -3); // cut the last ' OR'
	
	
	/* type filter */
	$sql_types_filter = "post_type IS NULL OR ";
	$types = explode('-', $filter['types']);
	foreach($types as $t) {
		if($t != '') {
			$sql_types_filter .= "(post_type LIKE '%$t%') OR ";
		}		
	}
	$sql_types_filter = substr("$sql_types_filter", 0, -3); // cut the last ' OR'

	/* status filter */
	$sql_status_filter = "post_status IS NULL OR ";
	$status = explode('-', $filter['status']);
	foreach($status as $s) {
		if($s != '') {
			$sql_status_filter .= "(post_status LIKE '%$s%') OR ";
		}		
	}
	$sql_status_filter = substr("$sql_status_filter", 0, -3); // cut the last ' OR'
	
	
	/* category filter */
	if($filter['categories'] == 'all' OR $filter['categories'] == '') {
		$sql_cat_filter = '';
	} else {
		
		$cats = explode(',', $filter['categories']);
		foreach($cats as $c) {
			if($c != '') {
				$sql_cat_filter .= "(post_categories LIKE '%$c%') OR ";
			}		
		}
		$sql_cat_filter = substr("$sql_cat_filter", 0, -3); // cut the last ' OR'
	}
	
	/* label filter */
	if($filter['labels'] == 'all' OR $filter['labels'] == '') {
		$sql_label_filter = '';
	} else {

		$checked_labels_array = explode('-', $filter['labels']);
		
		for($i=0;$i<count($fc_labels);$i++) {
			$label = $fc_labels[$i]['label_id'];
			if(in_array($label, $checked_labels_array)) {
				$sql_label_filter .= "post_labels LIKE '%,$label,%' OR post_labels LIKE '%,$label' OR post_labels LIKE '$label,%' OR post_labels = '$label' OR ";
			}
		}		
		$sql_label_filter = substr("$sql_label_filter", 0, -3); // cut the last ' OR'
	}

	$sql_filter = $sql_filter_start;
	
	if($sql_lang_filter != "") {
		$sql_filter .= " AND ($sql_lang_filter) ";
	}
	if($sql_types_filter != "") {
		$sql_filter .= " AND ($sql_types_filter) ";
	}
	if($sql_status_filter != "") {
		$sql_filter .= " AND ($sql_status_filter) ";
	}
	if($sql_cat_filter != "") {
		$sql_filter .= " AND ($sql_cat_filter) ";
	}
	if($sql_label_filter != "") {
		$sql_filter .= " AND ($sql_label_filter) ";
	}
	
	if(FC_SOURCE == 'frontend') {
		$sql_filter .= "AND post_releasedate <= '$time_string_now' ";
		
		if($filter['types'] == 'e') {
			// we show events longer (from event end)
			$time_hide_events = $time_string_now-$fc_prefs['prefs_posts_event_time_offset'];
			$sql_filter .= "AND post_event_enddate >= '$time_hide_events' ";
		}
		
	}

	if($time_string_start != '') {
		$sql_filter .= "AND post_releasedate >= '$time_string_start' AND post_releasedate <= '$time_string_end' AND post_releasedate < '$time_string_now' ";
	}
	
	if($db_type == 'sqlite') {
		$sql = "SELECT *, strftime('%Y-%m-%d',datetime(post_releasedate, 'unixepoch')) as 'sortdate', strftime('%Y-%m-%d',datetime(post_event_startdate, 'unixepoch')) as 'sortdate_events' FROM fc_posts $sql_filter $order $limit_str";
	} else {
		$sql = "SELECT *, FROM_UNIXTIME(post_releasedate,'%Y-%m-%d') as 'sortdate', FROM_UNIXTIME(post_event_startdate,'%Y-%m-%d') as 'sortdate_events' FROM fc_posts $sql_filter $order $limit_str";
	}

	$entries = $db_posts->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			
	$sql_cnt = "SELECT count(*) AS 'A', (SELECT count(*) FROM fc_posts $sql_filter) AS 'F'";
	$stat = $db_posts->query("$sql_cnt")->fetch(PDO::FETCH_ASSOC);

	/* number of posts that match the filter */
	$entries[0]['cnt_posts'] = $stat['F'];
	return $entries;
	
}


/**
 * count all entries
 */
 
function fc_cnt_post_entries() {
	
	global $db_posts;
	
	$sql = "SELECT count(*) AS 'All',
		(SELECT count(*) FROM fc_posts WHERE post_status LIKE '%1%' ) AS 'Public',
		(SELECT count(*) FROM fc_posts WHERE post_status LIKE '%2%' ) AS 'Draft',
		(SELECT count(*) FROM fc_posts WHERE post_type LIKE '%m%' ) AS 'Message',
		(SELECT count(*) FROM fc_posts WHERE post_type LIKE '%l%' ) AS 'Link',
		(SELECT count(*) FROM fc_posts WHERE post_type LIKE '%v%' ) AS 'Video',
		(SELECT count(*) FROM fc_posts WHERE post_type LIKE '%i%' ) AS 'Image',
		(SELECT count(*) FROM fc_posts WHERE post_type LIKE '%e%' ) AS 'Event',
		(SELECT count(*) FROM fc_posts WHERE post_type LIKE '%p%' ) AS 'Product',
		(SELECT count(*) FROM fc_posts WHERE post_type LIKE '%f%' ) AS 'File'
	FROM fc_posts
	";
	
	$stats = $db_posts->query($sql)->fetch(PDO::FETCH_ASSOC);

	return $stats;
}


function fc_get_post_data($id) {
	
	global $db_posts;
	
	$post_data = $db_posts->get("fc_posts","*", [
		"post_id" => $id
	]);
	
	return $post_data;
}


/**
 * print currency
 * aka 9,99
 *
 */
 
function fc_post_print_currency($number) {

	$number = number_format($number, 2, ',', '.');
	
	$comma_pos = stripos($number, ",");
	$article_price_big = substr("$number", 0, $comma_pos);
	$article_price_small = substr("$number", -2);
	
	$article_price_string = "<span class='price-predecimal'>$article_price_big</span><span class='price-decimal'>,$article_price_small</span>";
		
	return $article_price_string;

}





function fc_set_pagination_query($display_mode,$start) {
	
	global $fct_slug;
	global $pb_posts_filter;
	global $pub_preferences;
	global $array_mod_slug;
	
	if($display_mode == 'list_posts_category') {
		$pagination_link = "/$fct_slug".$array_mod_slug[0].'/p/'."$start/";
	} else if($display_mode == 'list_archive_year') {
		$pagination_link = "/$fct_slug".$array_mod_slug[0].'/p/'."$start/";
	} else if($display_mode == 'list_archive_month') {
		$pagination_link = "/$fct_slug".$array_mod_slug[0].'/'.$array_mod_slug[1].'/p/'."$start/";
	} else if($display_mode == 'list_archive_day') {
		$pagination_link = "/$fct_slug".$array_mod_slug[0].'/'.$array_mod_slug[1].'/'.$array_mod_slug[2].'/p/'."$start/";
	} else {
		$pagination_link = "/$fct_slug".'p/'."$start/";
	}

	
	return $pagination_link;
}

/**
 * check if the template $tpl_dir has the posts tpl file
 * if not, load files from the /default/templates/posts/ directory
 * return tpl file contents (string)
 */

function fc_load_posts_tpl($tpl_dir,$tpl_file) {
	
	$check_template = 'styles/'.basename($tpl_dir).'/templates/posts/'.$tpl_file;
	
	if(is_file($check_template)) {
		$contents = file_get_contents($check_template);
	} else {
		$fallback_tpl = 'styles/default/templates/posts/'.$tpl_file;
		$contents = file_get_contents($fallback_tpl);
	}
	
	return $contents;
}

/**
 * check if the template $tpl_dir has the comments tpl file
 * if not, load files from the /default/templates/comments/ directory
 * return tpl file contents (string)
 */

function fc_load_comments_tpl($tpl_dir,$tpl_file) {
	
	$check_template = 'styles/'.basename($tpl_dir).'/templates/comments/'.$tpl_file;
	
	if(is_file($check_template)) {
		$contents = file_get_contents($check_template);
	} else {
		$fallback_tpl = 'styles/default/templates/comments/'.$tpl_file;
		$contents = file_get_contents($fallback_tpl);
	}
	
	return $contents;
}


/**
 * increase the hits counter
 */
 
function fc_increase_posts_hits($post_id) {
	
	global $db_posts;
	
	$post_data_hits = $db_posts->get("fc_posts","post_hits", [
		"post_id" => $post_id
	]);
	
	$post_data_hits = $post_data_hits+1;

	$update = $db_posts->update("fc_posts", [
		"post_hits" => $post_data_hits
	],[
		"post_id" => $post_id
	]);
		
}

/**
 * get voting data for posts or comments
 * return array $votes['upv'] = x / $votes['dnv'] = x
 */

function fc_get_voting_data($type,$id) {
	
	global $db_content;
	
	if($type == 'post') {
		
		$count_upv = $db_content->count("fc_comments", [
			"AND" => [
				"comment_type" => "upv",
				"comment_relation_id" => $id
			]
		]);
		$count_dnv = $db_content->count("fc_comments", [
			"AND" => [
				"comment_type" => "dnv",
				"comment_relation_id" => $id
			]
		]);
		
		$votes = array('upv' => $count_upv, 'dnv' => $count_dnv);
		
		return $votes;
	
	}
}

/**
 * get data for events guestlist
 * return number of commitments
 */
 
function fc_get_event_confirmation_data($id){
	
	global $db_content;
	$count_evc = $db_content->count("fc_comments", [
		"AND" => [
			"comment_type" => "evc",
			"comment_relation_id" => $id
		]
	]);
	
	$event_data = array('evc' => $count_evc);
	
	return $event_data;
	
}

?>