<?php

function get_most_clicked($num = 5) {

	$num = (int) $num;

	global $db_statistics;
	global $db_content;
	global $languagePack;


	$contents = $db_content->select("fc_pages", ["page_id","page_language","page_linkname","page_permalink","page_title","page_status"], [
		"AND" => [
			"page_status[!]" => ["draft,ghost"],
			"page_language" => "$languagePack"
		]
	]);	
	
	
	$cnt_contents = count($contents);

	/**
	 * new array, the key -> page_id [$cont_title and $cont_linkname]
	 * example: $cont_title[333] = "the half evil page";
	 */
	
	for($i=0;$i<$cnt_contents;$i++) {
		$cont_title[$contents[$i]['page_id']] = $contents[$i]['page_title'];
		$cont_linkname[$contents[$i]['page_id']] = $contents[$i]['page_linkname'];
		$cont_permalink[$contents[$i]['page_id']] = $contents[$i]['page_permalink'];
	}
	
	$result = $db_statistics->select("hits", "*", [
		"AND" => [
			"page_id[!]" => ""
		],
		"ORDER" => [
			"counter" => "ASC"
		]
	]);	
	
	
	/**
	 * add missing data | linkname and title
	 * get it from the array - a few lines above
	 */
	$cnt_result = count($result);
	for($i=0;$i<$cnt_result;$i++) {
		$result[$i]['linkname'] = $cont_linkname[$result[$i]['page_id']];
		$result[$i]['pagetitle'] = $cont_title[$result[$i]['page_id']];
		$result[$i]['page_permalink'] = $cont_permalink[$result[$i]['page_id']];
	}
	
	/* remove pages without title - for example 404 pages */
	for($i=0;$i<$cnt_result;$i++) {
		if($result[$i]['linkname'] == "") {
			unset($result[$i]);
		}
	}
	
	$result = array_values($result);
	

	/* limit the number to $num */
	for($i=0;$i<$num;$i++) {
		$mostclicked[] = $result[$i];
	}
	
	$count_result = count($mostclicked);
	
	for($i=0;$i<$count_result;$i++) {
		$mostclicked[$i]['link'] = FC_INC_DIR . "/" . $mostclicked[$i]['page_permalink'];
	}
	
	return $mostclicked;
}






/**
 * Generate Cache-file for
 * most clicked pages
 */

function cache_most_clicked($num = 5) {

	$max_entries = (int) $num;
	
	global $db_statistics;
	global $db_content;
	global $languagePack;
	
	
	$contents = $db_content->select("fc_pages", ["page_id",	"page_language", "page_linkname", "page_permalink", "page_title", "page_status"], [
		"AND" => [
			"page_status[!]" => ["draft,ghost"],
			"page_language" => "$languagePack"
		],
		"ORDER" => [
			"page_sort" => "ASC"
		]
	]);
	
	
	$cnt_contents = count($contents);
	
	
	/**
	 * new array, the key -> page_id [$cont_title and $cont_linkname]
	 * example: $cont_title[333] = "the half evil page";
	 */
	
	for($i=0;$i<$cnt_contents;$i++) {
		$cont_title[$contents[$i]['page_id']] = $contents[$i]['page_title'];
		$cont_linkname[$contents[$i]['page_id']] = $contents[$i]['page_linkname'];
		$cont_permalink[$contents[$i]['page_id']] = $contents[$i]['page_permalink'];
	}
		
	$result = $db_statistics->select("hits", "*", [
		"AND" => [
			"page_id[!]" => ""
		],
		"ORDER" => [
			"counter" => "DESC"
		]
	]);	
	
	
	/* add missing data -> linkname and title */
	$cnt_result = count($result);
	for($i=0;$i<$cnt_result;$i++) {
		$result[$i]['linkname'] = $cont_linkname[$result[$i]['page_id']];
		$result[$i]['pagetitle'] = $cont_title[$result[$i]['page_id']];
		$result[$i]['page_permalink'] = $cont_permalink[$result[$i]['page_id']];
	}
	
	/* remove pages without title - for example 404 pages */
	for($i=0;$i<$cnt_result;$i++) {
		if($result[$i]['linkname'] == "") {
			unset($result[$i]);
		}
	}
	
	$result = array_values($result);
	$cnt_result = count($result);
	
	if($cnt_result <= $max_entries) {
		$max_entries = $cnt_result;
	}
	
	
	/* limit the number to $max_entries */
	for($i=0;$i<$max_entries;$i++) {
		$mostclicked[] = $result[$i];
	}
	
	$count_result = count($mostclicked);
	
	$string = "<?php\n";
	
	for($i=0;$i<$count_result;$i++) {
		
		$mostclicked[$i]['link'] = FC_INC_DIR . "/" . $mostclicked[$i]['page_permalink'];
		
		$string .= "\$arr_mostclicked[$i]['page_id'] = \"" . $mostclicked[$i]['page_id'] . "\";\n";
		$string .= "\$arr_mostclicked[$i]['link'] = \"" . $mostclicked[$i]['link'] . "\";\n";
		$string .= "\$arr_mostclicked[$i]['linkname'] = \"" . htmlentities($mostclicked[$i]['linkname'],ENT_QUOTES) . "\";\n";
		$string .= "\$arr_mostclicked[$i]['pagetitle'] = \"" . htmlentities($mostclicked[$i]['pagetitle'],ENT_QUOTES) . "\";\n";
	
	} // eol $i
	
	$string .= "?>";
	
	$file = FC_CONTENT_DIR . "/cache/cache_mostclicked.php";
	@file_put_contents($file, $string, LOCK_EX);

}



?>