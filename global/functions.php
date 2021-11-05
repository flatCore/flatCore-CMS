<?php
	
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * global functions
 * are used in frontend and backend
 * 
 */

include_once 'functions.sanitizer.php';
include_once 'functions.posts.php';

/**
 * get active preferences
 */
 
function fc_get_preferences() {
	global $db_content;	
	$prefs = $db_content->get("fc_preferences", "*", [
		"prefs_status" => "active"
	]);
	return $prefs;
}


/**
 * get the legal pages
 */
 
function fc_get_legal_pages() {
	global $db_content;
	global $languagePack;
	
	$pages = $db_content->select("fc_pages", ["page_linkname","page_title","page_permalink","page_type_of_use"], [
		"AND" => [
			"page_language" => $languagePack,
			"page_type_of_use" => ["imprint", "privacy_policy", "legal"]
		]
	]);
	
	return $pages;
}


/**
 * get all categories
 * order by cat_sort
 */

function fc_get_categories() {
	global $db_content;
	$categories = $db_content->select("fc_categories", "*",
	[
		"ORDER" => ["cat_sort" => "DESC"]
	]);	
	return $categories;
}


/**
 * get all comments
 * $filter = array()
 * $filter['type'] -> p|b|c
 * $filter['status'] -> all|1|2
 */

function fc_get_comments($start=0,$limit=100,$filter) {
	
	global $db_content;
	
	$filter_type = $filter['type'];
	if($filter_type == 'all') {
		$filter_type = ["p","b","c"];
	}
	

	if($filter['status'] == 'all') {
		$comment_status = ["1","2"];
	} else if($filter['status'] == '1') {
		$comment_status = "1";
	} else {
		$comment_status = 2;
	}
	
	
	$filter_relation_id = $filter['relation_id'];
	
	if($filter_relation_id == 'all') {

		$comments = $db_content->select("fc_comments", "*",[
				"AND" => [
				"comment_type" => $filter_type,
				"comment_status" => $comment_status
			],
				"LIMIT" => [$start,$limit],
				"ORDER" => ["comment_time" => "DESC"]
		]);

	} else {

		$comments = $db_content->select("fc_comments", "*",[
				"AND" => [
				"comment_type" => $filter_type,
				"comment_relation_id" => $filter_relation_id,
				"comment_status" => $comment_status
			],
				"LIMIT" => [$start,$limit],
				"ORDER" => ["comment_time" => "ASC"]
		]);		
		
	}
	
	return $comments;
}



/**
 * $comments array() from comments table
 * $sorting array() for sorting by id and parent_id
 */

function fc_list_comments_thread($comments, $sorting, $tpl, $root=0, $level=0) {

	global $lang;

  if(isset($sorting[$root])) {
  	foreach($sorting[$root] as $key => $comment_id) {
	     
	    $padding = (int) (20*$level);
	    if(!is_numeric($padding)) {
		  	$padding = 0;
	    }
	    	    
      $comment_time = date('d.m.Y H:i',$comments[$key]['comment_time']);
      
      /* default avatar image */
      $comment_avatar = '/styles/default/images/avatar.jpg';
      
      /* if it's a registrated user and if there is an avatar, use it */
      if($comments[$key]['comment_author_id'] != '') {
	      $check_avatar = './content/avatars/'.md5($comments[$key]['comment_author']).'.png';
	      if(is_file($check_avatar)) {
		      $comment_avatar = '/content/avatars/'.md5($comments[$key]['comment_author']).'.png';
	      }      
      }
      
      

      
      $comment_avatar_img = '<img src="'.$comment_avatar.'" class="img-avatar img-fluid rounded-circle" alt="" title="'.$comments[$key]['comment_author'].'">';
		$this_comment = $tpl;
			
		$this_comment = str_replace('{comment_author}', $comments[$key]['comment_author'], $this_comment);
		$this_comment = str_replace('{comment_text}', $comments[$key]['comment_text'], $this_comment);
		$this_comment = str_replace('{comment_time}', $comment_time, $this_comment);
		$this_comment = str_replace('{comment_avatar}', $comment_avatar_img, $this_comment);
		$this_comment = str_replace('{comment_id}', $comments[$key]['comment_id'], $this_comment);
		$a_url = '?cid='.$comments[$key]['comment_id'].'#comment-form';
		$this_comment = str_replace('{url_answer_comment}', $a_url, $this_comment);
		$this_comment = str_replace('{level}', $level, $this_comment);
						
		$entry_str .= '<div class="comment-level comment-level-'.$level.'">';
		$entry_str .=  $this_comment;
           
      $entry_str .= fc_list_comments_thread($comments, $sorting, $tpl, $comment_id, $level+1);
      $entry_str .= '</div>';

     }
  }
  
  $entry_str = str_replace('{lang_answer}', $lang['btn_send_answer'], $entry_str);
  
  return $entry_str;
  
}


