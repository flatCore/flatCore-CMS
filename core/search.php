<?php

$start_search = "true";

$s = sanitizeUserInputs($s);

if($s != '' && strlen($s) < 3) {
	$start_search = "false";
	$search_msg = $lang['msg_search_undersized'];
}

if($s != '' && $start_search == "true") {

	$sr = fc_search($s,1,10);
	$cnt_result = count($sr);
	if($cnt_result < 1) {
		$search_msg = $lang['msg_search_no_results'];
	} else {
		$search_msg = sprintf($lang['msg_search_results'], $cnt_result);
		
		for($i=0;$i<$cnt_result;$i++) {
			$sr[$i]['set_link'] = $sr[$i]['page_url'];
			
			if(strpos($sr[$i]['snipp'],'<|>') === false) {
				$sr[$i]['page_meta_description'] = $sr[$i]['snipp'];
			} else {
				$sr[$i]['page_meta_description'] = $sr[$i]['page_description'];
			}
			
			
		}

	}
}


$page_title = $lang['headline_searchresults'] . ' '.$s;


$smarty->assign('page_title', $page_title, true);
$smarty->assign('arr_results', $sr, true);

$smarty->assign('headline_searchresults', $lang['headline_searchresults'], true);

$smarty->assign('msg_searchresults', $search_msg, true);
$smarty->assign('search_string', $s, true);
$search_tpl = $smarty->fetch("search.tpl");
$output = $smarty->fetch("searchresults.tpl");
$smarty->assign('page_content', "$search_tpl $output", true);



?>