<?php


/**
 * Build the Mainmenu
 * get all pages where page_sort is integer
 *
 * @return	array
 */

function show_mainmenu() {

	global $fc_nav;
	global $current_page_sort;
	global $fc_mod_rewrite;
	global $fc_defs;
	
	$count_result = count($fc_nav);
	
	for($i=0;$i<$count_result;$i++) {
		
		if($fc_nav[$i]['page_sort'] == 'portal') {
			$menu['homepage_linkname'] = $fc_nav[$i]['page_linkname'];
			$menu['homepage_title'] = $fc_nav[$i]['page_title'];
		}
	
		if($fc_nav[$i]['page_sort'] == "" || $fc_nav[$i]['page_permalink'] == "" || $fc_nav[$i]['page_sort'] == 'portal') {
			continue; //no page_sort or portal -> no menu item
		}
		
		$sort = $fc_nav[$i]['page_sort'];
		$points_of_item = substr_count($sort, '.');
		
		if($points_of_item < 1) {
			$menu[$i]['page_id'] = $fc_nav[$i]['page_id'];
			$menu[$i]['page_sort'] = $fc_nav[$i]['page_sort'];
			$menu[$i]['page_linkname'] = stripslashes($fc_nav[$i]['page_linkname']);
			$menu[$i]['page_title'] = stripslashes($fc_nav[$i]['page_title']);
			$menu[$i]['page_permalink'] = $fc_nav[$i]['page_permalink'];
			$menu[$i]['page_target'] = $fc_nav[$i]['page_target'];
			$menu[$i]['page_hash'] = $fc_nav[$i]['page_hash'];
			$menu[$i]['page_classes'] = $fc_nav[$i]['page_classes'];
			$menu[$i]['link_status'] = $fc_defs['main_nav_class'];
		
			if(left_string($current_page_sort) == left_string($menu[$i]['page_sort']) ) {
				$menu[$i]['link_status'] = $fc_defs['main_nav_class_active'];
				define('FC_MAIN_CAT', clean_filename($fc_nav[$i]['page_linkname']));
				define('FC_TOC_HEADER', $menu[$i]['page_linkname']);
			}
		
			/* generate the main menu */
			$menu[$i]['link'] = FC_INC_DIR . "/" . $fc_nav[$i]['page_permalink'];
		}
	}
	
	
	return $menu;

} // eol func show_menu




/**
 * Build the Submenu
 * get all pages where page_sort begins with the given number (also a page_sort)
 *
 * @param mixed $num (page_sort of parent page)
 * @return array
 */

function show_menu($num){
	
	global $fc_nav;
	global $current_page_sort;
	
	
	if($num == "") { return; }
	$items = array();
	
	$num_split = explode('.',$num);
	$current_page_sort_split = explode('.',$current_page_sort);
	$current_level = count($num_split);
	$cnt_all_navs = count($fc_nav); // number of all nav entries
	
	$current_match_elements = array_slice($num_split, 0, $current_level);
		
	for($i=0;$i<$cnt_all_navs;$i++) {
		
		$nav_sort = $fc_nav[$i]['page_sort'];
		$nav_split = explode('.',$nav_sort);
		$nav_level = count($nav_split); // level
		$nav_match_elements = array_slice($nav_split, 0, $current_level);
		
		if($nav_level <= 1) {
			continue;
		}
		
		if($nav_level > ($current_level+1)) {
			continue;
		}
		
		if($nav_level > $current_level) {
			
			if($current_match_elements !== $nav_match_elements) {
				continue;
			}

		}
		
		if($nav_level <= $current_level) {
			
			$l = array_slice($num_split, 0, ($nav_level-1));
			$r = array_slice($nav_split, 0, ($nav_level-1));

			if($l !== $r) {
				continue;
			}
			
		}
				
		
		if(count(array_intersect_assoc($num_split, $nav_split)) < 1) {
			continue;
		}
		
		$items = build_submenu($i,$nav_level);
		
		foreach($items as $value) {
			$m[] = $value;
		}


	}

	return $m;
}



