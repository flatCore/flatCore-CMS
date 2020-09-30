<?php


function get_lastedit($num = 5) {

	$num = (int) $num;
	
	global $db_content;
	global $languagePack;
	
	$result = $db_content->select("fc_pages", ["page_linkname","page_permalink","page_title","page_status","page_lastedit"], [
		"AND" => [
			"page_status[!]" => ["draft,ghost"],
			"page_language" => "$languagePack"
		],
		"LIMIT" => $num,
		"ORDER" => [
			"page_lastedit" => "DESC"
		]
	]);
		
	$count_result = count($result);
	
	for($i=0;$i<$count_result;$i++) {
		$result[$i]['link'] = FC_INC_DIR . "/" . $result[$i]['page_permalink'];
	}
	
	
	return $result;
}

?>