<?php

/* track pageviews */

$dbh = new PDO("sqlite:$fc_db_stats");


if($p != "") {
	$hits_page_id = $p;
}

if($page_contents['page_sort'] == "portal") {
	$hits_page_id = "portal_$languagePack";
}

$sql = 'SELECT counter FROM hits WHERE page_id = :hits_page_id';
$sth = $dbh->prepare($sql);
$sth->bindParam(':hits_page_id', $hits_page_id, PDO::PARAM_STR);
$sth->execute();
$counter = $sth->fetchAll(PDO::FETCH_ASSOC);

if(sizeof($counter) != 0) {
	$set_counter = $counter[0]['counter'] + 1;
	$sql = 'UPDATE hits SET counter = :set_counter WHERE page_id = :hits_page_id';
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':set_counter', $set_counter, PDO::PARAM_STR);
	$sth->bindParam(':hits_page_id', $hits_page_id, PDO::PARAM_STR);
	$sth->execute();
	
} else {
	$sql = 'INSERT INTO hits (page_id, counter) VALUES (:hits_page_id, 1)';
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':hits_page_id', $hits_page_id, PDO::PARAM_STR);
	$sth->execute();
	
}

$dbh = null;

?>