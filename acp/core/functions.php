<?php
/**
 * prohibit unauthorized access
 */
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){ 
	die ('<h2>Direct File Access Prohibited</h2>');
}


/**
 * get all installed language files
 * return as array
 */

function get_all_languages($d='../lib/lang') {

	//$mdir = "../lib/lang";
	$cntLangs = 0;
	$scanned_directory = array_diff(scandir($d), array('..', '.','.DS_Store'));
	
	foreach($scanned_directory as $lang_folder) {
		if(is_file("$d/$lang_folder/index.php")) {
			include $d.'/'.$lang_folder.'/index.php';
			$arr_lang[$cntLangs]['lang_sign'] = "$lang_sign";
			$arr_lang[$cntLangs]['lang_desc'] = "$lang_desc";
			$arr_lang[$cntLangs]['lang_folder'] = "$lang_folder";
			$cntLangs++;
		}
	}
	
	return($arr_lang);
}


/**
 * get all preferences
 * return as array
 */
 
function get_preferences() {
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "SELECT * FROM fc_preferences WHERE prefs_id = 1";

	$result = $dbh->query($sql);
	$result = $result->fetch(PDO::FETCH_ASSOC);

	$dbh = null;
	
	return $result;
}


/**
 * get all installed Moduls
 * return as array -> $arr_iMods
 */

function get_all_moduls() {

	$mdir = "../modules";
	$cntMods = 0;
	$scanned_directory = array_diff(scandir($mdir), array('..', '.','.DS_Store'));
		
	foreach($scanned_directory as $mod_folder) {
		if(is_file("$mdir/$mod_folder/info.inc.php")) {
			include $mdir.'/'.$mod_folder.'/info.inc.php';
			$arr_iMods[$cntMods]['name'] = $mod['name'];
			$arr_iMods[$cntMods]['folder'] = $mod_folder;
			$cntMods++;		
		}
	}

	return($arr_iMods);
}

/**
 * get all addons stored in table fc_addons
 * type = theme | module
 */
 
function fc_get_addons($t='module') {
	
	$result = array();
	
	if($t == 'module') {
		$type = 'module';
	} else {
		$type = 'theme';
	}
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "SELECT * FROM fc_addons WHERE addon_type = '$type' ";

	foreach ($dbh->query($sql) as $row) {
		$result[] = $row;
	}
	
	$dbh = null;
	
	return($result);
		
}


/**
 * get all user groups
 * return as array
 */

function get_all_groups() {

	$dbh = new PDO("sqlite:".USER_DB);	
	$sql = "SELECT * FROM fc_groups ORDER BY group_id ASC";
	
	foreach ($dbh->query($sql) as $row) {
		$result[] = $row;
	}
	
	return($result);
}


/**
 * get all admins
 * return as array
 */

function get_all_admins() {

	$dbh = new PDO("sqlite:".USER_DB);
	$sql = "SELECT * FROM fc_user WHERE user_class = 'administrator'";
	
	   foreach ($dbh->query($sql) as $row) {
	     $result[] = $row;
	   }
	
	$dbh = null;
	
	return($result);
}


/**
 * show all installed templates
 * return as array
 */

function get_all_templates() {

	//templates folder
	$sdir = "../styles";
	$cntStyles = 0;
	$scanned_directory = array_diff(scandir($sdir), array('..', '.','.DS_Store'));
	
	foreach($scanned_directory as $tpl_folder) {
		if(is_dir("$sdir/$tpl_folder")) {
			$arr_Styles[] = "$tpl_folder";
		}	
	}

	return($arr_Styles);
}


/**
 * show all images
 * return array
 */

function get_all_images() {

	global $img_path;
	$images = array();

	$dir = "../$img_path";
	$scan_dir = array_diff(scandir($dir), array('..', '.','.DS_Store'));
	$types = array('jpg','jpeg','png','gif');
	
	foreach($scan_dir as $key => $file) {
		$suffix = substr($file, strrpos($file, '.') + 1);
			if(in_array($suffix, $types)) {
			$images[] = basename($file);
		}
	}
	 return $images;
}

/**
 * show all images from images folder
 * optional filter by prefix
 * return array
 */

