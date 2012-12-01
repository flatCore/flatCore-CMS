<?php


function is_user_in_group($user_id,$user_group) {

/* check for username in usergroup */

	global $fc_db_user;

	$dbh = new PDO("sqlite:$fc_db_user");

	$sql = "SELECT group_name, group_user
			FROM fc_groups
			WHERE group_name = '$user_group'
			";
	
	$result = $dbh->query($sql);
	$result= $result->fetch(PDO::FETCH_ASSOC);
   
	$arr_users = explode(" ", $result[group_user]);

	if(in_array("$_SESSION[user_id]",$arr_users)) {
		$in_group = "true";
	} else {
		$in_group = "false";
	}


return $in_group;

} // eol is_user_in_group()









function buffer_script($script) {

/*
buffer scripts placed in the plugin folder
input: function text_parser($text)
*/

	ob_start();
	if(is_file("./content/plugins/$script")) {
		include("./content/plugins/$script");
	}

	$content = ob_get_clean();
return $content;

} // eol buffer_script()


function text_parser($text) {

	/**
	 * find [include] [script] and [snippet]
	 * except codes within <pre> … </pre>
	 */

	preg_match('#\<pre\>(.+)\</pre\>#i', $text, $matches);

	$noparse = $matches[1];
	$noparse = preg_replace('/\[/i', '&#91;', $noparse);
	$noparse = preg_replace('/\]/i', '&#93;', $noparse);
	$text = preg_replace('#\<pre\>(.+)\</pre\>#i', "<pre>$noparse</pre>", $text);
 
	$text = preg_replace("/\[include\](.*?)\[\/include\]/se","file_get_contents('./content/plugins/\\1')",$text);
	$text = preg_replace("/\[script\](.*?)\[\/script\]/se","buffer_script('\\1')",$text);
	$text = preg_replace("/\[snippet\](.*?)\[\/snippet\]/esi","get_textlib_by_fn('\\1')",$text);

return $text;

} // eol text_parser()




function bbcode_encode($text) {

	/*
	replace bbcodes with html tags
	*/
	
	$text = preg_replace("/\[b\](.*)\[\/b\]/Usi", "<b>\\1</b>", $text); 
	$text = preg_replace("/\[i\](.*)\[\/i\]/Usi", "<i>\\1</i>", $text); 
	$text = preg_replace("/\[u\](.*)\[\/u\]/Usi", "<u>\\1</u>", $text); 
	$text = preg_replace("/\[color=(.*)\](.*)\[\/color\]/Usi", "<span style=\"color:\\1;\">\\2</span>", $text);
	$text = preg_replace("/\[email=(.*)\](.*)\[\/email\]/Usi", "<a href=\"mailto:\\1\">\\2</a>", $text);
	$text = preg_replace("/\[url=(.*)\](.*)\[\/url\]/Usi", "<a href=\"\\1\">\\2</a>", $text);
	$text = preg_replace("/\[img\](.*)\[\/img\]/Usi", "<img src=\"\\1\">", $text); 
	
	return $text;

} // eol bbcode


function clean_visitors_input($text) {

/*
remove tags [include] [script] and [snippet]
*/
	$text = preg_replace("/\[snippet\](.*?)\[\/snippet\]/esi","",$text);
	$text = preg_replace("/\[script\](.*?)\[\/script\]/esi","",$text);
	$text = preg_replace("/\[include\](.*?)\[\/include\]/esi","",$text);


return $text;

} // eol bbcode





function clean_filename($str) {

/*
clean filenames
used for upload and SEO URL
*/

	$str = strtolower($str);

	$a = array('ä',    'ö',    'ü',    'ß',    ' - ',    ' + ',    '_',    ' / ',    '/'); 
	$b = array('ae',   'oe',   'ue',   'ss',   '-',      '-',      '-',    '-',      '-');
	$str = str_replace($a, $b, $str);

	$str = preg_replace('/\s/s', '_', $str);  // replace blanks -> '_'
	$str = preg_replace('/[^a-z0-9_-]/isU', '', $str); // only a-z 0-9

	$str = trim($str); 

return $str; 
}  








function get_avatar($user_mail) {

/* get user avatar via e-mail adress */

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






function get_avatar_by_username($user_name) {

/* get user avatar via md5(user_name) */

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







function mailto_admin($subject,$message) {

/*
send a notification to admin
$fc_mailer_adr (defined in config.php)
*/

global $fc_mailer_adr;
global $fc_mailer_name;

$subject = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $subject );
$message = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $message );

	require_once("lib/Swift/lib/swift_required.php");
	$transport = Swift_MailTransport::newInstance();
	$mailer = Swift_Mailer::newInstance($transport);
	$msg = Swift_Message::newInstance()
		->setSubject("flatCore Notification - $subject")
  		->setFrom(array("$fc_mailer_adr" => "flatCore Notification"))
  		->setTo(array("$fc_mailer_adr" => "$fc_mailer_name"))
  		->setBody("$message", 'text/html')
  	;
  	$result = $mailer->send($msg);

	if(!$result) {
		echo"<hr>ERROR<hr>";
	}


} // eol mailto_admin()






function record_log($log_trigger = 'system', $log_entry, $log_priority = '0') {

/*
create logs
$log_trigger	- (string) system or username
$log_entry		- (string) what's happened
$log_priority	- (integer) 0-10
				
example:
record_log("$_SESSION[user_nick]","the message","5");
*/

global $fc_db_stats;

$log_time = time();

$dbh = new PDO("sqlite:$fc_db_stats");


$sql = "INSERT INTO log
		(	log_id , log_time , log_trigger , log_entry , log_priority
		) VALUES (
		NULL, '$log_time', '$log_trigger', '$log_entry', '$log_priority' ) ";
										
$cnt_changes = $dbh->exec($sql);

$dbh = null;



} // eol record_log()





function get_left_string($string,$separator) {
/*
returns the part of the $string
before the first occurrence of $separator
*/

  $string = explode("$separator", $string);
  return $string[0];
}






?>