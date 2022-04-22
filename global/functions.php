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
include_once 'functions.cart.php';

/**
 * get active preferences
 */
 
function fc_get_preferences() {
	
	global $db_content;
	
	$prefs = $db_content->select("fc_options", "*", [
		"option_module" => "fc"
	]);
	
	if(count($prefs) < 1) {
		echo '<p class="alert alert-danger">There are no options</p>';
	
		/* read the old prefs from fc_preferences and write it to fc_options */
		$prefs = $db_content->get("fc_preferences", "*", [
			"prefs_status" => "active"
		]);
		
		foreach($prefs as $key => $value) {		
			$db_content->insert("fc_options", [
				"option_module" => 'fc',
				"option_key" => $key,
				"option_value" => $value
			]);
		}
	
		$prefs = $db_content->select("fc_options", "*", [
			"option_module" => "fc"
		]);	
	
	}
	
	

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

function fc_get_comments($start,$limit,$filter) {
	
	global $db_content;
	
	if(empty($start)) {
		$start = 0;
	}
	if(empty($limit)) {
		$limit = 100;
	}	
	
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


/**
 * store a comment
 * @param array $data 'input_name' 'input_mail' 'input_comment'
 * @return void
 */

function fc_write_comment($data) {
	
	global $db_content;
	global $lang;
	global $prefs_comments_mode;
	
	if($data['input_name'] != '' && $data['input_mail'] != '' && $data['input_comment'] != '') {
	
		foreach($data as $key => $val) {
			$$key = sanitizeUserInputs($val);
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
 * store your smtp settings in /content/config_smtp.php
 *
 * @param array $recipient 'mail' and 'name'
 * @param string $subject subject of the email
 * @param string $message string/html content of the email
 * @return 1 if success or ErrorInfo if failed
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
 * @param array $recipient 'type', 'name' and 'mail'
 * @param integer $order the order id
 * @param string $reason this will change the subject
 * @return void
 */

function fc_send_order_status($recipient,$order,$reason) {

    global $fc_prefs;
    global $languagePack;
    global $lang;

    $order_id = (int) $order;
    $this_order = fc_get_order_details($order_id);

    if($recipient['type'] == 'client') {
        // get client data
        $user_data = fc_get_userdata_by_id($this_order['user_id']);
        $recipient['name'] = $user_data['user_firstname'].' '.$user_data['user_lastname'];
        $recipient['mail'] = $user_data['user_mail'];
        if($recipient['mail'] == '') {
            return 'error';
        }
    } else {
        // send to admin
        $recipient['name'] = $fc_prefs['prefs_mailer_name'];
        $recipient['mail'] = $fc_prefs['prefs_mailer_adr'];
    }

    if($reason == 'notification') {
        $subject = "Notification: Order status # ".$this_order['order_nbr'];
    } else if($reason == 'change_payment_status'){
        $subject = "We changed the Payment Status # ".$this_order['order_nbr'];
    } else if($reason == 'change_shipping_status') {
        $subject = "We changed the Shipping Status # ".$this_order['order_nbr'];
    } else {
        $subject = "We changed something in # ".$this_order['order_nbr'];
    }


    $order_invoice_address = html_entity_decode($this_order['order_invoice_address']);

    $mail_data['body_tpl'] = 'send-order-status.tpl';
    $mail_data['subject'] = $subject;
    $mail_data['salutation'] = $subject;

    $build_html_mail = fc_build_html_file($mail_data);

    if($this_order['order_status_payment'] == 2) {
        $build_html_mail = str_replace("{payment_status}",$lang['status_payment_paid'],$build_html_mail);
    } else {
        $build_html_mail = str_replace("{payment_status}",$lang['status_payment_open'],$build_html_mail);
    }
    if($this_order['order_status_shipping'] == 2) {
        $build_html_mail = str_replace("{shipping_status}",$lang['status_shipping_done'],$build_html_mail);
    } else {
        $build_html_mail = str_replace("{shipping_status}",$lang['status_shipping_open'],$build_html_mail);
    }
    $build_html_mail = str_replace("{order_nbr}",$this_order['order_nbr'],$build_html_mail);
    $build_html_mail = str_replace("{invoice_address}",$order_invoice_address,$build_html_mail);
    $price_total = fc_post_print_currency($this_order['order_price_total']). ' '.$this_order['order_currency'];
    $build_html_mail = str_replace("{price_total}",$price_total,$build_html_mail);

    $order_products = json_decode($this_order['order_products'],true);
    $cnt_order_products = count($order_products);

    $products_str = '<table role="presentation" border="0" cellpadding="0" cellspacing="0">';
    $products_str .= '<tr>';
    $products_str .= '<td>#</td>';
    $products_str .= '<td>'.$lang['label_product_info'].'</td>';
    $products_str .= '<td>'.$lang['label_product_amount'].'</td>';
    $products_str .= '<td>'.$lang['label_price'].' ('.$lang['label_gross'].')</td>';
    $products_str .= '</tr>';
    for($i=0;$i<$cnt_order_products;$i++) {
        $products_str .= '<tr>';
        $products_str .= '<td>'.$order_products[$i]['product_number'].'</td>';
        $products_str .= '<td>'.$order_products[$i]['title'].'</td>';
        $products_str .= '<td>'.$order_products[$i]['amount'].'</td>';
        $products_str .= '<td>'.fc_post_print_currency($order_products[$i]['price_gross_raw']).' '.$this_order['order_currency'].'</td>';
        $products_str .= '</tr>';
    }
    $products_str .= '</table>';

    $build_html_mail = str_replace("{order_products}",$products_str,$build_html_mail);

    foreach($lang as $key => $val) {
        $search = '{lang_'.$key.'}';
        $build_html_mail = str_replace("$search","$val",$build_html_mail);
    }

    $send_mail = fc_send_mail($recipient, $subject, $build_html_mail);
    return $send_mail;
}


/**
 * create html file resp. string
 * send via fc_send_mail() or force download as file
 * get the content from mail template f.e. /styles/default/templates-mail/mail.tpl
 * get the styles /styles/default/templates-mail/styles.css
 * get the mail body template from $data['tpl']
 * bring everything together and return as string
 * @param array $data 'subject','preheader','title','salutation','body','footer','tpl', 'body_tpl'
 * @return string html formatted string
 */

function fc_build_html_file($data) {
	
	global $fc_prefs;
	global $languagePack;
	
	$tpl_dir = FC_CORE_DIR.'styles/'.$fc_prefs['prefs_template'];
	$tpl_style = file_get_contents($tpl_dir.'/templates-mail/styles.css');

    if($data['tpl'] == '') {
        $tpl_file = file_get_contents($tpl_dir.'/templates-mail/mail.tpl');
    } else {
        $tpl_file = file_get_contents($tpl_dir.'/templates-mail/'.basename($data['tpl']));
    }

    if($data['body_tpl'] != '') {
        $tpl_body_file = file_get_contents($tpl_dir . '/templates-mail/' . basename($data['body_tpl']));
        $tpl_file = str_replace('{mail_body}', $tpl_body_file, $tpl_file);
    }

	$footer = $data['footer'];
	if($data['footer'] == '') {
		$footer = fc_get_textlib('footer_text_mail','','content');
	}

	$tpl_file = str_replace('{styles}', $tpl_style, $tpl_file);
    $tpl_file = str_replace('{mail_subject}', $data['subject'], $tpl_file);
    $tpl_file = str_replace('{mail_salutation}', $data['salutation'], $tpl_file);
	$tpl_file = str_replace('{mail_body}', $data['body'], $tpl_file);
	$tpl_file = str_replace('{mail_title}', $data['title'], $tpl_file);
    $tpl_file = str_replace('{mail_preheader}', $data['preheader'], $tpl_file);
	$tpl_file = str_replace('{mail_footer}', $footer, $tpl_file);
	
	return $tpl_file;	
}


/**
 * get textlib content
 * @param string $name name of the entry
 * @param string $lang language of the entry
 * @param string $type 'all' returns all contents as array
 * @param string $type 'content' returns only the text as string
 * @param string $type 'tpl' use snippet it's template. Return as string.
 *
 * @global $db_content database settings
 * @global $languagePack the language required
 */


function fc_get_textlib($name,$lang,$type) {

	global $db_content;
	global $languagePack;
	
	if($lang == '') {
		$lang = $languagePack;
	}

	
	$textlibData = $db_content->get("fc_textlib", "*", [
		"AND" => [
			"textlib_name" => "$name",
			"textlib_lang" => "$lang"
		]
	]);
	
	if($type == 'all') {
		return $textlibData;
	}
	
	if($type == 'content') {
		return $textlibData['textlib_content'];
	}
	
	if($type == 'tpl') {
		
		foreach($textlibData as $k => $v) {
	   		$$k = stripslashes($v);
		}
		
		$get_tpl_file = 'styles/default/templates/snippet.tpl';
		
		if($textlib_theme != '' OR $textlib_theme != 'use_standard') {
			$get_tpl_file = 'styles/'.$textlib_theme.'/templates/'.$textlib_template;
		}
		
		if(is_file("$get_tpl_file")) {
			$tpl_file = file_get_contents($get_tpl_file);
			
			$snippet_thumbnail_array = explode("<->", $textlib_images);
			if(count($snippet_thumbnail_array) > 0) {
				foreach($snippet_thumbnail_array as $img) {
					$img = str_replace('../content/', '/content/', $img);
					$tpl_file = str_replace('{$snippet_img_src}',$img,$tpl_file);						
				}
			}
			
			$tpl_file = str_replace('{$snippet_title}',$textlib_title,$tpl_file);
			$tpl_file = str_replace('{$snippet_text}',$textlib_content,$tpl_file);
			$tpl_file = str_replace('{$snippet_teaser}',$textlib_teaser,$tpl_file);
			$tpl_file = str_replace('{$snippet_classes}',$textlib_classes,$tpl_file);
			$tpl_file = str_replace('{$snippet_url}',$textlib_permalink,$tpl_file);
			$tpl_file = str_replace('{$snippet_url_name}',$textlib_permalink_name,$tpl_file);
			$tpl_file = str_replace('{$snippet_url_title}',$textlib_permalink_title,$tpl_file);
			$tpl_file = str_replace('{$snippet_url_classes}',$textlib_permalink_classes,$tpl_file);
			return $tpl_file;
		}
	}

}


/**
 * @param integer $id
 * @return mixed
 */

function fc_get_userdata_by_id($id) {

    global $db_user;

    $user_data = $db_user->get("fc_user", "*", [
        "user_id" => $id
    ]);

    return $user_data;
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
 * get posts features from fc_textlib
 * textlib_type = post_feature
 */

function fc_get_posts_features() {
	
	global $db_content;
	
	
	$features = $db_content->select("fc_textlib", "*",[
		"textlib_type" => 'post_feature',
		"ORDER" => [
			"textlib_priority" => "DESC"
		]
	]);
	
	return $features;
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



/**
 * upload avatar
 * convert to png and square format
 * rename file to md5(username)
 *
 * $file (array) data from upload form
 * $username (string) 
 */

function fc_upload_avatar($file,$username) {
	
	if(FC_SOURCE == 'frontend') {
		$uploads_dir = "content/avatars";
	} else {
		$uploads_dir = "../content/avatars";
	}
	$max_width = 100;
		
	$tmp_name = $file['avatar']['tmp_name'];
	$org_name = $file['avatar']['name'];
	$new_name = md5($username);
	$new_avatar_src = $uploads_dir.'/'.$new_name.'.png';
		
	list($width_upl, $height_upl, $type_upl) = getimagesize($tmp_name);
    
		if ($width_upl > $height_upl) {
		  $y = 0;
		  $x = ($width_upl - $height_upl) / 2;
		  $smallestSide = $height_upl;
		} else {
		  $x = 0;
		  $y = ($height_upl - $width_upl) / 2;
		  $smallestSide = $width_upl;
		}
    
		$imgt = '';
		if($type_upl==1) { $imgt = imagecreatefromgif($tmp_name);  }
		if($type_upl==2) { $imgt = imagecreatefromjpeg($tmp_name);  }
		if($type_upl==3) { $imgt = imagecreatefrompng($tmp_name);  }
		
		
		if($imgt != '') {

			$new_image = imagecreatetruecolor($max_width, $max_width);
			imagecopyresampled($new_image, $imgt, 0, 0, $x, $y, $max_width, $max_width, $smallestSide, $smallestSide);
			
					
			if(imagepng($new_image, $new_avatar_src,9) === true) {
				imagedestroy($new_image);
				return true;			
			}
			
		
		} else {
			return false;
		}

	
}

?>