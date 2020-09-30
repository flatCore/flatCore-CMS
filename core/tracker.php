<?php

/* track pageviews */

if($p != "") {
	$hits_page_id = $p;
}

if($page_contents['page_sort'] == "portal") {
	$hits_page_id = "portal_$languagePack";
}

$counter = $db_statistics->get("hits", ["counter"],
	[
		"page_id" => $hits_page_id
	]);

if(is_array($counter)) {
	$set_counter = $counter['counter'] + 1;
	
	$db_statistics->update("hits", [
		"counter" => $set_counter
		], [
		"page_id" => $hits_page_id
		]);
	
	} else {
	
	$db_statistics->insert("hits", [
		"page_id" => $hits_page_id,
		"counter" => 1
		]);		
}
?>