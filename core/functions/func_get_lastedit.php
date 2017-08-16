<?php


function get_lastedit($num = 5) {

	$num = (int) $num;
	
	global $fc_db_content;
	global $fc_mod_rewrite;
	global $languagePack;
	
	
	$dbh = new PDO("sqlite:$fc_db_content");
	$sql = "SELECT page_id, page_linkname, page_permalink, page_title, page_status, page_lastedit	FROM fc_pages
			WHERE page_status != 'draft' AND page_status != 'ghost' AND page_language = :languagePack
			ORDER BY page_lastedit DESC 
			LIMIT 0 , :num";
	
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':languagePack', $languagePack, PDO::PARAM_STR);
	$sth->bindParam(':num', $num, PDO::PARAM_INT);
	$sth->execute();
	$result = $sth->fetchAll(PDO::FETCH_ASSOC);
	$dbh = null;
	
	$count_result = count($result);
	
	for($i=0;$i<$count_result;$i++) {
		$result[$i]['link'] = FC_INC_DIR . "/" . $result[$i]['page_permalink'];
	}
	
	
	return $result;
}

?>