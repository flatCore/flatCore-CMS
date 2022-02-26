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
	$get_modules_docs = array();
	
	foreach($get_modules as $k => $v) {
		$mod_dir = '../modules/'.$get_modules[$k]['folder'].'/docs/'.$languagePack;
		if(is_dir($mod_dir)) {
			$get_modules_docs = array_diff(scandir($mod_dir), array('..', '.','.DS_Store'));
		}
		
		
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
	$get_theme_docs = array();
	
	foreach($get_themes as $theme) {
		$theme_dir = '../styles/'.$theme.'/docs/'.$languagePack;
		if(is_dir($theme_dir)) {
			$get_theme_docs = array_diff(scandir($theme_dir), array('..', '.','.DS_Store'));
		}
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
 * @deprecated - this function will be removed in the near future. Use fc_get_preferences() from global functions.
 */
 
function get_preferences() {
	global $db_content;
	
	$prefs = $db_content->get("fc_preferences", "*", [
		"prefs_id" => 1
	]);
	
	return $prefs;
}

/**
 * write preferences
 * table fc_options
 */

function fc_write_option($data,$module) {
	
	global $db_content;

	foreach($data as $key => $val) {
		
		if($key == '') {
			continue;
		}
		
		if(substr($key, 0, 6 ) !== "prefs_") {
			continue;
		}
		
		/* check if exists */
		$entry = $db_content->get("fc_options","*", [
			"option_key" =>  $key,
			"option_module" => $module
		]);
		
		if($entry['option_key'] != '') {


			$data = $db_content->update("fc_options", [
				"option_value" =>  $val,
			], [
				"AND" => [
					"option_key" => $key,
					"option_module" => $module
				]
			]);
			
		} else {

			$data = $db_content->insert("fc_options", [
				"option_value" =>  $val,
				"option_key" => $key,
				"option_module" => $module
			]);
			
		}
				
		
		
	}	
	
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

function fc_get_hook($position,$data) {
	
	global $all_mods;	
	$hook = basename($position);
	
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
 * format time and date
 * formatting is set in preferences
 * prefs_dateformat, prefs_timeformat
 */
 
 function fc_format_datetime($timestring) {
	 
	 global $lang;
	 global $prefs_timeformat;
	 global $prefs_dateformat;
	 
	 $date = date($prefs_dateformat,$timestring);
	 
	 if($date == date("$prefs_dateformat", time())) {
		 $str_date = $lang['label_datetime_today'];
	 } else if($date == date("$prefs_dateformat", time() - (24 * 60 * 60))) {
		 $str_date = $lang['label_datetime_yesterday'];
	 } else {
		 $str_date = $date;
	 }
	 
	 $time = date($prefs_timeformat,$timestring);
 
	 return $str_date. ' ' .$time;
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
	echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>';
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

function record_log($log_trigger, $log_entry, $log_priority = '0') {

	$log_time = time();
	
	global $db_statistics;
	
	if(empty($log_trigger)) {
		$log_trigger = 'undefined';
	}
	
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
	global $lang;
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
		echo '<div class="alert alert-secondary">'.$lang['msg_no_entries_so_far'].'</div>';
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
			"AND" => [
				"page_status[!]" => ["draft","private","ghost"]
			],
			"ORDER" => [
				"page_lastedit" => "DESC"
			]
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
 * get data from fc_media
 * all files bei type f.e. 'image'
 */
 
 function fc_get_all_media_data($type) {
	 
	global $db_content;
	
	$media_data = $db_content->select("fc_media","*",[

		"AND" => [
			"media_type[~]" => "$type"
		],
		"ORDER" => [
			"media_upload_time" => "DESC"
			]
	]);
	
	return $media_data;
	 
	 
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
 * get data from fc_media
 * by id
 *
 */

function fc_get_media_data_by_id($id) {
	
	global $db_content;
	
	$media_data = $db_content->get("fc_media","*",[

		"media_id" => $id
	]);
	
	return $media_data;
}


/**
 * clear all thumbnails
 * and subdirectories in /content/images_tmb/ 
 */

function fc_clear_thumbs_directory($dir=NULL) {

	if($dir == NULL) {
		$dir = '../content/images_tmb/';
	}
	
	/* check if we are in the thumbnail directory */
	if(substr($dir,0,22) != '../content/images_tmb/') {
		return 'Sorry. No permissions to delete in:' . $dir;
	} else {
		
   if(is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if(filetype($dir."/".$object) == "dir") {
         		fc_clear_thumbs_directory($dir."/".$object);
         	} else {
	        	unlink($dir."/".$object);
         	}
       }
     }
     reset($objects);
     if($dir != '../content/images_tmb/') {
	     rmdir($dir);
     }
     
   }
		
	}
	
	


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

function fc_write_media_data($filename,$title=NULL,$notes=NULL,$keywords=NULL,$text=NULL,$url=NULL,$alt=NULL,$lang=NULL,$credit=NULL,$priority=NULL,$license=NULL,$lastedit=NULL,$filesize=NULL,$version=NULL,$labels=NULL) {

	global $db_content;
	global $languagePack;
	
	if($lang === NULL) {
		$lang = $languagePack;
	}
	
	$title = fc_return_clean_value($title);
	$notes = fc_return_clean_value($notes);
	$keywords = fc_return_clean_value($keywords);
	$text = fc_return_clean_value($text);
	$alt = fc_return_clean_value($alt);
	$priority = (int) $priority;
	$credit = fc_return_clean_value($credit);
	$license = fc_return_clean_value($license);
	$version = fc_return_clean_value($version);
	
	/* labels */
	if(is_array($labels)) {
		sort($labels);
		$string_labels = implode(",", $labels);
	} else {
		$string_labels = "";
	}	
		
	$filetype = mime_content_type(realpath($filename));
	
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
		"media_type" => "$filetype",
		"media_labels" => "$string_labels"
	];
	
	if($cnt > 0) {
		$modus = 'update';
		
		$cnt_changes = $db_content->update("fc_media", $columns, [
			"AND" => [
				"media_file" => "$filename",
				"media_lang" => "$lang"
			]
		]);
		
	} else {
		$modus = 'new';
		$columns["media_file"] = "$filename";
		$cnt_changes = $db_content->insert("fc_media", $columns);
		$lastId = $db_content->id();
	}
	
	if($cnt_changes->rowCount() > 0) {
		return 'success';
	} else {
		
		return $error;
	}

}


/**
 * remove duplicate entries from multidimensional array
 * we use this for fc_media entries
 * because we only want one result per upload
 * not an result for every language
 * $array = multidimensional array
 * $key = key you want to check for duplicates
 *
 * https://www.php.net/manual/de/function.array-unique.php#116302
 *
 */

function fc_unique_multi_array($array, $key) { 
    $temp_array = array(); 
    $i = 0; 
    $key_array = array(); 
    
    foreach($array as $val) { 
      if(!in_array($val[$key], $key_array)) { 
      	$key_array[$i] = $val[$key]; 
        $temp_array[$i] = $val; 
      } 
      $i++; 
    }
    
    // re-index
    $temp_array= array_values($temp_array);
    return $temp_array; 
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



/**
 * generate an select-image widget
 * $images array()
 * $selected array()
 * return html string
 * post name => picker$id_images[]
 */

function fc_select_img_widget($images,$seleced_img,$prefix='',$id=1) {
	
	global $lang;
	
	if(!array($seleced_img)) {
		$seleced_img = array();
	}
	
	$choose_images  = '<div class="scroll-container">';
	$choose_images .= '<select multiple="multiple" class="image-picker show-html" name="picker'.$id.'_images[]">';
	
	/* if we have selected images, show them first */
	if(count($seleced_img)>0) {
		$choose_images .= '<optgroup label="'.$lang['label_image_selected'].'">';
		foreach($seleced_img as $sel_images) {
			if(is_file("$sel_images")) {
				$choose_images .= '<option data-img-src="'.$sel_images.'" value="'.$sel_images.'" selected>'.basename($sel_images).'</option>'."\r\n";
			}
		}
		$choose_images .= '</optgroup>'."\r\n";
	}
	
	$cnt_images = count($images);
	
	for($i=0;$i<$cnt_images;$i++) {
		
		$img_filename = basename($images[$i]['media_file']);
		$image_name = $images[$i]['media_file'];
		$image_tmb_name = $images[$i]['media_thumb'];
		$imgsrc = "../$img_path/$images[$i][media_file]";
		$lastedit = $images[$i]['media_lastedit'];
		$lastedit_year = date('Y',$lastedit);
		$filemtime = $lastedit_year;
		
		if($prefix != '') {
			if((strpos($image_name, $prefix)) === false) {
				continue;
			}
		}
		
		if(file_exists($image_tmb_name)) {
			$preview = $image_tmb_name;
		} else {
			$preview = $image_name;
		}
		
		/* new label for each year */
		if(date('Y',$images[$i-1]['media_lastedit']) != $lastedit_year) {	
			if($i == 0) {
				$choose_images .= '<optgroup label="'.$filemtime.'">'."\r\n";
			} else {
				$choose_images .= '</optgroup><optgroup label="'.$filemtime.'">'."\r\n";
			}
		}
		
		if(!in_array($image_name, $seleced_img)) {
			$choose_images .= '<option data-img-src="'.$preview.'" value="'.$image_name.'">'.$img_filename.'</option>'."\r\n";
		}
		
	}
	$choose_images .= '</optgroup>'."\r\n";
	$choose_images .= '</select>'."\r\n";
	$choose_images .= '</div>';
	
	return $choose_images;
	
}


function fc_list_gallery_thumbs($gid) {
	
	global $db_posts;
	global $icon;
	$gid = (int) $gid;
	
	
	$date = $db_posts->get("fc_posts","post_date", [
	"post_id" => $gid
	]);
	
	$filepath = '../content/galleries/'.date('Y',$date).'/gallery'.$gid.'/*_tmb.jpg';
	$thumbs_array = glob("$filepath");
	arsort($thumbs_array);

	$thumbs = '';
	foreach($thumbs_array as $tmb) {
		$thumbs .= '<div class="tmb">';
		$thumbs .= '<div class="tmb-preview"><img src="'.$tmb.'" class="img-fluid"></div>';
		$thumbs .= '<div class="tmb-actions d-flex btn-group">';
		$thumbs .= '<button type="submit" name="sort_tmb" value="'.$tmb.'" class="btn btn-sm btn-fc w-100">'.$icon['angle_up'].'</button>';
		$thumbs .= '<button type="submit" name="del_tmb" value="'.$tmb.'" class="btn btn-sm btn-danger w-50">'.$icon['trash_alt'].'</button>';
		$thumbs .= '</div>';
		$thumbs .= '</div>';
	}
	
	
	$str = '';
	$str .= $thumbs;
	
	
	return $str;
		
}



function fc_rename_gallery_image($thumb) {
	
	$timestring = microtime(true);
	
	$path_parts = pathinfo($thumb);
	$dir = $path_parts['dirname'].'/';
	$tmb = $dir.$path_parts['basename'];
	$img = str_replace("_tmb", "_img", $tmb);
	
	$new_tmb = $dir.$timestring.'_tmb.jpg';
	$new_img = $dir.$timestring.'_img.jpg';

	
	rename("$tmb", "$new_tmb");
	rename("$img", "$new_img");
	
}



function fc_remove_gallery($id,$dir) {

	$fp = '../content/galleries/'.$dir.'/gallery'.$id.'/';
	$files = glob("$fp*jpg");

	foreach($files as $file) {
		unlink($file);
	}
	
	rmdir($fp);
	
	
}



/**
 * please use the new function fc_create_thumbnail()
 */
 
function fc_create_tmb($img_src, $tmb_name, $tmb_width, $tmb_height, $tmb_quality) {
	
	global $img_tmb_path;
	
	/* thumbnail directories */
	$tmb_dir = '../'.$img_tmb_path;
	$tmb_dir_year = $tmb_dir.'/'.date('Y',time());
	$tmb_destination = $tmb_dir_year.'/'.date('m',time());
	if(!is_dir($tmb_dir_year)) {
		mkdir($tmb_dir_year);
	}
	if(!is_dir($tmb_destination)) {
		mkdir($tmb_destination);
	}
	
	$arr_image_details	= GetImageSize("$img_src");
	$original_width		= $arr_image_details[0];
	$original_height	= $arr_image_details[1];
	$a = $tmb_width / $tmb_height;
  $b = $original_width / $original_height;
	
	
	if ($a<$b) {
     $new_width = $tmb_width;
     $new_height	= intval($original_height*$new_width/$original_width);
  } else {
     $new_height = $tmb_height;
     $new_width	= intval($original_width*$new_height/$original_height);
  }
	
	if(($original_width <= $tmb_width) AND ($original_height <= $tmb_height)) {
	  $new_width = $original_width;
	  $new_height = $original_height;
  }
  
	if($arr_image_details[2]==1) { $imgt = "imagegif"; $imgcreatefrom = "imagecreatefromgif";  }
	if($arr_image_details[2]==2) { $imgt = "imagejpeg"; $imgcreatefrom = "imagecreatefromjpeg";  }
	if($arr_image_details[2]==3) { $imgt = "imagepng"; $imgcreatefrom = "imagecreatefrompng";  }
	
	
	if($imgt) { 
		$old_image	= $imgcreatefrom("$img_src");
		$new_image	= imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($new_image,$old_image,0,0,0,0,$new_width,$new_height,$original_width,$original_height);
		imagejpeg($new_image,"$tmb_destination/$tmb_name",$tmb_quality);
		imagedestroy($new_image);
	}
	
}

/**
 * $img_src = path to original image
 * $tmb_name = name of the new thumbnail
 * $tmb_dir = directory where the thumb should be saved
 * $tmb_width $tmb_height $tmb_quality = size and quality
 */

function fc_create_thumbnail($img_src, $tmb_name, $tmb_dir=NULL, $tmb_width=100, $tmb_height=100, $tmb_quality=50) {
	
	global $img_tmb_path;
		
	/* thumbnail directories */
	if($tmb_dir == NULL) {
		$dir = '../'.$img_tmb_path;
		$dir_year = $tmb_dir.'/'.date('Y',time());
		$tmb_destination = $tmb_dir_year.'/'.date('m',time());
	} else {
		$tmb_destination = $tmb_dir;
	}
	
	if(!is_dir($tmb_destination)) {
		mkdir($tmb_destination,0777,true);
	}
	
	$arr_image_details	= GetImageSize("$img_src");
	$original_width		= $arr_image_details[0];
	$original_height	= $arr_image_details[1];
	$a = $tmb_width / $tmb_height;
  $b = $original_width / $original_height;
	
	
	if ($a<$b) {
     $new_width = $tmb_width;
     $new_height	= intval($original_height*$new_width/$original_width);
  } else {
     $new_height = $tmb_height;
     $new_width	= intval($original_width*$new_height/$original_height);
  }
	
	if(($original_width <= $tmb_width) AND ($original_height <= $tmb_height)) {
	  $new_width = $original_width;
	  $new_height = $original_height;
  }
  
	if($arr_image_details[2]==1) { $imgt = "imagegif"; $imgcreatefrom = "imagecreatefromgif";  }
	if($arr_image_details[2]==2) { $imgt = "imagejpeg"; $imgcreatefrom = "imagecreatefromjpeg";  }
	if($arr_image_details[2]==3) { $imgt = "imagepng"; $imgcreatefrom = "imagecreatefrompng";  }
	
	
	if($imgt) { 
		$old_image	= $imgcreatefrom("$img_src");
		$new_image	= imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($new_image,$old_image,0,0,0,0,$new_width,$new_height,$original_width,$original_height);
		imagejpeg($new_image,"$tmb_destination/$tmb_name",$tmb_quality);
		imagedestroy($new_image);
	}
}




function debug_to_console($data) {
  if(is_array($data) || is_object($data)) {
		echo("<script>console.log('PHP: ".json_encode($data)."');</script>");
	} else {
		echo("<script>console.log('PHP: ".$data."');</script>");
	}
}



?>