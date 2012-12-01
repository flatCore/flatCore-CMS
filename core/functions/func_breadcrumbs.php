<?php

function breadcrumbs_menu($num){

global $result;
global $current_page_sort;
global $fc_mod_rewrite;

unset($sort);

$points_of_num = substr_count($num, '.');

$count_result = count($result);



if($points_of_num > 0) {

$str_length = strlen($num);

for($i=0;$i<$count_result;$i++) {

if($result[$i][page_sort] == "") {
	continue; //no page_sort no menu item
}

$id = $result[$i][page_id];
$sort = $result[$i][page_sort];
$sort_length = strlen($sort);
$linkname = stripslashes($result[$i][page_linkname]);
$permalink = $result[$i][page_permalink];
$trim_actual_page = substr($num, 0, $sort_length);



if($sort == "$trim_actual_page") {

$bc[$i][page_id] = $result[$i][page_id];
$bc[$i][page_sort] = $result[$i][page_sort];
$bc[$i][page_linkname] = stripslashes($result[$i][page_linkname]);
$bc[$i][page_permalink] = $result[$i][page_permalink];


if($fc_mod_rewrite == "on") {
	$set_title = str_replace(" ","_",$result[$i][page_title]);
	$set_linkname = str_replace(" ","_",$result[$i][page_linkname]);
	$bc[$i][link] = FC_INC_DIR . "/" . $set_linkname ."/". $result[$i][page_id] ."/". $set_title;
} elseif ($fc_mod_rewrite == "off") {
	$bc[$i][link] = "$_SERVER[PHP_SELF]?p=" . $result[$i][page_id];
} elseif ($fc_mod_rewrite == "permalink") {
	$bc[$i][link] = FC_INC_DIR . "/" . $result[$i][page_permalink];
}




}


} // eol $i

}

return($bc);

} // eol func breadcrumbs_menu


?>