function fc_get_all_images($prefix='') {

	global $img_path;
	$images = array();

	$dir = "../$img_path";
	$scan_dir = array_diff(scandir($dir), array('..', '.','.DS_Store'));
	$types = array('jpg','jpeg','png','gif');
	
	foreach($scan_dir as $key => $file) {
		$suffix = substr($file, strrpos($file, '.') + 1);
		
			if(in_array($suffix, $types)) {
			
				if($prefix != '') {
					if(substr(basename($file), 0,strlen($prefix)) !== $prefix) {
						continue;
					}
				}
			
				$images[] = basename($file);
		  
		  }
	}
	 return $images;
}


/**
 * get all files from directory (recursive)
 * return array
 */

function fc_scandir_rec($dir) { 
   
   $result = array(); 

   $cdir = scandir($dir); 
   foreach ($cdir as $key => $value) { 
      if(in_array($value,array('..', '.','.DS_Store','index.html'))) {
	      continue;
	    }
      if(is_dir($dir . '/' . $value)) {
	      $result[] = fc_scandir_rec($dir . '/' . $value); 
      } else { 
        $result[] = $dir.'/'.$value; 
      } 
   } 
   $result = fc_flatten_array($result);
   return $result; 
} 

function fc_flatten_array(array $array) {
    $flattened_array = array();
    array_walk_recursive($array, function($a) use (&$flattened_array) { $flattened_array[] = $a; });
    return $flattened_array;
}



/**
 * CLEAN VARS // URL PARAMETERS
 */

function clean_vars($var) {
	$chars = array('<', '>', '\\', '/', '=','..'); 
	$var = str_replace($chars, "", $var);
	$var = strip_tags($var);
	return $var;
}

function clean_filename($str) {
	$str = strtolower($str);
	$a = array('ä','ö','ü','ß',' - ',' + ',' / ','/'); 
	$b = array('ae','oe','ue','ss','-','-','-','-');
	$str = str_replace($a, $b, $str);
	$str = preg_replace('/\s/s', '_', $str);  // replace blanks -> '_'
	$str = preg_replace('/[^a-z0-9_-]/isU', '', $str); // only a-z 0-9
	$str = trim($str); 
	return $str; 
}  


/**
 * MAKE DATES LIKE 2008-12-24
 * READABLE (for germans)
 */

function readable_dates($date_string) {
	$year = 	substr($date_string, 0, 4);
	$month = 	substr($date_string, 5, 2);
	$day = 		substr($date_string, 8, 2);
	$new_string = "$day.$month.$year";
	return $new_string;
}

/**
 * MAKE TIMESTRINGS LIKE 20080729142412 (YYYYMMDDHHMMSS)
 * READABLE (for germans)
 */

function readable_timestring($date_string) {
	$year = 	substr($date_string, 0, 4);
	$month = 	substr($date_string, 4, 2);
	$day = 		substr($date_string, 6, 2);
	$hour = 	substr($date_string, 8, 2);
	$min = 		substr($date_string, 10, 2);
	$sec = 		substr($date_string, 12, 2);
	$new_string = "$day.$month.$year $hour:$min:$sec";
	return $new_string;
}


/**
 * converting bytes to KB, MB, GB
 * Snippet from PHP Share: http://www.phpshare.org
 */
 
function readable_filesize($bytes) {
  if($bytes >= 1073741824) {
    $bytes = number_format($bytes / 1073741824, 2) . ' GB';
  } elseif ($bytes >= 1048576) {
      $bytes = number_format($bytes / 1048576, 2) . ' MB';
  } elseif ($bytes >= 1024) {
      $bytes = number_format($bytes / 1024, 2) . ' KB';
  } elseif ($bytes > 1) {
  	  $bytes = $bytes . ' bytes';
  } elseif ($bytes == 1) {
      $bytes = $bytes . ' byte';
  } else {
      $bytes = '0 bytes';
  }
  return $bytes;
}


/**
 * PRINT SYSTEM MESSAGE
 */

function print_sysmsg($msg) {

	$type = "{OKAY}";
	$pos = stripos($msg, $type);

	if($pos !== false) {
		$style = "alert alert-success";
	} else {
		$style = "alert alert-danger";
	}

	$msg = substr(strstr($msg, '}'), 2);
	echo '<div class="'.$style.' alert-dismissible fade in" role="alert">';
	echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>';
	echo $msg;
	echo '</div>';
}





/**
 * Get Page Impression
 */

