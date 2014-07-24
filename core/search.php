<?php


$start_search = "true";


$s = urldecode(strip_tags($s));

if(strlen($s) < 3) {
	$start_search = "false";
	$search_msg = $lang['msg_search_undersized'];
}


if($start_search == "true") {

	$sql = "SELECT page_id, page_permalink, page_language, page_title, page_meta_description, page_status, page_meta_keywords, page_content
			FROM fc_pages
			WHERE (page_title like :searchstring OR page_content like :searchstring OR page_meta_keywords like :searchstring)
			AND (page_language = :languagePack)
			AND (page_status != 'draft')
			AND (page_status != 'ghost')";
	
	
	try {
		$dbh = new PDO("sqlite:$fc_db_content");
		$sth = $dbh->prepare($sql);
		$sth->bindValue(':searchstring', "%{$s}%", PDO::PARAM_STR);
		$sth->bindValue(':languagePack', "$languagePack", PDO::PARAM_STR);
		$sth->execute();
		$arr_results = $sth->fetchAll(PDO::FETCH_ASSOC);
	}
	
	catch (PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
	
	$dbh = null;
	
	$cnt_result = count($arr_results);
	
	if($cnt_result < 1) {
		$search_msg = $lang[msg_search_no_results];
	} else {
		$search_msg = sprintf($lang[msg_search_results], $cnt_result);
		
		for($i=0;$i<$cnt_result;$i++) {
		
			if($fc_mod_rewrite == "permalink") {
				$arr_results[$i]['set_link'] = FC_INC_DIR . "/" . $arr_results[$i][page_permalink];
			} else {
				$arr_results[$i]['set_link'] = "$_SERVER[PHP_SELF]?p=" . $arr_results[$i][page_id];
			}
		
		} // eo $i
		
		
	}

}


$smarty->assign('page_title', "$lang[headline_searchresults] ($s)");
$smarty->assign('arr_results', $arr_results);
$smarty->assign('headline_searchresults', $lang[headline_searchresults]);
$smarty->assign('msg_searchresults', $search_msg);


$output = $smarty->fetch("searchresults.tpl");
$smarty->assign('page_content', $output);


?>