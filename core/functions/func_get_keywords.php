<?php

function get_keywords() {

	global $fc_db_content;
	global $fc_mod_rewrite;
	global $languagePack;
	
	$dbh = new PDO("sqlite:$fc_db_content");
	$sql = "SELECT page_meta_keywords FROM fc_pages
			WHERE page_status != 'draft' AND page_status != 'ghost' AND page_language = '$languagePack' ";
	
	foreach ($dbh->query($sql) as $row) {
		$clean_key = $row['page_meta_keywords'];
	  $clean_key = preg_replace("/ +/", " ", $clean_key);
	  $clean_key = trim($clean_key, " ");
	  $clean_key = strtolower($clean_key); 
	  if($clean_key != "") {
	  	$result .=  "$clean_key,";
	  }
	}  
	
	$dbh = null;
	
	$result = str_replace(", ",",",$result);
	$result = substr("$result", 0, -1);
	
	$array_keywords = array_count_values(explode(",",$result));
	arsort($array_keywords); // sort by strength
	$array_keywords = array_slice($array_keywords, 0, 25); // only the first 25
	ksort($array_keywords); // sort alphabetic
	
	$font_size = "90"; // %
	
	$x = 0;
	
	foreach($array_keywords as $key => $val) {
		$x ++;
	
		$skey = urlencode(trim($key));
		$fz = $font_size+($val*10);
		if($key == "") {continue;}
		$page_keywords .= '<span style="font-size:'.$fz.'%;"><a href="/search/?s='.$skey.'">'.$key.'</a></span>';
	
	}
	
	return $page_keywords;

}




?>