function get_page_impression($pid) {

		$dbh = new PDO("sqlite:".STATS_DB);
		$sql = $dbh->query("SELECT counter FROM hits WHERE page_id = '$pid' ");
		$result = $sql->fetch(PDO::FETCH_ASSOC);
		$dbh = null;

		$counter = $result['counter'];
		return($counter);
}


/**
 * write a log message
 */

function record_log($log_trigger = 'system', $log_entry, $log_priority = '0') {

	$log_time = time();
	$dbh = new PDO("sqlite:".STATS_DB);
	$sql = "INSERT INTO log	(
			log_id , log_time , log_trigger , log_entry , log_priority
			) VALUES (
			NULL, '$log_time', '$log_trigger', '$log_entry', '$log_priority' ) ";
										
	$cnt_changes = $dbh->exec($sql);	
	$dbh = null;

}



/**
 * show log entries
 * delete records that are older than 30 days
 */

function show_log($nbr) {

	$dbh = new PDO("sqlite:".STATS_DB);
	$interval = time() - (30 * 86400); // 30 days
	$count = $dbh->exec("DELETE FROM log WHERE log_time < '$interval'");

	if($count > 0) {
		echo"<div class='alert alert-info'>Logs removed ($count)</div>";
	}

	$sql = "SELECT * FROM log ORDER BY log_id DESC";

   		foreach ($dbh->query($sql) as $row) {
     		$result[] = $row;
   		}
   
	$cnt_result = count($result);

	for($i=0;$i<$cnt_result;$i++) {

		$time = date("H:i:s",$result[$i]['log_time']);
		$date = date("d.m.Y",$result[$i]['log_time']);
		$log_priority = 'log_priority'.$result[$i]['log_priority'];
		
		echo '<dl class="dl-horizontal dl-logfile">';
		echo '<dt><span class="priority-indicator '.$log_priority.'" title="'.$result[$i]['log_priority'].'"></span>'.$date.' '.$time.'</dt>';
		echo '<dd>'.$result[$i]['log_trigger'].' - '. $result[$i]['log_entry'] .'</dd>';
		echo '</dl>';

	} // eol $i
	
	if($cnt_result < 1) {
		echo"<div class='alert alert-info'>No entries.</div>";
	}

} // eo func


/**
 * add new item to the feeds table
 */

function add_feed($title, $text, $url, $sub_id, $feed_name, $time = NULL) {

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$interval = time() - (30 * 86400); // 30 days
	$del_interval = $dbh->exec("DELETE FROM fc_feeds WHERE feed_time < '$interval'");
	$del_dublicates = $dbh->exec("DELETE FROM fc_feeds WHERE feed_subid = '$sub_id'");

	if(is_null($time)) {
		$time = time();
	}

	$sql = "INSERT INTO fc_feeds	(
			feed_id , feed_subid, feed_time , feed_name , feed_title , feed_text, feed_url
			) VALUES (
			NULL, :sub_id, :time, :feed_name, :title, :text, :url ) ";
			
	$sth = $dbh->prepare($sql);
	
	$sth->bindParam(':sub_id', $sub_id, PDO::PARAM_STR);
	$sth->bindParam(':time', $time, PDO::PARAM_STR);
	$sth->bindParam(':feed_name', $feed_name, PDO::PARAM_STR);
	$sth->bindParam(':title', $title, PDO::PARAM_STR);
	$sth->bindParam(':text', $text, PDO::PARAM_STR);
	$sth->bindParam(':url', $url, PDO::PARAM_STR);

	$cnt_changes = $sth->execute();

	$dbh = null;

}





/**
 * Generate XML Sitemap
 */

