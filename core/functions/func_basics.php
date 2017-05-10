<?php
	

/**
 * check if username exists in usergroup
 */

function is_user_in_group($user_id,$user_group) {

	global $fc_db_user;

	$dbh = new PDO("sqlite:$fc_db_user");

	$sql = "SELECT group_name, group_user FROM fc_groups
					WHERE group_name = :user_group";
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(':user_group', $user_group, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
   
	$arr_users = explode(" ", $result['group_user']);

	if(in_array($_SESSION['user_id'],$arr_users)) {
		$in_group = "true";
	} else {
		$in_group = "false";
	}

	return $in_group;

}



/**
 * buffer scripts placed in the plugin folder
 * @param string $script filename of the script
 * @param string $parameters query
 */

function buffer_script($script,$parameters=NULL) {

	if($parameters !== NULL) {
		$parameter = parse_str(html_entity_decode($parameters));
	}

	ob_start();
	if(is_file("./content/plugins/$script")) {
		include("./content/plugins/$script");
	}

	$content = ob_get_clean();
	$buffer = $parameter . $content;
	
	return $buffer;
}



function fc_get_images_data($image,$parameters=NULL) {

	global $fc_db_content;
	global $fc_template;
	global $languagePack;
	
	if($parameters !== NULL) {
		$parameter = parse_str(html_entity_decode($parameters));
	}
	
	$dbh = new PDO("sqlite:$fc_db_content");
	$sql = "SELECT * FROM fc_media WHERE media_file LIKE :filename AND (media_lang = :lang OR media_lang = '' OR media_lang is null)";
	$sth = $dbh->prepare($sql);
	$sth->bindValue(':filename', "%$image%", PDO::PARAM_STR);
	$sth->bindValue(':lang', "$languagePack", PDO::PARAM_STR);
	$sth->execute();
	$imageData = $sth->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	
	$img_src = str_replace('../content/images/', '/content/images/', $imageData['media_file']);
	$tpl = file_get_contents('./styles/'.$fc_template.'/templates/image.tpl');
	$tpl = str_replace('{$image_src}', $img_src, $tpl);
	$tpl = str_replace('{$image_title}', $imageData['media_title'], $tpl);
	$tpl = str_replace('{$image_alt}', $imageData['media_alt'], $tpl);
	$tpl = str_replace('{$image_caption}', $imageData['media_text'], $tpl);
	$tpl = str_replace('{$image_license}', $imageData['media_license'], $tpl);
	$tpl = str_replace('{$image_credits}', $imageData['media_credits'], $tpl);
	$tpl = str_replace('{$image_priority}', $imageData['media_priority'], $tpl);
	$tpl = str_replace('{$image_link_class}', $aclass, $tpl);
	$tpl = str_replace('{$image_class}', $iclass, $tpl);
	
	return $tpl;
	
}


function fc_global_mod_snippets($mod,$params=NULL) {

	if($params !== NULL) {
		$parameter = parse_str(html_entity_decode($params));
	}
	
  if(is_file('modules/'.$mod.'.mod/global/snippets.php')) {
		include('modules/'.$mod.'.mod/global/snippets.php');
  }
	
	return $mod_str;
	
}


/**
 * find [include] [script] [plugin] and [snippet]
 * except codes within <pre> … </pre>
 */
	 
function text_parser($text) {
	
	global $languagePack;
	
	if(function_exists('theme_text_parser')) {
		$text = theme_text_parser($text);
	}
	
	$text = str_replace('<p>[', '[', $text);
	$text = str_replace(']</p>', ']', $text);
	
	if(preg_match_all('#\<pre.*?\>(.*?)\</pre\>#', $text, $matches)) {
		$match = $matches[0];
		foreach($match as $k => $v) {
			$o = $match[$k];
			$v = str_replace(array('[',']'),array('&#91','&#93'),$v);
			$text = str_replace($o, $v, $text);
		}
	}
 
	$text = preg_replace_callback(
	    '/\[snippet\](.*?)\[\/snippet\]/si',
	    function ($m) {
		    return get_textlib($m[1],$languagePack);
	    },
	    $text
	);
	
	$text = preg_replace_callback(
	    '/\[include\](.*?)\[\/include\]/s',
	    function ($m) {
		   return file_get_contents("./content/plugins/$m[1]");
	    },
	    $text
	);
 
	$text = preg_replace_callback(
	    '/\[script\](.*?)\[\/script\]/s',
	    function ($m) {
		   return buffer_script($m[1]);
	    },
	    $text
	);
	
	$text = preg_replace_callback(
	    '/\[plugin=(.*?)\](.*?)\[\/plugin\]/si',
	    function ($m) {
		   return buffer_script($m[1],$m[2]);
	    },
	    $text
	);
	
	$text = preg_replace_callback(
	    '/\[image=(.*?)\](.*?)\[\/image\]/si',
	    function ($m) {
		   return fc_get_images_data($m[1],$m[2]);
	    },
	    $text
	);
		
	$text = preg_replace_callback(
	    '/\[mod=(.*?)\](.*?)\[\/mod\]/si',
	    function ($m) {
		   return fc_global_mod_snippets($m[1],$m[2]);
	    },
	    $text
	);

	return $text;

}



/**
 * replace bbcodes with html tags
 */

function bbcode_encode($text) {

	$text = preg_replace("/\[b\](.*)\[\/b\]/Usi", "<b>\\1</b>", $text); 
	$text = preg_replace("/\[i\](.*)\[\/i\]/Usi", "<i>\\1</i>", $text); 
	$text = preg_replace("/\[u\](.*)\[\/u\]/Usi", "<u>\\1</u>", $text); 
	$text = preg_replace("/\[color=(.*)\](.*)\[\/color\]/Usi", "<span style=\"color:\\1;\">\\2</span>", $text);
	$text = preg_replace("/\[email=(.*)\](.*)\[\/email\]/Usi", "<a href=\"mailto:\\1\">\\2</a>", $text);
	$text = preg_replace("/\[url=(.*)\](.*)\[\/url\]/Usi", "<a href=\"\\1\">\\2</a>", $text);
	$text = preg_replace("/\[img\](.*)\[\/img\]/Usi", "<img src=\"\\1\">", $text); 
	
	return $text;

} // eol bbcode



/**
 * remove tags [include] [script] [plugin] and [snippet]
 */

function clean_visitors_input($text) {

	$text = preg_replace("/\[snippet\](.*?)\[\/snippet\]/esi","",$text);
	$text = preg_replace("/\[script\](.*?)\[\/script\]/esi","",$text);
	$text = preg_replace("/\[include\](.*?)\[\/include\]/esi","",$text);
	$text = preg_replace("/\[plugin=(.*?)\](.*?)\[\/plugin\]/esi","",$text);
	
	return $text;

} // eol bbcode




/**
 * clean filenames
 * used for upload and SEO URL
 */

function clean_filename($str) {

	$str = strtolower($str);

	$a = array('ä',    'ö',    'ü',    'ß',    ' - ',    ' + ',    '_',    ' / ',    '/'); 
	$b = array('ae',   'oe',   'ue',   'ss',   '-',      '-',      '_',    '-',      '-');
	$str = str_replace($a, $b, $str);

	$str = preg_replace('/\s/s', '_', $str);  // replace blanks -> '_'
	$str = preg_replace('/[^a-z0-9_-]/isU', '', $str); // only a-z 0-9

	$str = trim($str); 

	return $str; 
}  



/**
 * get user avatar via e-mail adress
 */

function get_avatar($user_mail) {

	global $page_contents;
	global $fc_inc_dir;
	global $fc_template;


	$mail_hash = md5($user_mail);
	$avatar_str = "$fc_inc_dir/styles/$fc_template/images/user_icon.jpg";

	if(file_exists("content/avatars/$mail_hash".".png")) {
		$avatar_str = "$fc_inc_dir/content/avatars/$mail_hash".".png";
	}

	return $avatar_str;
}




/** 
 * get user avatar via md5(user_name)
 */

function get_avatar_by_username($user_name) {

	global $page_contents;
	global $fc_inc_dir;
	global $fc_template;

	$avatar_hash = md5($user_name);
	$avatar_str = "$fc_inc_dir/styles/$fc_template/images/user_icon.jpg";

	if(file_exists("content/avatars/$avatar_hash".".png")) {
		$avatar_str = "$fc_inc_dir/content/avatars/$avatar_hash".".png";
	}

	return $avatar_str;
}



/**
 * send a notification to admin
 */

function mailto_admin($subject,$message) {

	global $prefs_mailer_adr;
	global $prefs_mailer_name;
	
	$subject = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $subject );
	$message = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $message );

	require_once("lib/Swift/lib/swift_required.php");
	$transport = Swift_MailTransport::newInstance();
	$mailer = Swift_Mailer::newInstance($transport);
	$msg = Swift_Message::newInstance()
		->setSubject("flatCore Notification - $subject")
  		->setFrom(array("$prefs_mailer_adr" => "flatCore Notification"))
  		->setTo(array("$prefs_mailer_adr" => "$prefs_mailer_name"))
  		->setBody("$message", 'text/html')
  	;
  	$result = $mailer->send($msg);

	if(!$result) {
		echo"<hr>ERROR<hr>";
	}

}



/**
 * create logs
 * @param string $log_trigger system or username
 * @param string $log_entry what's happened
 * @param integer $log_priority 0-10
 * @example record_log("$_SESSION[user_nick]","the message","5");
 */

function record_log($log_trigger = 'system', $log_entry, $log_priority = '0') {

	global $fc_db_stats;
	
	$log_time = time();
	
	$dbh = new PDO("sqlite:$fc_db_stats");
	
	
	$sql = "INSERT INTO log
			(	log_id , log_time , log_trigger , log_entry , log_priority
			) VALUES (
			NULL, '$log_time', '$log_trigger', '$log_entry', '$log_priority' ) ";
											
	$cnt_changes = $dbh->exec($sql);
	
	$dbh = null;

}



/**
 * returns the part of the $string
 * before the first occurrence of $separator
 */

function get_left_string($string,$separator) {
  $string = explode("$separator", $string);
  return $string[0];
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
 * get all active mods
 * from cached file /cache/active_mods.php
 */

function fc_get_active_mods() {
	
	$active_mods = array();
	$cached_mods = FC_CONTENT_DIR . "/cache/active_mods.php";
	
	if(is_file($cached_mods)) {
		include($cached_mods);
	}
	
	return $active_mods;	
}


?>
