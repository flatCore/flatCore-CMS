<?php

if($_POST['ask_for_psw']) {
	// send password information
	
	$mail = strip_tags($_POST['mail']);
	$send_data = 'false';
	$msg_mail_format = '';
	
	//check existing E-Mail Adresses	
	$all_usermail_array = array();
	
	if(!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
		$msg_mail_format = $lang['msg_invalid_mail_format'];
	} else {
		$all_usermail_array = get_all_usermail($fc_db_user);

		foreach($all_usermail_array as $entry) { 
			if($mail == $entry['user_mail']) {
		  	$send_data = "true";
				break;
		  }  
		}		
	}

	
	// send E-Mail
	if($send_data == "true") {
	
		$userdata_array = get_userdata_by_mail($mail);
		$user_nick = $userdata_array['user_nick'];
		$user_registerdate = $userdata_arry['user_registerdate'];
		
		/* unique token user_registerdate + user_mail */
		$reset_token = md5("$user_registerdate$mail");
		$reset_link = "http://$_SERVER[HTTP_HOST]$_SERVER[SCRIPT_NAME]?p=password&token=$reset_token";
		
		/* input token */
		$dbh = new PDO("sqlite:$fc_db_user");
		$sql = "UPDATE fc_user SET user_reset_psw = :reset_token WHERE user_mail = :mail";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':reset_token', $reset_token, PDO::PARAM_STR);
		$sth->bindParam(':mail', $mail, PDO::PARAM_STR);
		$sth->execute();
		$dbh = null;
		
		/* generate the message */
		$email_msg = str_replace("{USERNAME}","$user_nick",$lang['forgotten_psw_mail_info']);
		$email_msg = str_replace("{RESET_LINK}","$reset_link",$email_msg);
		
		/* send register mail to the new user */
		require_once("lib/Swift/lib/swift_required.php");
		
		if($prefs_mailer_type == 'smtp') {
			$transport = Swift_SmtpTransport::newInstance("$prefs_smtp_host", "$prefs_smtp_port")
				->setUsername("$prefs_smtp_username")
				->setPassword("$prefs_smtp_psw");
				
			if($prefs_mail_smtp_encryption_input != '') {
				$transport ->setEncryption($pb_prefs['prefs_smtp_encryption']);
			}
		} else {
			$transport = Swift_MailTransport::newInstance();
		}
	
		$mailer = Swift_Mailer::newInstance($transport);
		$message = Swift_Message::newInstance()
			->setSubject("$lang[forgotten_psw_mail_subject] | $prefs_pagetitle")
	  		->setFrom(array("$prefs_mailer_adr" => "$prefs_mailer_name"))
	  		->setTo(array("$mail" => "$user_nick"))
	  		->setBody("$email_msg", 'text/html');
	  	$result = $mailer->send($message);
		
		$psw_message = $lang['msg_forgotten_psw_step1'];
	
	} // eol send E-Mail
	
	if($psw_message != "") {
		$smarty->assign("msg_status","alert alert-info");
		$smarty->assign("psw_message","$psw_message");
	}

}



/**
 * Generate new temp Password
 * inform user via mail
 */

if($_GET['token'] != "") {

	$token = strip_tags($_GET['token']);
	
	unset($userdata_array);
	$userdata_array = get_userdata_by_token($token);
	
	if(!is_array($userdata_array)) {
		die('Error: unauthorized access');
	}
	
	$temp_psw = randpsw();
	
	$user_id = $userdata_array['user_id'];
	$user_nick = $userdata_array['user_nick'];
	$user_mail = $userdata_array['user_mail'];
	
	$update_user_psw = password_hash($temp_psw, PASSWORD_DEFAULT);
	
	/* update db - send information to user_mail */
	
	$dbh = new PDO("sqlite:$fc_db_user");
	$sql = "UPDATE fc_user SET user_psw_hash = :update_user_psw, user_reset_psw = '' WHERE user_id = :user_id ";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':update_user_psw', $update_user_psw, PDO::PARAM_STR);
	$sth->bindParam(':user_id', $user_id, PDO::PARAM_INT);
	
	$cnt = $sth->execute();
	$dbh = null;
	
	$email_msg = $lang['forgotten_psw_mail_update'];
	$email_msg = str_replace("{USERNAME}","$user_nick",$email_msg);
	$email_msg = str_replace("{temp_psw}","$temp_psw",$email_msg);
	
	/* send register mail to the new user */
	require_once("lib/Swift/lib/swift_required.php");
	if($prefs_mailer_type == 'smtp') {
		$transport = Swift_SmtpTransport::newInstance("$prefs_smtp_host", "$prefs_smtp_port")
			->setUsername("$prefs_smtp_username")
			->setPassword("$prefs_smtp_psw");
			
		if($prefs_mail_smtp_encryption_input != '') {
			$transport ->setEncryption($pb_prefs['prefs_smtp_encryption']);
		}
	} else {
		$transport = Swift_MailTransport::newInstance();
	}
	$mailer = Swift_Mailer::newInstance($transport);
	$message = Swift_Message::newInstance()
		->setSubject("$lang[forgotten_psw_mail_subject] | $prefs_pagetitle")
  	->setFrom(array("$prefs_mailer_adr" => "$prefs_mailer_name"))
  	->setTo(array("$user_mail" => "$user_nick"))
  	->setBody("$email_msg", 'text/html');
  $result = $mailer->send($message);
	
	$psw_message = $lang['msg_forgotten_psw_step2'];
	
	if($psw_message != "") {
		$smarty->assign("msg_status","alert alert-info");
		$smarty->assign("psw_message","$psw_message");
	}


}




if($fc_mod_rewrite == 'permalink') {
	$form_url = FC_INC_DIR . "/password/";
} else {
	$form_url = "$_SERVER[PHP_SELF]?p=password";
}

$smarty->assign("form_url","$form_url");
$smarty->assign("forgotten_psw","$lang[forgotten_psw]");
$smarty->assign("forgotten_psw_intro","$lang[forgotten_psw_intro]");
$smarty->assign("label_mail","$lang[label_mail]");
$smarty->assign("button_send","$lang[button_send]");
$smarty->assign("legend_ask_for_psw","$lang[legend_ask_for_psw]");

$output = $smarty->fetch("password.tpl");
$smarty->assign('page_content', $output);

?>