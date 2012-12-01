<?php



function get_most_clicked($num = 5) {


$num = (int) $num;

global $fc_db_stats;
global $fc_mod_rewrite;
global $fc_db_content;
global $languagePack;


/* complete $fc_db_content into array */
$dbh = new PDO("sqlite:$fc_db_content");

$sql = "SELECT page_id,	page_language, page_linkname, page_permalink, page_title, page_status
		FROM fc_pages
		WHERE page_status != 'draft' AND page_language = '$languagePack'
		ORDER BY page_sort ASC";

$contents = $dbh->query($sql)->fetchAll();
$dbh = null;

$cnt_contents = count($contents);

/* 
new array, the key -> page_id [$cont_title and $cont_linkname]
example: $cont_title[333] = "the half evil page";
*/

for($i=0;$i<$cnt_contents;$i++) {
	$cont_title[$contents[$i][page_id]] = $contents[$i][page_title];
	$cont_linkname[$contents[$i][page_id]] = $contents[$i][page_linkname];
	$cont_permalink[$contents[$i][page_id]] = $contents[$i][page_permalink];
}




$dbh = new PDO("sqlite:$fc_db_stats");

$statement = $dbh->query("SELECT * FROM hits WHERE page_id != '' ORDER BY counter DESC ");

$result = $statement->fetchAll();
$dbh = null;

/*
add missing data | linkname and title
get it from the array - a few lines above
*/
$cnt_result = count($result);
for($i=0;$i<$cnt_result;$i++) {
	$result[$i][linkname] = $cont_linkname[$result[$i][page_id]];
	$result[$i][pagetitle] = $cont_title[$result[$i][page_id]];
	$result[$i][page_permalink] = $cont_permalink[$result[$i][page_id]];
}

/* remove pages without title - for example 404 pages */
for($i=0;$i<$cnt_result;$i++) {
	if($result[$i][linkname] == "") {
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

	if($fc_mod_rewrite == "auto") {
		$set_title = str_replace(" ","_",$mostclicked[$i][pagetitle]);
		$mostclicked[$i][link] = FC_INC_DIR . "/" . $mostclicked[$i][linkname] ."/". $mostclicked[$i][page_id] ."/". $set_title;
	} elseif ($fc_mod_rewrite == "off") {
		$mostclicked[$i][link] = "$_SERVER[PHP_SELF]?p=" . $mostclicked[$i][page_id];
	} elseif ($fc_mod_rewrite == "permalink") {
		$mostclicked[$i][link] = FC_INC_DIR . "/" . $mostclicked[$i][page_permalink];
	}

} // eol $i






return $mostclicked;




}






/* -------------------------------------------------------------------------
# Generate Cache-file for
# most clicked pages
----------------------------------------------------------------------------
*/

function cache_most_clicked($num = 5) {


$max_entries = (int) $num;

global $fc_db_stats;
global $fc_mod_rewrite;
global $fc_db_content;
global $languagePack;


/* complete $fc_db_content into array */
$dbh = new PDO("sqlite:$fc_db_content");

$sql = "SELECT page_id,	page_language, page_linkname, page_permalink, page_title, page_status
		FROM fc_pages
		WHERE page_status != 'draft' AND page_language = '$languagePack'
		ORDER BY page_sort ASC";

$contents = $dbh->query($sql)->fetchAll();
$dbh = null;

$cnt_contents = count($contents);



/* 
new array, the key -> page_id [$cont_title and $cont_linkname]
example: $cont_title[333] = "the half evil page";
*/

for($i=0;$i<$cnt_contents;$i++) {
	$cont_title[$contents[$i][page_id]] = $contents[$i][page_title];
	$cont_linkname[$contents[$i][page_id]] = $contents[$i][page_linkname];
	$cont_permalink[$contents[$i][page_id]] = $contents[$i][page_permalink];
}




$dbh = new PDO("sqlite:$fc_db_stats");

$statement = $dbh->query("SELECT * FROM hits WHERE page_id != '' ORDER BY counter DESC ");

$result = $statement->fetchAll();
$dbh = null;

/*
 * add missing data -> linkname and title
*/
$cnt_result = count($result);
for($i=0;$i<$cnt_result;$i++) {
	$result[$i][linkname] = $cont_linkname[$result[$i][page_id]];
	$result[$i][pagetitle] = $cont_title[$result[$i][page_id]];
	$result[$i][page_permalink] = $cont_permalink[$result[$i][page_id]];
}

/* remove pages without title - for example 404 pages */
for($i=0;$i<$cnt_result;$i++) {
	if($result[$i][linkname] == "") {
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

	if($fc_mod_rewrite == "auto") {
		$set_title = str_replace(" ","_",$mostclicked[$i][pagetitle]);
		$mostclicked[$i][link] = FC_INC_DIR . "/" . $mostclicked[$i][linkname] ."/". $mostclicked[$i][page_id] ."/". $set_title;
	} elseif ($fc_mod_rewrite == "off") {
		$mostclicked[$i][link] = "index.php?p=" . $mostclicked[$i][page_id];
	} elseif ($fc_mod_rewrite == "permalink") {
		$mostclicked[$i][link] = FC_INC_DIR . "/" . $mostclicked[$i][page_permalink];
	}
	
	$string .= "\$arr_mostclicked[$i][page_id] = \"" . $mostclicked[$i][page_id] . "\";\n";
	$string .= "\$arr_mostclicked[$i][link] = \"" . $mostclicked[$i][link] . "\";\n";
	$string .= "\$arr_mostclicked[$i][linkname] = \"" . $mostclicked[$i][linkname] . "\";\n";
	$string .= "\$arr_mostclicked[$i][pagetitle] = \"" . $mostclicked[$i][pagetitle] . "\";\n";

} // eol $i



$string .= "?>";


$file = FC_CONTENT_DIR . "/cache/cache_mostclicked.php";
@file_put_contents($file, $string, LOCK_EX);

}



?>