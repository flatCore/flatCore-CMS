<?php

/* track pageviews */

$dbh = new PDO("sqlite:$fc_db_stats");


if($p != "") {
	$hits_page_id = $p;
}

if($page_contents[page_sort] == "portal") {
	$hits_page_id = "portal_$languagePack";
}

$sql = "SELECT counter FROM hits WHERE page_id='$hits_page_id'";
$result = $dbh->query($sql)->fetchAll();

if(sizeof($result) != 0) {
	$set_counter = $result[0]['counter'] + 1;
	$dbh->exec("UPDATE hits SET counter=$set_counter WHERE page_id='$hits_page_id'");
} else {
	$dbh->exec("INSERT INTO hits (page_id, counter) VALUES ('$hits_page_id', 1)");
}

$dbh = null;

?>