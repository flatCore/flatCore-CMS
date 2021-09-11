<?php
	

/**
 * check if username exists in usergroup
 */

function is_user_in_group($user_id,$user_group) {

	global $db_user;
	
	$result = $db_user->get("fc_groups", ["group_name","group_user"], [
			"group_name" => $user_group
	]);
	  
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
		include './content/plugins/'.$script;
	} else if (is_dir("./content/plugins/$script")) {
		
		if(is_file("./content/plugins/$script/index.php")) {
			include './content/plugins/'.$script.'/index.php';
		}
		
	}

	$content = ob_get_clean();
	$buffer = $parameter . $content;
	
	return $buffer;
}


/**
 * get the image data from $db_content
 * if parameter data = array return only data
 * if no parameter is set, return the image data styled with tpl file image.tpl
 */

function fc_get_images_data($image,$parameters=NULL) {

	global $db_content;
	global $fc_template;
	global $languagePack;
	
	if($parameters !== NULL) {
		$parameter = parse_str(html_entity_decode($parameters));
	}
	
	$imageData = $db_content->get("fc_media", "*", [
			"AND" => [
			"media_file[~]" => "%$image",
			"media_lang" => "$languagePack"
			]
	]);
	
	if($data == 'array') {
		return $imageData;
	}
	
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

function fc_get_files_data($file,$parameters=NULL) {

	global $db_content;
	global $fc_template;
	global $languagePack;
	
	if($parameters !== NULL) {
		$parameter = parse_str(html_entity_decode($parameters));
	}
	
	$fileData = $db_content->get("fc_media", "*", [
			"AND" => [
			"media_file[~]" => "%$file",
			"media_lang" => "$languagePack"
			]
	]);
	
	$file_src = str_replace('../content/files/', '/content/files/', $fileData['media_file']);
	$tpl = file_get_contents('./styles/'.$fc_template.'/templates/download.tpl');
	$tpl = str_replace('{$file_src}', $file_src, $tpl);
	$tpl = str_replace('{$file_title}', $fileData['media_title'], $tpl);
	$tpl = str_replace('{$file_alt}', $fileData['media_alt'], $tpl);
	$tpl = str_replace('{$file_caption}', $fileData['media_text'], $tpl);
	$tpl = str_replace('{$file_license}', $fileData['media_license'], $tpl);
	$tpl = str_replace('{$file_credits}', $fileData['media_credits'], $tpl);
	$tpl = str_replace('{$file_priority}', $fileData['media_priority'], $tpl);
	$tpl = str_replace('{$file_link_class}', $aclass, $tpl);
	$tpl = str_replace('{$file_class}', $iclass, $tpl);
	
	return $tpl;
	
}


function fc_global_mod_snippets($mod,$params=NULL) {

	if($params !== NULL) {
		$parameter = parse_str(html_entity_decode($params));
	}
	
  if(is_file('modules/'.$mod.'.mod/global/snippets.php')) {
		include 'modules/'.$mod.'.mod/global/snippets.php';
  }
	
	return $mod_str;
	
}


/**
 * find [include] [script] [plugin] and [snippet]
 * except codes within <pre> … </pre> or <code> … </code>
 */
	 
function text_parser($text) {
	
	global $languagePack;
	global $shortcodes;

	$text = str_replace('<p>[', '[', $text);
	$text = str_replace(']</p>', ']', $text);
	
	/* replace all shortcodes */
	if(is_array($shortcodes)) {
		foreach($shortcodes as $k => $v) {
			
			$text = str_replace($v['textlib_shortcode'], $v['textlib_content'], $text,$count);
			if($count > 0) {
				fc_store_admin_helper('sc',$v['textlib_shortcode']);
			}
		}
	}
	
	if(preg_match_all('#\<pre.*?\>(.*?)\</pre\>#', $text, $matches)) {
		$match = $matches[0];
		foreach($match as $k => $v) {
			$o = $match[$k];
			$v = str_replace(array('[',']'),array('&#91','&#93'),$v);
			$text = str_replace($o, $v, $text);
		}
	}
	
	if(preg_match_all('#\<code.*?\>(.*?)\</code\>#', $text, $matches)) {
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
		    fc_store_admin_helper('s',$m[1]);
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
		    fc_store_admin_helper('p',$m[1]);
				return buffer_script($m[1],$m[2]);
	    },
	    $text
	);
	
	$text = preg_replace_callback(
	    '/\[image=(.*?)\](.*?)\[\/image\]/si',
	    function ($m) {
		    fc_store_admin_helper('i',$m[1]);
				return fc_get_images_data($m[1],$m[2]);
	    },
	    $text
	);

	$text = preg_replace_callback(
	    '/\[file=(.*?)\](.*?)\[\/file\]/si',
	    function ($m) {
		    fc_store_admin_helper('f',$m[1]);
				return fc_get_files_data($m[1],$m[2]);
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

	if(function_exists('theme_text_parser')) {
		$text = theme_text_parser($text);
	}
	
	return $text;

}

/**
 * We store possibly existing plugins or snippets ...
 * so we can enable faster access from frontend
 * use smarty variable {$admin_helpers} in frontend
 *
 * $trigger p 	= plugin
 * 					s 	= snippet
 *					sc 	= shortcode
 *					i|f	= media file / image or file from fc_media
 * $lang = language
 */
function fc_store_admin_helper($trigger,$val) {
	
	global $languagePack;
	
	/* skip this function for visitors */
	if($_SESSION['user_class'] != 'administrator') {
		return;
	}
	
	if($trigger === null) {
	}
	
	if($lang === null) {
	}
	
	
	$store = $_SESSION['fc_admin_helpers'];

	/* add a shortcode */
	if($trigger == 'sc') {
		
		$stored_sc .= '<form action="/acp/acp.php?tn=pages&sub=shortcodes" method="POST" class="d-inline p-1">';
		$stored_sc .= '<button class="btn btn-sm btn-secondary">'.$val.'</button>';
		$stored_sc .= '<input type="hidden" name="edit_shortcode" value="'.$val.'">';
		$stored_sc .= '</form>';
		
		$store['shortcodes'][] = $stored_sc;
	}
	
	/* add a plugin */
	if($trigger == 'p') {
		$store['plugin'][] = $val;
	}
	
	/* add a image */
	if($trigger == 'i') {
		$store['images'][] = $val;
	}
	
	/* add a file */
	if($trigger == 'f') {
		$store['files'][] = $val;
	}
	
	/* add a snippet */
	if($trigger == 's') {
		
		$id = get_textlib_id($val,$languagePack);
		
		$stored_snippet .= '<form action="/acp/acp.php?tn=pages&sub=snippets" method="POST" class="d-inline p-1">';
		$stored_snippet .= '<button class="btn btn-sm btn-secondary">'.$val.'</button>';
		$stored_snippet .= '<input type="hidden" name="snip_id" value="'.$id.'">';
		$stored_snippet .= '</form>';
		
		$store['snippet'][] = $stored_snippet;
		
	}
		
	$_SESSION['fc_admin_helpers'] = $store;	
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
 * sanitize user inputs
 *
 */

function sanitizeUserInputs($str,$type='str',$flags=NULL) {
	
	if($type == 'str') {
		$str = trim($str);	
		$str = strip_tags($str);
		$str = filter_var($str, FILTER_SANITIZE_STRING);
		$str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
	}
	
	return $str;
	
}

/**
 * Generate cryptographically secure random strings
 * from https://gist.github.com/raveren/5555297
 */

function random_text( $type = 'alnum', $length = 8 ) {
	switch ( $type ) {
		case 'alnum':
			$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
		case 'alpha':
			$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
		case 'hexdec':
			$pool = '0123456789abcdef';
			break;
		case 'numeric':
			$pool = '0123456789';
			break;
		case 'nozero':
			$pool = '123456789';
			break;
		case 'distinct':
			$pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
			break;
		default:
			$pool = (string) $type;
			break;
	}


	$crypto_rand_secure = function ( $min, $max ) {
		$range = $max - $min;
		if ( $range < 0 ) return $min; // not so random...
		$log    = log( $range, 2 );
		$bytes  = (int) ( $log / 8 ) + 1; // length in bytes
		$bits   = (int) $log + 1; // length in bits
		$filter = (int) ( 1 << $bits ) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );
			$rnd = $rnd & $filter; // discard irrelevant bits
		} while ( $rnd >= $range );
		return $min + $rnd;
	};

	$token = "";
	$max   = strlen( $pool );
	for ( $i = 0; $i < $length; $i++ ) {
		$token .= $pool[$crypto_rand_secure( 0, $max )];
	}
	return $token;
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

	global $db_statistics;
	
	$log_time = time();
	
	$db_statistics->insert("log", [
		"log_time" => "$log_time",
		"log_trigger" => "$log_trigger",
		"log_entry" => "$log_entry",
		"log_priority" => $log_priority
	]);

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
 * $s = fc_array_multisort($pages, 'page_language', SORT_ASC, 'page_sort', SORT_ASC, SORT_NATURAL);
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
		include $cached_mods;
	}
	
	return $active_mods;	
}



