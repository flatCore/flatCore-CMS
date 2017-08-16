<?php

/**
 * create breadcrumb menu
 * example: Homepage >> Products >> Product XY
 * @return	array
 */

function breadcrumbs_menu($num){

	global $page_contents;
	global $fc_mod_rewrite;
	global $fc_nav;
	
	$points_of_num = substr_count($num, '.');
	$count_result = count($fc_nav);
	
	if($points_of_num > 0) {
	
		$str_length = strlen($num);
		
		for($i=0;$i<$count_result;$i++) {
		
			if($fc_nav[$i]['page_sort'] == "") {
				continue; //no page_sort no menu item
			}
			
			$id = $fc_nav[$i]['page_id'];
			$sort = $fc_nav[$i]['page_sort'];
			$sort_length = strlen($sort);
			$linkname = stripslashes($fc_nav[$i]['page_linkname']);
			$permalink = $fc_nav[$i]['page_permalink'];
			$trim_actual_page = substr($num, 0, $sort_length);
			
			if($sort === $trim_actual_page) {
			
				$bc[$i]['page_id'] = $fc_nav[$i]['page_id'];
				$bc[$i]['page_sort'] = $fc_nav[$i]['page_sort'];
				$bc[$i]['page_linkname'] = stripslashes($fc_nav[$i]['page_linkname']);
				$bc[$i]['page_permalink'] = $fc_nav[$i]['page_permalink'];
				$bc[$i]['page_title'] = $fc_nav[$i]['page_title'];
				
				$bc[$i]['link'] = FC_INC_DIR . "/" . $fc_nav[$i]['page_permalink'];
			
			}
		
		
		}
	
	}
	
	return($bc);

}


?>