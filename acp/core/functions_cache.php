<?php
/**
 * prohibit unauthorized access
 */
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){ 
	die ('<h2>Direct File Access Prohibited</h2>');
}



/**
 * Generate Cache-file for
 * last edit pages
 */

function cache_lastedit($num = 5) {

	$num = (int) $num;
	
	global $fc_mod_rewrite;
	global $languagePack;
	global $db_content;

	$result = $db_content->select("fc_pages", ["page_id","page_linkname","page_title","page_status","page_lastedit","page_permalink"], [
		"page_status[!]" => "draft",
		"page_status[!]" => "ghost",
		"page_language" => "$languagePack",
		"ORDER" => ["page_lastedit" => "DESC"],
		"LIMIT" => [0,$num]
	]);
	
	$count_result = count($result);
	
	$string = "<?php\n";
	
	for($i=0;$i<$count_result;$i++) {
	
		$set_title = str_replace(" ","_",$result[$i]['page_title']);
		
		if($fc_mod_rewrite == "on") {
			$result[$i]['link'] = FC_ROOT . "/" . $result[$i]['page_linkname'] ."/". $result[$i]['page_id'] ."/". $set_title;
		} elseif ($fc_mod_rewrite == "off") {
			$result[$i]['link'] = "index.php?p=" . $result[$i]['page_id'];
		} elseif ($fc_mod_rewrite == "permalink") {
			$result[$i]['link'] = FC_ROOT . "/" . $result[$i]['page_permalink'];
		}
	
		$string .= "\$arr_lastedit[$i]['page_id'] = \"" . $result[$i]['page_id'] . "\";\n";
		$string .= "\$arr_lastedit[$i]['link'] = \"" . $result[$i]['link'] . "\";\n";
		$string .= "\$arr_lastedit[$i]['page_title'] = \"" . htmlentities($result[$i]['page_title'],ENT_QUOTES) . "\";\n";
		$string .= "\$arr_lastedit[$i]['page_linkname'] = \"" . htmlentities($result[$i]['page_linkname'],ENT_QUOTES) . "\";\n";
	
	} // eol $i
	
	$string .= "?>";
	
	
		$file = FC_CONTENT_DIR . "/cache/cache_lastedit.php";
		file_put_contents($file, $string, LOCK_EX);
}


/**
 * Generate Cache-file for
 * tag cloud (keywords)
 */

function cache_keywords() {

	global $languagePack;
	global $db_content;
	
	$keywords = $db_content->select("fc_pages", ["page_meta_keywords"], [
		"page_status[!]" => "draft",
		"page_status[!]" => "ghost",
		"page_language" => "$languagePack"
	]);
	
	foreach($keywords as $key) {
		$clean_key = $key['page_meta_keywords'];
		$clean_key = preg_replace("/ +/", " ", $clean_key);
		$clean_key = trim($clean_key, " ");
		$clean_key = strtolower($clean_key); 
	  if($clean_key != "") {
	  	$result .=  "$clean_key,";
	  }		
	}
	
	
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
		$page_keywords .= '<span style="font-size:'.$fz.'%;"><a href="/search/?s='.$skey.'" title="'.$key.'">'.$key.'</a></span>';
	
	} // eol foreach
	
		$file = FC_CONTENT_DIR . "/cache/cache_keywords.html";
		file_put_contents($file, $page_keywords, LOCK_EX);

}


/**
 * try to delete cache files
 */
 
function delete_cache_file($file='cache_mostclicked') {
	
	$fp = FC_CONTENT_DIR . "/cache";
	$file = basename($file) . ".php";
	
	if(is_file("$fp/$file")) {
		@unlink("$fp/$file");
	}
	
}

/**
 * delete smarty cache files
 * $cache_id	(string)	md5(page_permalink) -> delete pages cache
 * 				(string) 'all' -> delete complete cache
 */

function fc_delete_smarty_cache($cache_id) {
	
	require_once '../lib/Smarty/Smarty.class.php';
	$smarty = new Smarty;
	$smarty->cache_dir = FC_CONTENT_DIR.'/cache/cache/';
	$smarty->compile_dir = FC_CONTENT_DIR.'/cache/templates_c/';
	
	if($cache_id == 'all') {
		$smarty->clearAllCache();
		$smarty->clearCompiledTemplate();
	} else {
		$smarty->clearCache(null,$cache_id);
		$smarty->clearCompiledTemplate(null,$cache_id);		
	}

}




/**
 * cache all saved url paths
 * generate array from pages where permalink is not empty
 * store in ... cache/active_urls.php
 */

function cache_url_paths() {

	global $db_content;
	
	$result = $db_content->select("fc_pages", "*");	
	$count_result = count($result);
	
	$x = 0;
	$string = "\$existing_url = array();\n";
	for($i=0;$i<$count_result;$i++) {
		
		if($result[$i]['page_permalink'] != "") {
			$string .= "\$existing_url[$x] = \"" . $result[$i]['page_permalink'] . "\";\n";
			$x++;
		}
	}
	
	$str = "<?php\n$string\n?>";
	$file = FC_CONTENT_DIR . "/cache/active_urls.php";
	file_put_contents($file, $str, LOCK_EX);
}


?>