/* search */

function fc_search($query, $currentPage=1, $itemsPerPage=10) {
	
	global $fc_db_index;
	
	$query = str_replace('-', ' ', $query);
	
	$dbh = new PDO("sqlite:$fc_db_index");
	$dbh->query("SET NAMES 'utf-8'");
	$dbh->sqliteCreateFunction('rank', 'rankinfo', 1);
	
	$sqlquery = 'SELECT COUNT(*) AS totalrows FROM pages WHERE page_content LIKE :searchstring';
	$sth = $dbh->prepare($sql);
	$sth->bindValue(':searchstring', "%{$query}%", PDO::PARAM_STR);
	$sth->execute();
	$arr_results = $sth->fetchAll(PDO::FETCH_ASSOC);
	
	
	$startOffset = (int) ($currentPage-1) * $itemsPerPage;
	$endOffset = $startOffset + $itemsPerPage;
		
	$sql = "SELECT page_url, page_title, page_description, page_thumbnail, snippet(pages, '<mark class=\"hi\">', '</mark>', '...', -1, -60) AS snipp, rank(matchinfo(pages)) AS score FROM pages WHERE pages MATCH :search ORDER BY score DESC LIMIT $startOffset, $endOffset;"; // LIMIT 0,10

	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(':search', "*$query*", PDO::PARAM_STR);
	$stmt->execute();
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	$dbh = null;
	return $results;
	
}


/**
 * get a rank
 * https://www.sqlite.org/fts3.html#appendix_a
 */

function rankinfo($string) {
	
	$matchinfo = unpack("I*", $string);
	$cnt_phrase = $matchinfo[1];
	$cnt_col = $matchinfo[2];
	
	$score = 0;
	
	for($i=0; $i<$cnt_phrase; $i++) {
		
		$aPhraseinfo = array_slice($matchinfo, 2 + $i * $cnt_col * 3);
		for($x=0; $x<$cnt_col; $x++) {
		
			$nHitCount = $aPhraseinfo[3 * $x];
			$nGlobalHitCount = $aPhraseinfo[3 * $x + 1];
			$weight = 10;
			
			if( $nHitCount > 0 ) {
				$score += ((double)$nHitCount / (double)$nGlobalHitCount) * $weight;
			}
			
		}
	}
	return $score;
}


?>