function build_submenu($index,$level=1) {
	
	global $fc_mod_rewrite;
	global $fc_nav;
	global $current_page_sort;
	global $fc_defs;
	
	$sort = $fc_nav[$index]['page_sort'];
	
	$submenu[$index]['page_id'] = $fc_nav[$index]['page_id'];
	$submenu[$index]['page_sort'] = $fc_nav[$index]['page_sort'];
	$submenu[$index]['page_permalink'] = $fc_nav[$index]['page_permalink'];
	$submenu[$index]['page_target'] = $fc_nav[$index]['page_target'];
	$submenu[$index]['page_hash'] = $fc_nav[$index]['page_hash'];
	$submenu[$index]['page_classes'] = $fc_nav[$index]['page_classes'];
	$submenu[$index]['page_linkname'] = stripslashes($fc_nav[$index]['page_linkname']);
	$submenu[$index]['page_title'] = stripslashes($fc_nav[$index]['page_title']);
	

	if($sort === $current_page_sort) {
		$submenu[$index]['link_status'] = $fc_defs['sub_nav_prefix_class_active'].$level;
	} else {
		$submenu[$index]['link_status'] = 'sub_link'.$level;
	}
	
	$submenu[$index]['sublink'] = FC_INC_DIR . "/" . $fc_nav[$index]['page_permalink'];
	
	return $submenu;
}




/**
 * Build an unordered list <ul> with al pages and sub-pages
 * sort by page_sort
 * use {$fc_sitemap} in your templates
 * @return string
*/


function show_sitemap() {
	
	global $fc_nav;
	global $current_page_sort;
	global $fc_mod_rewrite;
	global $fc_defs;
	
	$cnt_results = count($fc_nav);
	
	$sm_string .= '<ul class="'.$fc_defs['sm_ul_class'].'">'."\r\n";
	
	for($i=0;$i<$cnt_results;$i++) {
	
		$page_id = $fc_nav[$i]['page_id'];
		$page_sort = $fc_nav[$i]['page_sort'];
		$page_linkname = $fc_nav[$i]['page_linkname'];
		$page_title = $fc_nav[$i]['page_title'];
		$page_status = $fc_nav[$i]['page_status'];
		$page_hash = $fc_nav[$i]['page_hash'];
		$page_classes = $fc_nav[$i]['page_classes'];
		$page_permalink = $fc_nav[$i]['page_permalink'];
		
		$li_class = '';
		
		if($fc_nav[$i]['page_sort'] == "" || $fc_nav[$i]['page_sort'] == 'portal') {
			continue;
		}
		
		$target = FC_INC_DIR . "/" . $page_permalink;
		
		$points_of_item[$i] = substr_count($page_sort, '.');

		// set class for current page
		$li_class = '';
		if($current_page_sort == $page_sort) {
			$li_class .= $fc_defs['sm_li_class_active'] . ' ';
		}
		
		if(strpos($current_page_sort, $page_sort) !== false) {
			$li_class .= $fc_defs['sm_li_class_active_tree'] . ' ';
		}
		
		// new level, increase class name sm_ul_class
		$start_ul = '';
		if($points_of_item[$i] > $points_of_item[$i-1]) {
			$stage_ul_class = $fc_defs['sm_ul_class'] . '-' . $points_of_item[$i];
			$start_ul = "\r\n".'<ul class='.$stage_ul_class.'>'."\r\n";
			// remove the last </li>
			$sm_string = substr(trim($sm_string), 0, -5);
		}	
		
		// end this level </ul>
		$end_ul = '';
		if($points_of_item[$i] < $points_of_item[$i-1]) {
			$div_level = $points_of_item[$i] - $points_of_item[$i-1];
			$end_ul = str_repeat("</ul>", abs($div_level));
			$end_ul .= '</li>';
		}
		
		$start_li = "\r\n".'<li class="'.$li_class.'">';
		
		$end_li = '</li>';
		
		$sm_string .= "$start_ul";
		$sm_string .= "$end_ul";
		$sm_string .= $start_li;
		$sm_string .= '<a href="'.$target.'" title="'.$page_title.'">'.$page_linkname.'</a>';
		$sm_string .= $end_li;
	}

	$sm_string .= '</ul>'."\r\n";	
		
	return $sm_string;
}



function left_string($string) {
  $string = explode(".", $string);
  return $string[0];
}

?>