function generate_xml_sitemap() {

	global $languagePack;
	global $fc_base_url;
	
	$file = "../sitemap.xml";
	$tpl_sitemap = file_get_contents('templates/sitemap.tpl');
	$tpl_sitemap_urlset = file_get_contents('templates/sitemap_urlset.tpl');
	
	$dbh = new PDO("sqlite:".CONTENT_DB);	
	$sqlgp = "SELECT prefs_xml_sitemap FROM fc_preferences WHERE prefs_id = 1";
	$prefs_xml_sitemap = $dbh->query($sqlgp)->fetchColumn(); // -> returns on|off
	
	
	if($prefs_xml_sitemap == "on") {
	
		$sql = "SELECT page_id, page_language, page_permalink, page_lastedit, page_status	FROM fc_pages
				WHERE page_status = 'public' ORDER BY page_lastedit DESC ";
		    
		$results = $dbh->query($sql)->fetchAll();
		$dbh = null;
		
		$cnt_results = count($results);
		
		/* generate content for xml file */	
		$url_set = "";
		
		for($i=0;$i<$cnt_results;$i++) {
		
			$page_id = $results[$i]['page_id'];
			$page_permalink = $results[$i]['page_permalink'];
			$page_lastedit = date("Y-m-d",$results[$i]['page_lastedit']);
			
			$link = $fc_base_url . $page_permalink;
			
			$link = str_replace("/acp","",$link);
			
			$url_set = str_replace('{url}', $link, $tpl_sitemap_urlset);
			$url_set = str_replace('{lastmod}', $page_lastedit, $url_set);
			$url_set_list .= $url_set."\r\n";			
		}

		$sitemap = str_replace('{url_set}', $url_set_list, $tpl_sitemap);	
		file_put_contents($file, $sitemap, LOCK_EX);
	}
}


/**
 * Generate Cache-file for
 * last edit pages
 */

function cache_lastedit($num = 5) {

	$num = (int) $num;
	
	global $fc_db_content;
	global $fc_mod_rewrite;
	global $languagePack;
	
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	
	$sql = "SELECT page_id, page_linkname, page_permalink, page_title, page_status, page_lastedit
			FROM fc_pages
			WHERE page_status != 'draft' AND page_status != 'ghost' AND page_language = '$languagePack'
			ORDER BY page_lastedit DESC 
			LIMIT 0 , $num
			";
	
	   foreach ($dbh->query($sql) as $row) {
	     $result[] = $row;
	   }  
	
	$dbh = null;
	
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
	
	
		$file = "../" . FC_CONTENT_DIR . "/cache/cache_lastedit.php";
		file_put_contents($file, $string, LOCK_EX);
}


/**
 * Generate Cache-file for
 * tag cloud (keywords)
 */

function cache_keywords() {

	global $languagePack;
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	
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
	
	} // eol foreach
	
		$file = "../" . FC_CONTENT_DIR . "/cache/cache_keywords.html";
		file_put_contents($file, $page_keywords, LOCK_EX);

}


/**
 * try to delete cache files
 */
 
function delete_cache_file($file='cache_mostclicked') {
	
	$fp = "../" . FC_CONTENT_DIR . "/cache";
	$file = basename($file) . ".php";
	
	if(is_file("$fp/$file")) {
		@unlink("$fp/$file");
	}
	
}

/**
 * delete smarty cache files
 * $cache_id	(string)	md5(page_permalink) -> delete pages cach
 * 				(string) 'all' -> delete complete cache
 */

function fc_delete_smarty_cache($cache_id) {
	
	require_once '../lib/Smarty/Smarty.class.php';
	$smarty = new Smarty;
	$smarty->cache_dir = '../'.FC_CONTENT_DIR.'/cache/cache/';
	$smarty->compile_dir = '../'.FC_CONTENT_DIR.'/cache/templates_c/';
	
	if($cache_id == 'all') {
		$smarty->clearAllCache();
		$smarty->clearCompiledTemplate();
	} else {
		$smarty->clearCache(null,$cache_id);
		$smarty->clearCompiledTemplate(null,$cache_id);		
	}

}


/**
 * check in active modules
 * generate array from pages containing a module
 * and from addon_dir -> content.sqlite3
 * store in ... cache/active_mods.php
 */

function mods_check_in() {
	
	$pages = array();
	$mods = array();
	$m = array();

	$dbh = new PDO("sqlite:".CONTENT_DB);
	
	$sql_get_mods = "SELECT addon_dir FROM fc_addons WHERE addon_type = 'module'";
	foreach ($dbh->query($sql_get_mods) as $row) {
		$mods[] = $row;
	}
	
	for($i=0;$i<count($mods);$i++) {
		$m[]['page_modul'] = $mods[$i]['addon_dir'];
		$m[]['page_permalink'] = 'NULL';
	}
	
	
	$sql = "SELECT page_modul, page_permalink FROM fc_pages";
	
	foreach ($dbh->query($sql) as $row) {
		$pages[] = $row;
	}
	
	$dbh = null;
	
	$items = array_merge($pages, $m);
	
	$cnt_items = count($items);
	$x = 0;
	for($i=0;$i<$cnt_items;$i++) {
	
		if($items[$i]['page_modul'] != "") {
			$string .= "\$active_mods[$x]['page_modul'] = \"" . $items[$i]['page_modul'] . "\";\n";
			$string .= "\$active_mods[$x]['page_permalink'] = \"" . $items[$i]['page_permalink'] . "\";\n";			
			$x++;
		}
	
	}
	
	$str = "<?php\n$string\n?>";
		
	$file = "../" . FC_CONTENT_DIR . "/cache/active_mods.php";
	file_put_contents($file, $str, LOCK_EX);

}


