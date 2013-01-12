<?php

//prohibit unauthorized access
require("core/access.php");


/**
 * get all installed language files
 * return as array
 */

function get_all_languages() {

	$mdir = "../lib/lang";
	$cntLangs = 0;
	
	$handle = opendir($mdir);
	while ($file = readdir($handle)) {
	
	if(eregi("^\.{1,2}$",$file)) {
		continue;
	}
	
		if(is_dir("$mdir/$file")) {
			if(is_file("$mdir/$file/index.php")) {
				include("$mdir/$file/index.php");
				$arr_lang[$cntLangs][lang_sign] = "$lang_sign";
				$arr_lang[$cntLangs][lang_desc] = "$lang_desc";
				$arr_lang[$cntLangs][lang_folder] = "$file";
				$cntLangs++;
			}
		}
	
	}
	
	@closedir($handle);
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
	
	$handle = opendir($mdir);
	while ($file = readdir($handle)) {
	
	if(eregi("^\.{1,2}$",$file)) {
		continue;
	}
	
		if(is_dir("$mdir/$file"))	{
			if(is_file("$mdir/$file/info.inc.php")) {
				include("$mdir/$file/info.inc.php");
				$arr_iMods[$cntMods][name] = "$mod[name]";
				$arr_iMods[$cntMods][folder] = "$file";
				$cntMods++;
			}
		}
	
	}
	
	@closedir($handle);
	return($arr_iMods);
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

	// connect to database
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
	
	$handle = opendir($sdir);
		while ($file = readdir($handle)) {
			if(eregi("^\.{1,2}$",$file)) {
				continue;
			}
	
			//fill array
			if(is_dir("$sdir/$file")) {
				$arr_Styles[] = "$file";
			}
	
	}
	
	@closedir($handle);
	return($arr_Styles);

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

	$a = array('ä','ö','ü','ß',' - ',' + ','_',' / ','/'); 
	$b = array('ae','oe','ue','ss','-','-','-','-','-');
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
 * PRINT SYSTEM MESSAGE
 */

function print_sysmsg($msg) {

	$type = "{OKAY}";
	$pos = stripos($msg, $type);

	if($pos !== false) {
		$style = "alert alert-success";
	} else {
		$style = "alert alert-error";
	}

	$msg = substr(strstr($msg, '}'), 2);
	echo"<div class=\"$style\"><p>$msg</p></div>";

}





/**
 * Get Page Impression
 */


function get_page_impression($pid) {

		$dbh = new PDO("sqlite:".STATS_DB);

		$sql = $dbh->query("SELECT counter FROM hits WHERE page_id = '$pid' ");
		$result = $sql->fetch(PDO::FETCH_ASSOC);

		$dbh = null;

		$counter = $result[counter];
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

		$time = date("H:i:s",$result[$i][log_time]);
		$date = date("d.m.Y",$result[$i][log_time]);
		$log_priority = $result[$i][log_priority];

		echo"<div class='log_priority$log_priority'>";
		echo'<div class="row-fluid">';
		echo'<div class="span3">';
		echo "$date $time";
		echo'</div>';
		echo'<div class="span9">';
		echo $result[$i][log_trigger] . " - " . $result[$i][log_entry];
		echo'</div>';
		echo'</div>';
		echo"</div>\n";

	} // eol $i



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
	global $fc_mod_rewrite;
	
	$file = "../sitemap.xml";
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	
	$sqlgp = "SELECT prefs_xml_sitemap FROM fc_preferences WHERE prefs_id = 1";
	$prefs_xml_sitemap = $dbh->query($sqlgp)->fetchColumn(); // -> returns on|off
	
	
	if($prefs_xml_sitemap == "on") {
	
		$sql = "SELECT page_id, page_language, page_permalink, page_lastedit, page_status	FROM fc_pages
				WHERE page_status = 'public' AND page_language = '$languagePack' ORDER BY page_lastedit DESC ";
		    
		$results = $dbh->query($sql)->fetchAll();
		$dbh = null;
		
		$cnt_results = count($results);
		
		/* generate content for xml file */
		
		$url_set = "";
		
		for($i=0;$i<$cnt_results;$i++) {
		
			$page_id = $results[$i]['page_id'];
			$page_permalink = $results[$i]['page_permalink'];
			$page_lastedit = date("Y-m-d",$results[$i][page_lastedit]);
			
			if($fc_mod_rewrite == "permalink") {
				$link = "http://$_SERVER[HTTP_HOST]" . FC_INC_DIR . "/$page_permalink";
			} else {
				$link = "http://$_SERVER[HTTP_HOST]" . FC_INC_DIR . "/index.php?p=$page_id";
			}
			
			$link = str_replace("/acp","",$link);
				
			$url_set .= "
			<url>
				<loc>$link</loc>
				<lastmod>$page_lastedit</lastmod>
			</url>";
			
		}
		
		$file_content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"
		        xmlns:image=\"http://www.sitemaps.org/schemas/sitemap-image/1.1\"
		        xmlns:video=\"http://www.sitemaps.org/schemas/sitemap-video/1.1\">
				$url_set
		</urlset>";
			
		file_put_contents($file, $file_content, LOCK_EX);
	
	}


} // eo function






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
			WHERE page_status != 'draft' AND page_language = '$languagePack'
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
	
		$set_title = str_replace(" ","_",$result[$i][page_title]);
		
		if($fc_mod_rewrite == "on") {
			$result[$i][link] = FC_ROOT . "/" . $result[$i][page_linkname] ."/". $result[$i][page_id] ."/". $set_title;
		} elseif ($fc_mod_rewrite == "off") {
			$result[$i][link] = "index.php?p=" . $result[$i][page_id];
		} elseif ($fc_mod_rewrite == "permalink") {
			$result[$i][link] = FC_ROOT . "/" . $result[$i][page_permalink];
		}
	
		$string .= "\$arr_lastedit[$i][page_id] = \"" . $result[$i][page_id] . "\";\n";
		$string .= "\$arr_lastedit[$i][link] = \"" . $result[$i][link] . "\";\n";
		$string .= "\$arr_lastedit[$i][page_title] = \"" . $result[$i][page_title] . "\";\n";
		$string .= "\$arr_lastedit[$i][page_linkname] = \"" . $result[$i][page_linkname] . "\";\n";
	
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

