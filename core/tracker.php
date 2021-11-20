<?php

/* track pageviews */

$hits_page_id = $page_contents['page_id'];

$counter = '';

$counter = $db_statistics->get("hits", "counter",
	[
		"page_id" => $hits_page_id
	]);


if($counter != '') {
	$set_counter = $counter + 1;
	
	$db_statistics->update("hits", [
		"counter" => $set_counter
		], [
		"page_id" => "$hits_page_id"
		]);
	
} else {
		
	$db_statistics->insert("hits", [
		"page_id" => $hits_page_id,
		"counter" => 1
	]);
	
}
?>