function fc_write_comment($data) {
	
	global $db_content;
	global $lang;
	global $prefs_comments_mode;
	
	if($data['input_name'] != '' && $data['input_mail'] != '' && $data['input_comment'] != '') {
	
		foreach($data as $key => $val) {
			$$key = htmlspecialchars(strip_tags($val)); 
		}
		
		$type = 'p';
		$comment_status = 2;
		
		if($prefs_comments_mode == 1) {
			$comment_status = 1;
		}
		
		$comment_time = time();
		
		if(is_numeric($data['page_id'])) {
			$type = 'p';
			$relation_id = (int) $data['page_id'];
		}
		
		if(is_numeric($data['post_id'])) {
			$type = 'b';
			$relation_id = (int) $data['post_id'];
		}
	
		if(strlen($input_name) > 30) {
			$input_name = substr($input_name, 0,30);
		}
		
		if(strlen($input_mail) > 50) {
			$input_mail = substr($input_mail, 0,50);
		}
			
		if(strlen($input_comment) > 500) {
			$input_comment = substr($input_comment, 0,500);
		}
		
		if(is_numeric($data['parent_id'])) {
			$parent_id = (int) $data['parent_id'];
		}
		
		if(is_numeric($_SESSION['user_id'])) {
			$comment_author_id = $_SESSION['user_id'];
		} else {
			$comment_author_id = '';
		}
		
		
		$input_comment = nl2br($input_comment);
		
		
		$db_content->insert("fc_comments", [
			"comment_type" =>  $type,
			"comment_relation_id" =>  $relation_id,
			"comment_parent_id" =>  $parent_id,
			"comment_status" =>  $comment_status,
			"comment_time" =>  $comment_time,
			"comment_author" =>  $input_name,
			"comment_author_id" =>  $comment_author_id,
			"comment_author_mail" =>  $input_mail,
			"comment_text" =>  $input_comment
		]);
		
		$insert_id=$db_content->id();
		
		return $insert_id;
		
	}
}




/**
 * sending e-mails
 * $recipient -> array() 'name' and 'mail'
 * $subject -> string()
 * $message -> string()
 *
 * store your smtp settings in /content/config_smtp.php
 */


function fc_send_mail($recipient,$subject,$message) {

	global $prefs_mailer_adr, $prefs_mailer_name, $prefs_mailer_type, $smtp_host, $smtp_port, $smtp_encryption, $smtp_username, $smtp_psw;
	
	$subject = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $subject );
	$message = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $message );

	
	require_once FC_CORE_DIR.'lib/PHPMailer/src/Exception.php';
	require_once FC_CORE_DIR.'lib/PHPMailer/src/PHPMailer.php';
	require_once FC_CORE_DIR.'lib/PHPMailer/src/SMTP.php';
	
	$mail = new PHPMailer(true);
	
	if($prefs_mailer_type == 'smtp') {
		/* sending via smtp */

	  $mail->isSMTP();
	  $mail->Host = "$smtp_host";
	  $mail->SMTPAuth = true;
	  $mail->Username   = "$smtp_username";
	  $mail->Password   = "$smtp_psw";
	  if($smtp_encryption != '') {
	  	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	  }
	  $mail->Port = $smtp_port;
	
	  $mail->setFrom("$prefs_mailer_adr", "$prefs_mailer_name");
	  $mail->addAddress($recipient['mail'], $recipient['name']);
  }

	$mail->setFrom("$prefs_mailer_adr", "$prefs_mailer_name");
	$mail->addAddress($recipient['mail'], $recipient['name']);
	   
  $mail->isHTML(true);
  $mail->CharSet = 'utf-8';
	$mail->Subject = "$subject";
	$mail->Body = "$message";
	  
	  
	if(!$mail->send()) {
    $fail = 'Mailer Error: ' . $mail->ErrorInfo;
    $return = $fail;
	} else {
     $return = 1;
	}
	return $return;
}


/**
 * get all shortcodes or filter by label
 * example for filters
 * $filter['labels'] = '1-2-3-4';
 */
function fc_get_shortcodes($filter=NULL) {
	
	global $db_content;
	global $fc_labels;
		
	/* label filter */
	if($filter['labels'] == 'all' OR $filter['labels'] == '') {
		
		$set_label_filter = '';
		
	} else {
			
		$filter_labels = explode('-', $filter['labels']);
		
		for($i=0;$i<count($fc_labels);$i++) {
			$label = $fc_labels[$i]['label_id'];
			if(in_array($label, $filter_labels)) {
				$set_label_filter .= "textlib_labels LIKE '%,$label,%' OR textlib_labels LIKE '%,$label' OR textlib_labels LIKE '$label,%' OR textlib_labels = '$label' OR ";
			}
		}
		
		$set_label_filter = substr("$set_label_filter", 0, -3); // cut the last ' OR'
		
	}
	
	$sql_filter = "WHERE textlib_type LIKE 'shortcode' ";
	
	if($set_label_filter != "") {
		$sql_filter .= " AND ($set_label_filter) ";
	}
	
	$sql = "SELECT * FROM fc_textlib $sql_filter";
	$shortcodes = $db_content->query($sql)->fetchAll(PDO::FETCH_ASSOC);

	return $shortcodes;
}


/**
 * get saved data from table fc_themes
 * $theme (string) name of the theme
 */
function fc_get_theme_options($theme) {

	global $db_content;
	
	$theme_data = $db_content->select("fc_themes", "*",[
		"theme_name" => $theme
	]);
	
	return $theme_data;		
}


?>