/**
 * cache all saved url paths
 * generate array from pages where permalink is not empty
 * store in ... cache/active_urls.php
 */

function cache_url_paths() {

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "SELECT * FROM fc_pages";
	
	foreach ($dbh->query($sql) as $row) {
		$result[] = $row;
	}  
	
	$dbh = null;
	
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
	$file = "../" . FC_CONTENT_DIR . "/cache/active_urls.php";
	file_put_contents($file, $str, LOCK_EX);
}


/**
 * get custom columns from table fc_pages
 * return array
 */

function get_custom_fields() {

	$customs_fields = array();

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "SELECT * FROM fc_pages";
	
	$cf = $dbh->query($sql);
	$cf = $cf->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	
	$cf = array_keys($cf);
	$cnt_cf = count($cf);
	
	for($i=0;$i<$cnt_cf;$i++) {
		if(substr($cf[$i],0,7) == "custom_") {
			$customs_fields[] = $cf[$i];
		}
	}
	return $customs_fields;

}


/**
 * get custom columns from table fc_user
 * return array
 */

function get_custom_user_fields() {

	$customs_fields = array();

	$dbh = new PDO("sqlite:".USER_DB);
	$sql = "SELECT * FROM fc_user";
	
	$result = $dbh->query($sql);
	$result = $result->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	
	$result = array_keys($result);
	$cnt_result = count($result);
	
	for($i=0;$i<$cnt_result;$i++) {
		if(substr($result[$i],0,7) == "custom_") {
			$customs_fields[] = $result[$i];
		}
	}
	
	return $customs_fields;

}



/**
 * show editor's switch buttons
 * for plain text, code or wysiwyg
 */

function show_editor_switch($tn,$sub) {

	$btn_wysiwyg_link = "acp.php?tn=$tn&sub=$sub&editor=wysiwyg";
	$btn_code_link = "acp.php?tn=$tn&sub=$sub&editor=code";
	$btn_text_link = "acp.php?tn=$tn&sub=$sub&editor=plain";
	
	if($_SESSION['editor_class'] == "plain") {
		$btn_wysiwyg = 'btn btn-default btn-sm';
		$btn_text = 'btn btn-primary btn-sm disabled';
		$btn_code = 'btn btn-default btn-sm';
	} elseif($_SESSION['editor_class'] == "wysiwyg") {
		$btn_wysiwyg = 'btn btn-primary btn-sm disabled';
		$btn_text = 'btn btn-default btn-sm';
		$btn_code = 'btn btn-default btn-sm';
	} else {
		$btn_wysiwyg = 'btn btn-default btn-sm';
		$btn_text = 'btn btn-default btn-sm';
		$btn_code = 'btn btn-primary btn-sm disabled';
	}
	
	
	echo '<div class="btn-group btn-group-justified">';
	echo '<a href="'.$btn_wysiwyg_link.'" class="'.$btn_wysiwyg.'">WYSIWYG</a>';
	echo '<a href="'.$btn_text_link.'" class="'.$btn_text.'">Text</a>';
	echo '<a href="'.$btn_code_link.'" class="'.$btn_code.'">Code</a>';
	echo '</div>';
	
}



/**
 * show the first xx words of a string
 * return string
 */

function first_words($string,$nbr=5) {
	$short_string = implode(' ', array_slice(explode(' ', $string), 0, $nbr));
	
	if(strlen($short_string) < strlen($string)) {
		$short_string .= ' (...)';
	}
	
	return $short_string;
}


/**
 * get comments from fc_comments
 * delete records from chat that are older than 30 days
 *
 */
 
