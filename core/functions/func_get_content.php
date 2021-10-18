<?php

/**
 * get contents of the current page (default by (int) $p)
 * get contents for navigation
 *
 * @return array
 */

function fc_get_content($page, $mode = 'p') {

	global $db_content;
	global $languagePack;

	if($mode == 'permalink') {
		
		$page_contents = $db_content->get("fc_pages", "*", [
			"page_permalink" => $page
		]);
		
	} elseif ($mode == 'type_of_use') {
			
		$page_contents = $db_content->get("fc_pages", "*", [
				"AND" => [
				"page_type_of_use" => $page,
				"page_language" => "$languagePack"
				]
		]);	
	
	
	} elseif ($mode == 'page_sort') {
			
		$page_contents = $db_content->get("fc_pages", "*", [
				"AND" => [
				"page_sort" => "$page",
				"page_language" => "$languagePack"
				]
		]);		
	
	
	} elseif ($mode == 'preview') {
	
		$page_contents = $db_content->get("fc_pages_cache", "*", [
				"AND" => [
				"page_id_original" => "$page",
				"page_language" => "$languagePack"
			],
				"ORDER" => ["page_id" => "DESC"]
			]);			
	
	} else {
		
	
		$page_contents = $db_content->get("fc_pages", "*", [
			"page_id" => $page
		]);
	
	}
				

	if($page_contents['page_language'] == '') {
		$page_contents['page_language'] = $languagePack;
	} else {
		$languagePack = $page_contents['page_language'];
	}

	if(empty($_SESSION['user_class']) && $_SESSION['user_class'] != 'administrator') {

		$fc_nav = $db_content->select("fc_pages", ['page_id', 'page_classes', 'page_hash', 'page_language', 'page_linkname', 'page_permalink', 'page_target', 'page_title', 'page_sort', 'page_status'], [
				"AND" => [
					"OR" => [
						"page_status[!]" => ["draft","ghost"]
				],
				"page_language" => $languagePack
			],
				"ORDER" => ["page_sort" => "DESC"]
			]);
		
	} else {

		$fc_nav = $db_content->select("fc_pages", ['page_id', 'page_classes', 'page_hash', 'page_language', 'page_linkname', 'page_permalink', 'page_target', 'page_title', 'page_sort', 'page_status'], [
				"page_language" => $languagePack
			],[
				"ORDER" => ["page_sort" => "DESC"]
			]);
	}
	
	$fc_nav = fc_array_multisort($fc_nav, 'page_language', SORT_ASC, 'page_sort', SORT_ASC, SORT_NATURAL);
	$contents = array($page_contents,$fc_nav);
	
	return $contents;
}



/**
 * check if given url is a shortlink
 * if applicable, immediately redirect to page permalink
 */

function fc_check_shortlinks($shortlink) {

	global $db_content;
	
	$page = $db_content->get("fc_pages", ["page_permalink", "page_permalink_short_cnt"], [
		"page_permalink_short" => $shortlink
	]);	
	
	
	/* increase page_permalink_short_cnt
		 redirect to page_permalink	*/
		 
	if($page['page_permalink'] != '') {
				
		$page_permalink_short_cnt = $page['page_permalink_short_cnt'] +1;
		
		$db_content->update("fc_pages", [
			"page_permalink_short_cnt" => $page_permalink_short_cnt
		], [
			"page_permalink_short" => $shortlink
		]);
		
				
		$redirect = '/'.$page['page_permalink'];
		header("location: $redirect",TRUE,301);	
		exit;
	}	
}

/**
 * check if given url is a funnel uri
 * if applicable, immediately redirect to page permalink
 */

function fc_check_funnel_uri($uri) {
		
	global $db_content;

	$pages = $db_content->select("fc_pages", ["page_permalink", "page_funnel_uri"], [
		"page_funnel_uri[~]" => "%$uri%"
	]);
	
	foreach($pages as $page) {
		$page_funnel_uri = explode(',', $page['page_funnel_uri']);
		foreach($page_funnel_uri as $u) {

			if($u == $uri) {
				$redirect = '/'.$page['page_permalink'];
				header("location: $redirect",TRUE,301);
				exit;
			}
			
		}
	}	
}


function fc_get_type_of_use_pages($type) {
	
	global $db_content;
	global $languagePack;
	
	$page = $db_content->get("fc_pages", ["page_permalink", "page_funnel_uri"], [
		"AND" => [
			"page_type_of_use" => "$type",
			"page_language" => "$languagePack"
		]
	]);
	
	return $page;
}


?>