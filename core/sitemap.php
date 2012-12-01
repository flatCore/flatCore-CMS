<?php

/**
 * generate sitemap
 * display as ul
 *
 * this feature is not official until now
 * see it in action -> index.php?p=sitemap
 */



try {
	$dbh = new PDO("sqlite:$fc_db_content");
	
	$sql = "SELECT page_id, page_language, page_permalink, page_linkname, page_title, page_sort, page_status
		FROM fc_pages
		WHERE page_status != 'draft' AND page_language = 'de'
		ORDER BY page_sort ASC";
	    
	$results = $dbh->query($sql)->fetchAll();

}

catch (PDOException $e) {
	echo 'Error: ' . $e->getMessage();
}

   
$dbh = null;   


$cnt_results = count($results);


$sm_string = "<ul id='sitemap'>";


for($i=0;$i<$cnt_results;$i++) {

	$page_id = $results[$i]['page_id'];
	$page_sort = $results[$i]['page_sort'];
	$page_linkname = $results[$i]['page_linkname'];
	$page_title = $results[$i]['page_title'];
	$page_status = $results[$i]['page_status'];
	$page_permalink = $results[$i]['page_permalink'];
	

if($results[$i]['page_sort'] == "" || $results[$i]['page_sort'] == 'portal') {
	continue;
}
	
	
$points_of_item[$i] = substr_count($page_sort, '.');

unset($next_level);
if($points_of_item[$i] > $points_of_item[$i-1]) {
	$next_level = "<ul>";
}


unset($end_level);
if($points_of_item[$i] < $points_of_item[$i-1]) {
	$div_level = $points_of_item[$i] - $points_of_item[$i-1];
	$end_level = str_repeat("</ul>", abs($div_level));
}



if($fc_mod_rewrite == "permalink") {
	$target = FC_INC_DIR . "/" . $page_permalink;
} else {
	$target = "$_SERVER[PHP_SELF]?p=$page_id";
}

$sm_string .= "$next_level $end_level <li><a href='$target' title='$page_title'>$page_linkname</a><br />
											<span class='silentInfo'>$page_title</span>
									   </li> ";




} // eo $i


$sm_string .= "</ul>";

   


/*
Send data to the template
*/

$smarty->assign('page_title', 'Sitemap');
$smarty->assign('sitemap_list', $sm_string);

$output = $smarty->fetch("sitemap.tpl");
$smarty->assign('page_content', $output);





?>