function fc_get_comments($parent) {

	$dbh = new PDO("sqlite:".CONTENT_DB);
	
	$interval = time() - (30 * 86400); // 30 days
	$count = $dbh->exec("DELETE FROM fc_comments WHERE comment_time < '$interval' AND comment_parent LIKE 'c' ");

	$sql = "SELECT * FROM fc_comments WHERE comment_parent LIKE '$parent' ORDER BY comment_time DESC";
	
	foreach ($dbh->query($sql) as $row) {
  	$result[] = $row;
	}
	
	$dbh = null;
	
	return($result);
}


/**
 * get comment from fc_comments
 * by comment_id
 *
 */

function fc_get_comment($id) {

	$id = (int) $id;

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "SELECT * FROM fc_comments WHERE comment_id = '$id' ";
	
	$result = $dbh->query($sql);
	$result = $result->fetch(PDO::FETCH_ASSOC);
	
	$dbh = null;
	
	return($result);
}


/**
 * write a comment
 * $parent - 'c' for chat
 * $parent - 'p + page_id' for comments on page overview
 * 
 */


function fc_write_comment($author, $message, $parent, $id = NULL) {
	
	$comment_time = time();
	$comment_hash = md5($comment_time);
	$author = strip_tags($author);
	$message = strip_tags($message);
	$parent = strip_tags($parent);
	
	$pdo_fields_update = array(
		'comment_text' => 'STR'
	);
	
	$pdo_fields_new = array(
		'comment_id' => 'INT',
		'comment_hash' => 'STR',
		'comment_parent' => 'STR',
		'comment_time' => 'STR',
		'comment_author' => 'STR',
		'comment_text' => 'STR'
	);
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	
	if(!is_null($id)) {
		$sql = generate_sql_update_str($pdo_fields_update,"fc_comments","WHERE comment_id = '$id' ");
		$sth = $dbh->prepare($sql);
		generate_bindParam_str($pdo_fields_update,$sth);
		$sth->bindParam(':comment_text', $message, PDO::PARAM_STR);
	} else {
		$sql = generate_sql_insert_str($pdo_fields_new,"fc_comments");
		$sth = $dbh->prepare($sql);
		generate_bindParam_str($pdo_fields_new,$sth);
		$sth->bindParam(':comment_hash', $comment_hash, PDO::PARAM_STR);
		$sth->bindParam(':comment_parent', $parent, PDO::PARAM_STR);
		$sth->bindParam(':comment_time', $comment_time, PDO::PARAM_STR);
		$sth->bindParam(':comment_author', $author, PDO::PARAM_STR);
		$sth->bindParam(':comment_text', $message, PDO::PARAM_STR);
	}

	$cnt_changes = $sth->execute();

	$error = print_r($dbh->errorInfo(),true);
	$lastId = $dbh->lastInsertId();
	$dbh = null;
	
	if($cnt_changes == true) {
		return 'success';
	} else {
		return $error;
	}

}



/**
 * get data from fc_media
 * by filename
 *
 */

function fc_get_media_data($filename,$lang=NULL) {

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "SELECT * FROM fc_media WHERE media_file = :media_file AND (media_lang = :lang OR media_lang = '' OR media_lang is null)";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':media_file', $filename, PDO::PARAM_STR);
	$sth->bindParam(':lang', $lang, PDO::PARAM_STR);
	$sth->execute();
	$result = $sth->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	
	return($result);
}

/**
 * delete data from fc_media
 * by filename
 *
 */

function fc_delete_media_data($filename) {
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$query = "DELETE FROM fc_media WHERE media_file = :filename";
	$sth = $dbh -> prepare($query);
	$sth -> bindParam(':filename', $filename, PDO::PARAM_STR);
	$sth->execute();
	$dbh = null;
	
	$record_msg = 'delete media data: <strong>'.basename($filename).'</strong>';
	record_log($_SESSION['user_nick'],$record_msg,"2");

}


/**
 * write data into fc_media
 * check by file name if data already exists
 *
 */

