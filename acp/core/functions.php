<?php
/**
 * prohibit unauthorized access
 */
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){ 
	die ('<h2>Direct File Access Prohibited</h2>');
}

include_once 'functions_addons.php';
include_once 'functions_database.php';
include_once 'functions_cache.php';
include_once 'functions_index.php';


/**
 * get the core docs
 * from acp/docs/$languagePack
 */

function fc_get_core_docs() {

	global $languagePack;

	$dir_core_docs = 'docs/'.$languagePack;
	$get_core_docs = array_diff(scandir($dir_core_docs), array('..', '.','.DS_Store'));
	foreach($get_core_docs as $docs) {
		$core_docs[] = $dir_core_docs.'/'.$docs;
	}
	
	return $core_docs;
		
}

/**
 * get the modules docs
 * from modules/xxx.mod/docs/$languagePack
 */

function fc_get_modules_docs() {

	global $languagePack;
	$get_modules = get_all_moduls();
	
	foreach($get_modules as $k => $v) {
		$mod_dir = '../modules/'.$get_modules[$k]['folder'].'/docs/'.$languagePack;
		$get_modules_docs = array_diff(scandir($mod_dir), array('..', '.','.DS_Store'));
		
			foreach($get_modules_docs as $docs) {
				$modules_docs[] = $mod_dir.'/'.$docs;
			}
	}
	
	return $modules_docs;	
}

/**
 * get the themes docs
 * from styles/theme/docs/$languagePack
 */

function fc_get_themes_docs() {

	global $languagePack;
	$get_themes = get_all_templates();
	
	foreach($get_themes as $theme) {
		$theme_dir = '../styles/'.$theme.'/docs/'.$languagePack;
		$get_theme_docs = array_diff(scandir($theme_dir), array('..', '.','.DS_Store'));
		
		foreach($get_theme_docs as $docs) {
			$theme_docs[] = $theme_dir.'/'.$docs;
		}
	}
	return $theme_docs;	
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
	global $db_content;
	
	$prefs = $db_content->get("fc_preferences", "*", [
		"prefs_id" => 1
	]);
	
	return $prefs;
}


/**
 * hook in
 * inject code from addons
 * $position (string)	-> where should the code be injected
 * $data (array)			-> data which will be passed to the hook-script
 *
 * example: fc_get_hook('page_updated',$_POST);
 *
 * Hooks (will be expanded soon):
 * - page_updated
 * - dashboard_listed_all_addons
 */

function fc_get_hook($posion,$data) {
	
	global $all_mods;	
	$hook = basename($posion);
	
	foreach($all_mods as $mod) {
		
		$hook_file = '../modules/'.$mod['folder'].'/hooks/'.$hook.'.php';
		if(is_file($hook_file)) {
			include $hook_file;
		}
		
	}
	
	
}


/**
 * get all user groups
 * return as array
 */

function get_all_groups() {
	
	global $db_user;
	
	$groups = $db_user->select("fc_groups", "*", [
	"ORDER" => ["group_id" => "ASC"]
	]);
	
	return $groups;
}


/**
 * get all admins
 * return as array
 */