$sql = "SELECT page_meta_keywords
		FROM fc_pages
		WHERE page_status != 'draft' AND page_language = '$languagePack' ";

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
	$page_keywords .= "<span style=\"font-size:$fz%;\"><a href='" . FC_ROOT . "/index.php?p=search&amp;s=$skey'>$key</a></span> ";

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
 * check in active modules
 * generate list for .. /cache/ ..
 */


function mods_check_in() {

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "SELECT * FROM fc_pages";
	
	foreach ($dbh->query($sql) as $row) {
		$result[] = $row;
	}  
	
	$dbh = null;
	
	$count_result = count($result);
	
	$x = 0;
	for($i=0;$i<$count_result;$i++) {
	
		if($result[$i][page_modul] != "") {
			$string .= "\$active_mods[$x][page_modul] = \"" . $result[$i][page_modul] . "\";\n";
			$string .= "\$active_mods[$x][page_permalink] = \"" . $result[$i][page_permalink] . "\";\n";
			$x++;
		}
	
	}
	
	$str = "<?php\n$string\n?>";
	
	
		$file = "../" . FC_CONTENT_DIR . "/cache/active_mods.php";
		file_put_contents($file, $str, LOCK_EX);



} //  eo function





/**
 * get custom columns from table fc_pages
 * return array
 */



function get_custom_fields() {

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "SELECT * FROM fc_pages";
	
	$result = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
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
 * for plain text or wysiwyg
 */


function show_editor_switch($tn,$sub) {

	if($_SESSION[editor_class] == "plain") {
		$btn_primary_wysiwyg = '';
		$btn_wysiwyg_link = "$_SERVER[PHP_SELF]?tn=$tn&sub=$sub&editor=toggle";
		$btn_text_link = "#";
		$btn_primary_text = 'btn-inverse disabled';
	} else {
		$btn_primary_wysiwyg = 'btn-inverse disabled';
		$btn_text_link = "$_SERVER[PHP_SELF]?tn=$tn&sub=$sub&editor=toggle";
		$btn_wysiwyg_link = "#";
		$btn_primary_text = '';
	}
	
	
	echo '<div class="btn-group" style="float:right;">';
	echo "<a href='$btn_wysiwyg_link' class='btn btn-small $btn_primary_wysiwyg'>WYSIWYG</a>";
	echo "<a href='$btn_text_link' class='btn btn-small $btn_primary_text'>Text</a>";
	echo '</div>';
	echo '<div class="clearfix"></div>';
	
}





?>