function fc_write_media_data($filename,$title=NULL,$notes=NULL,$keywords=NULL,$text=NULL,$url=NULL,$alt=NULL,$lang=NULL,$credit=NULL,$priority=NULL,$license=NULL) {

	global $languagePack;
	
	if($lang === NULL) {
		$lang = $languagePack;
	}
	
	$pdo_fields_update = array(
		'media_title' => 'STR',
		'media_notes' => 'STR',
		'media_keywords' => 'STR',
		'media_text' => 'STR',
		'media_alt' => 'STR',
		'media_url' => 'STR',
		'media_lang' => 'STR',
		'media_priority' => 'STR',
		'media_credit' => 'STR',
		'media_license' => 'STR'
	);
		
	$pdo_fields_new = array(
		'media_id' => 'INT',
		'media_file' => 'STR',
		'media_title' => 'STR',
		'media_notes' => 'STR',
		'media_keywords' => 'STR',
		'media_text' => 'STR',
		'media_alt' => 'STR',
		'media_url' => 'STR',
		'media_lang' => 'STR',
		'media_priority' => 'STR',
		'media_credit' => 'STR',
		'media_license' => 'STR'
	);

	$dbh = new PDO("sqlite:".CONTENT_DB);
	
	$sql_cnt = "SELECT count(*) FROM fc_media WHERE media_file = :media_file AND (media_lang = :media_lang OR media_lang = '' OR media_lang is null)";
	$sth = $dbh->prepare($sql_cnt);
	$sth->bindParam(':media_file', $filename, PDO::PARAM_STR);
	$sth->bindParam(':media_lang', $lang, PDO::PARAM_STR);
	$sth->execute();
	$cnt = $sth->fetch(PDO::FETCH_NUM);
	
	if($cnt[0] > 0) {
		$modus = 'update';
		$sql_update = generate_sql_update_str($pdo_fields_update,"fc_media","WHERE media_file = :media_file AND (media_lang = :media_lang OR media_lang = '' OR media_lang is null)");
		$sth = $dbh->prepare($sql_update);
		generate_bindParam_str($pdo_fields_update,$sth);
		
	} else {
		$modus = 'new';
		$sql_new = generate_sql_insert_str($pdo_fields_new,"fc_media");
		$sth = $dbh->prepare($sql_new);
		generate_bindParam_str($pdo_fields_new,$sth);
	}
	
	$sth->bindParam(':media_file', $filename, PDO::PARAM_STR);
	$sth->bindParam(':media_title', $title, PDO::PARAM_STR);
	$sth->bindParam(':media_notes', $notes, PDO::PARAM_STR);
	$sth->bindParam(':media_keywords', $keywords, PDO::PARAM_STR);
	$sth->bindParam(':media_text', $text, PDO::PARAM_STR);
	$sth->bindParam(':media_url', $url, PDO::PARAM_STR);
	$sth->bindParam(':media_alt', $alt, PDO::PARAM_STR);
	$sth->bindParam(':media_lang', $lang, PDO::PARAM_STR);
	$sth->bindParam(':media_priority', $priority, PDO::PARAM_STR);
	$sth->bindParam(':media_license', $license, PDO::PARAM_STR);
	$sth->bindParam(':media_credit', $credit, PDO::PARAM_STR);

	$cnt_changes = $sth->execute();
	
	$error = print_r($dbh->errorInfo(),true);
	$lastId = $dbh->lastInsertId();
	//debug_to_console($modus);
	$dbh = null;
	
	if($cnt_changes == true) {
		return 'success';
	} else {
		
		return $error;
	}
	
	
}


/**
 * sort arrays like SQL Results
 * example:
 * $s = fc_array_multisort($pages, 'lang', SORT_ASC, 'page_sort', SORT_ASC, SORT_NATURAL);
 *
 */

function fc_array_multisort(){
	$args = func_get_args();
  $data = array_shift($args);
  foreach($args as $n => $field) {
  	if(is_string($field)) {
			$tmp = array();
      foreach ($data as $key => $row){
      	$tmp[$key] = $row[$field];
        $args[$n] = $tmp;
			}
    }
  }
  $args[] = &$data;
  call_user_func_array('array_multisort', $args);
  return array_pop($args);
}


/**
 * get all labels
 * return as array
 */

function fc_get_labels() {

	$dbh = new PDO("sqlite:".CONTENT_DB);	
	$sql = "SELECT * FROM fc_labels";
	
	foreach ($dbh->query($sql) as $row) {
		$result[] = $row;
	}
	
	return($result);
}



function debug_to_console($data) {
  if(is_array($data) || is_object($data)) {
		echo("<script>console.log('PHP: ".json_encode($data)."');</script>");
	} else {
		echo("<script>console.log('PHP: ".$data."');</script>");
	}
}


?>