function get_all_admins() {

	global $db_user;
		
	$admins = $db_user->select("fc_user", "*", [
	"user_class" => "administrator"
	]);
	
	return $admins;
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
 * show all images from images folder and it's subfolders
 * optional filter by prefix
 * return array
 */

function fc_get_all_images_rec($prefix='',$dir='') {

	global $img_path;
	$images = array();
	
	if($dir == '') {
		$dir = "../$img_path";
	}
	
	$scan_dir = array_diff(scandir($dir), array('..', '.','.DS_Store'));
	$types = array('jpg','jpeg','png','gif');
	
	foreach($scan_dir as $key => $file) {
		
		if(is_dir($dir . '/' . $file)) {
			$images[] = fc_get_all_images_rec("$prefix",$dir . '/' . $file);
			continue;
		}
		$suffix = substr($file, strrpos($file, '.') + 1);
		
		if(in_array($suffix, $types)) {
			
			if($prefix != '') {
				if(substr(basename($file), 0,strlen($prefix)) !== $prefix) {
					continue;
				}
			}
			
			$images[] = $dir . '/' . $file;
		  
		}
	}
		
	
	$images = fc_flatten_array($images);
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

/**
 * get all (sub)directories from directory (recursive)
 * return array
 */

function fc_get_dirs_rec($dir) { 
   
   $result = array(); 

   $cdir = scandir($dir); 
   foreach ($cdir as $key => $value) { 
      if(in_array($value,array('..', '.','.DS_Store','index.html'))) {
	      continue;
	    }
      if(is_dir($dir . '/' . $value)) {
	      $result[] = $dir.'/'.$value;
	      $result[] = fc_get_dirs_rec($dir . '/' . $value); 
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
 * get size of a directory
 * returning bytes
 */

function fc_dir_size($dir) {
    $size = 0;
    foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
        $size += is_file($each) ? filesize($each) : fc_dir_size($each);
    }
    return $size;
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
	echo '<div class="'.$style.' alert-dismissible" role="alert">';
	echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>';
	echo $msg;
	echo '</div>';
}





/**
 * Get Page Impression
 */

function get_page_impression($pid) {
	
	global $db_statistics;

	$counter = $db_statistics->get("hits", "counter", [
		"page_id" => $pid
	]);
		
	return $counter;
}


/**
 * write a log message
 */

function record_log($log_trigger = 'system', $log_entry, $log_priority = '0') {

	$log_time = time();
	
	global $db_statistics;
	
	$db_statistics->insert("log", [
		"log_time" => $log_time,
		"log_trigger" => $log_trigger,
		"log_entry" => $log_entry,
		"log_priority" => $log_priority
	]);

}



/**
 * show log entries
 * delete records that are older than 30 days
 */

function show_log($nbr) {
	
	global $db_statistics;
	$interval = time() - (30 * 86400); // 30 days
	
	$del = $db_statistics->delete("log", [
	"log_time[<]" => $interval
	]);

	$count = $del->rowCount();
	
	if($count > 0) {
		echo "<div class='alert alert-info'>Logs removed ($count)</div>";
	}

	$result = $db_statistics->select("log", "*", [
		"ORDER" => ["log_id" => "DESC"]
	]);

	$cnt_result = count($result);

	for($i=0;$i<$cnt_result;$i++) {

		$time = date("H:i:s",$result[$i]['log_time']);
		$date = date("d.m.Y",$result[$i]['log_time']);
		$log_priority = 'log_priority'.$result[$i]['log_priority'];
		
		echo '<dl class="row dl-logfile">';
		echo '<dt class="col-sm-3"><span class="priority-indicator '.$log_priority.'" title="'.$result[$i]['log_priority'].'"></span>'.$date.' '.$time.'</dt>';
		echo '<dd class="col-sm-9">'.$result[$i]['log_trigger'].' - '. $result[$i]['log_entry'] .'</dd>';
		echo '</dl>';

	}
	
	if($cnt_result < 1) {
		echo "<div class='alert alert-info'>No entries.</div>";
	}

}


/**
 * add new item to the feeds table
 */

function add_feed($title, $text, $url, $sub_id, $feed_name, $time = NULL) {
	
	global $db_content;
	$interval = time() - (30 * 86400); // 30 days

	if(is_null($time)) {
		$time = time();
	}
		
	/* romove old entries */
	$db_content->delete("fc_feeds", [
	"feed_time[<]" => $interval
	]);
	/* remove duplicates */
	$db_content->delete("fc_feeds", [
	"feed_subid" => $sub_id
	]);
	
	$db_content->insert("fc_feeds", [
		"feed_subid" => "$sub_id",
		"feed_time" => "$time",
		"feed_name" => "$feed_name",
		"feed_title" => "$title",
		"feed_text" => "$text",
		"feed_url" => "$url"
	]);

}





/**
 * Generate XML Sitemap
 */

function generate_xml_sitemap() {

	global $languagePack;
	global $fc_base_url;
	global $db_content;
	
	$file = "../sitemap.xml";
	$tpl_sitemap = file_get_contents('templates/sitemap.tpl');
	$tpl_sitemap_urlset = file_get_contents('templates/sitemap_urlset.tpl');

	
	$prefs_xml_sitemap = $db_content->get("fc_preferences", "prefs_xml_sitemap", [
	"prefs_id" => 1
	]);
		
	if($prefs_xml_sitemap == "on") {
	

		$results = $db_content->select("fc_pages", "*", [
			"ORDER" => ["page_lastedit" => "DESC"]
		]);
		
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
 * get custom columns from table fc_pages
 * return array
 */

function get_custom_fields() {
	
	global $db_content;

	$customs_fields = array();
	
	$cf = $db_content->get("fc_pages", "*");
	
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
	
	global $db_user;

	$customs_fields = array();
	
	$cf = $db_user->get("fc_user", "*");

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
 * show editor's switch buttons
 * for plain text, code or wysiwyg
 */

function show_editor_switch($tn,$sub) {

	$btn_wysiwyg_link = "acp.php?tn=$tn&sub=$sub&editor=wysiwyg";
	$btn_code_link = "acp.php?tn=$tn&sub=$sub&editor=code";
	$btn_text_link = "acp.php?tn=$tn&sub=$sub&editor=plain";
	
	if($_SESSION['editor_class'] == "plain") {
		$btn_wysiwyg = 'btn btn-light btn-sm';
		$btn_text = 'btn btn-primary btn-sm disabled';
		$btn_code = 'btn btn-light btn-sm';
	} elseif($_SESSION['editor_class'] == "wysiwyg") {
		$btn_wysiwyg = 'btn btn-primary btn-sm disabled';
		$btn_text = 'btn btn-light btn-sm';
		$btn_code = 'btn btn-light btn-sm';
	} else {
		$btn_wysiwyg = 'btn btn-light btn-sm';
		$btn_text = 'btn btn-light btn-sm';
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
	
	global $db_content;
	
	$media_data = $db_content->get("fc_media","*",[

		"AND" => [
			"media_file[~]" => "$filename",
			"media_lang[~]" => "$lang"
		]
	]);
	
	return $media_data;
}

/**
 * delete data from fc_media
 * by filename
 *
 */

function fc_delete_media_data($filename) {
	
	global $db_content;
	
	$db_content->delete("fc_media", [
		"AND" => [
		"media_file" => "$filename"
		]
	]);
	
	$record_msg = 'delete media data: <strong>'.basename($filename).'</strong>';
	record_log($_SESSION['user_nick'],$record_msg,"2");

}


/**
 * write data into fc_media
 * check by file name if data already exists
 *
 */

function fc_write_media_data($filename,$title=NULL,$notes=NULL,$keywords=NULL,$text=NULL,$url=NULL,$alt=NULL,$lang=NULL,$credit=NULL,$priority=NULL,$license=NULL,$lastedit=NULL,$filesize=NULL,$version=NULL) {

	global $db_content;
	global $languagePack;
	
	if($lang === NULL) {
		$lang = $languagePack;
	}
		
	$filetype = mime_content_type("../$filename");
	
	$cnt = $db_content->count("fc_media", [
		"AND" => [
		"media_file" => "$filename",
		"media_lang" => "$lang"
		]
	]);
	
	$columns = [
		"media_title" => "$title",
		"media_notes" => "$notes",
		"media_keywords" => "$keywords",
		"media_text" => "$text",
		"media_alt" => "$alt",
		"media_url" => "$url",
		"media_lang" => "$lang",
		"media_priority" => "$priority",
		"media_credit" => "$credit",
		"media_license" => "$license",
		"media_version" => "$version",
		"media_filesize" => "$filesize",
		"media_lastedit" => "$lastedit",
		"media_type" => "$type"		
	];
	
	if($cnt > 0) {
		$modus = 'update';
		//$sql_update = generate_sql_update_str($pdo_fields_update,"fc_media","WHERE media_file = :media_file AND (media_lang = :media_lang OR media_lang = '' OR media_lang is null)");
		//$sth = $dbh->prepare($sql_update);
		//generate_bindParam_str($pdo_fields_update,$sth);
		
		$cnt_changes = $db_content->update("fc_media", $columns, [
			"AND" => [
				"media_file" => "$filename",
				"media_lang" => "$lang"
			]
		]);
		
	} else {
		$modus = 'new';
		//$sql_new = generate_sql_insert_str($pdo_fields_new,"fc_media");
		//$sth = $dbh->prepare($sql_new);
		//generate_bindParam_str($pdo_fields_new,$sth);
		
			$columns["media_file"] = "$filename";
			
		$cnt_changes = $db_content->insert("fc_media", $columns, [
			"AND" => [
				"media_file" => "$filename",
				"media_lang" => "$lang"
			]
		]);
		
		$lastId = $db_content->id();
		
	}
	

	/*
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
	$sth->bindParam(':media_version', $version, PDO::PARAM_STR);
	$sth->bindParam(':media_filesize', $filesize, PDO::PARAM_STR);
	$sth->bindParam(':media_lastedit', $lastedit, PDO::PARAM_STR);
	$sth->bindParam(':media_type', $filetype, PDO::PARAM_STR);

	$cnt_changes = $sth->execute();
	*/
	
	//$error = print_r($dbh->errorInfo(),true);
	//$lastId = $dbh->lastInsertId();
	//debug_to_console($modus);
	//$dbh = null;
	
	if($cnt_changes->rowCount() > 0) {
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

	global $db_content;

	$customs_fields = array();
	$labels = $db_content->select("fc_labels", "*");
	
	return $labels;
}



function debug_to_console($data) {
  if(is_array($data) || is_object($data)) {
		echo("<script>console.log('PHP: ".json_encode($data)."');</script>");
	} else {
		echo("<script>console.log('PHP: ".$data."');</script>");
	}
}



?>