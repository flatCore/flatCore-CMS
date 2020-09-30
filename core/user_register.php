<?php

/**
 * prohibit unauthorized access
 */
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){ 
	die ('<h2>Direct File Access Prohibited</h2>');
}

if($prefs_userregistration != 'yes') {
	die("unauthorized access");
}

$send_data = 'true';

// all incoming data -> strip_tags
// limit/trim string to 200 characters
foreach($_POST as $key => $val) {
	$$key = strip_tags(substr($val, 0, 200)); 
}

if($accept_terms == '') {
	$send_data = 'false';
	$register_message = $lang['msg_register_accept'].'<br>';
}

//required fields
if( ($username == "") || ($psw == "") || ($mail == "")  ){
	$send_data = "false";
	$register_message .= $lang['msg_register_requiredfields'].'<br>';
}

//mail and mailrepeat
if($mail != $mailrepeat) {
	$send_data = 'false';
	$register_message .= $lang['msg_register_mailrepeat_error'].'<br>';
}

if(!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
	$send_data = 'false';
	$register_message .= $lang['msg_invalid_mail_format'].'<br>';
}

//psw and psw_repeat
if($psw != $psw_repeat) {
	$send_data = 'false';
	$register_message .= $lang['msg_register_pswrepeat_error'].'<br>';
}


//no special chars are allowed
if(!preg_match("/^[a-zA-Z0-9-_]{2,20}$/",$username)) {
	$send_data = "false";
	$register_message .= $lang['msg_register_userchars'].'<br>';
}


//check existing usernames
$all_usernames_array = get_all_usernames("$fc_db_user");

foreach ($all_usernames_array as $entry) {
    if($username == $entry['user_nick']) {
    	$send_data = "false";
			$register_message .= $lang['msg_register_existinguser'].'<br>';
			break;
    }
}


//check existing E-Mail Adresses
$all_usermail_array = get_all_usermail("$fc_db_user");

foreach ($all_usermail_array as $entry) {    
    if($mail == $entry['user_mail']) {
    	$send_data = "false";
			$register_message .= $lang['msg_register_existingusermail'].'<br>';
			break;
    }  
}

echo '<hr>FOOOOOOO:'.$prefs_mailer_adr.'<hr>';

//yeah, create the new account
if($send_data == 'true') {

	$user_groups = "1";
	$user_registerdate = time();
	$user_verified = 'waiting';
	$drm_string = '';
	$psw_string = md5("$psw$username");
	$user_psw_hash = password_hash($psw, PASSWORD_DEFAULT);
	$user_activationkey = random_text('alnum',32);
	$activation_url = $fc_base_url."?p=account&user=$username&al=$user_activationkey";
	$user_activationlink = '<a href="'.$activation_url.'">'.$activation_url.'</a>';

	$db_user->insert("fc_user", [
		"user_nick" => "$username",
		"user_registerdate" => "$user_registerdate",
		"user_verified" => "$user_verified",
		"user_groups" => "$user_groups",
		"user_drm" => "$drm_string",
		"user_firstname" => "$firstname",
		"user_lastname" => "$name",
		"user_company" => "$user_company",
		"user_street" => "$street",
		"user_street_nbr" => "$nr",
		"user_zipcode" => "$zip",
		"user_city" => "$city",
		"user_public_profile" => "$about_you",
		"user_mail" => "$mail",
		"user_newsletter" => "$user_newsletter",
		"user_psw_hash" => "$user_psw_hash",
		"user_activationkey" => "$user_activationkey"
	]);	
	
	/* generate the message */
	$email_msg = get_textlib("account_confirm_mail","$languagePack");
	$email_msg = str_replace("{USERNAME}","$username",$email_msg);
	$email_msg = str_replace("{SITENAME}","$prefs_pagetitle",$email_msg);
	$email_msg = str_replace("{ACTIVATIONLINK}","$user_activationlink",$email_msg);
	
	/* send register mail to the new user */
	require_once("lib/Swift/lib/swift_required.php");
		
	$transport = Swift_SmtpTransport::newInstance()
      ->setUsername("$prefs_smtp_username")
      ->setPassword("$prefs_smtp_psw")
      ->setHost("$prefs_smtp_host")
      ->setPort($prefs_smtp_port);
			
	if($prefs_mail_smtp_encryption_input != '') {
		$transport->setEncryption($prefs_smtp_encryption);
	}

	$mailer = Swift_Mailer::newInstance($transport);
	$message = Swift_Message::newInstance()
			->setSubject("Account | $prefs_pagetitle")
  		->setFrom(array($prefs_mailer_adr => $prefs_mailer_name))
  		->setTo(array($mail => $username))
  		->setBody("$email_msg", 'text/html');

  $send_message = $mailer->send($message, $failures);
	
	$smarty->assign("msg_status","alert alert-success",true);
	$smarty->assign("register_message",$lang['msg_register_success'],true);
	
	record_log("user_register","new user $username","6");
	
	$admin_notification_text  = $lang['msg_register_admin_notification_text'].'<hr>';
	$admin_notification_text .= 'Username: <b>'.$username.'</b><br>';
	$admin_notification_text .= 'E-Mail: '.$mail.'<br>';
	$admin_notification_text .= 'Server: '.$_SERVER['SERVER_NAME'].'<br>';
	mailto_admin("$lang[msg_register_admin_notification_subject]","$admin_notification_text");

} else {
	//oh no, don't create an new account
	
	$smarty->assign("msg_status","alert alert-danger",true);
	$smarty->assign("register_message",'<p><strong>'.$lang['msg_register_error'].'</strong></p><p>'.$register_message.'</p>',true);
	
	//show the entries again
	$smarty->assign("send_username",$username,true);
	$smarty->assign("send_mail",$mail,true);
	$smarty->assign("send_mailrepeat",$mailrepeat,true);
	$smarty->assign("send_firstname",$firstname,true);
	$smarty->assign("send_name",$name,true);
	$smarty->assign("send_zip",$zip,true);
	$smarty->assign("send_city",$city,true);
	$smarty->assign("send_street",$street,true);
	$smarty->assign("send_nr",$nr,true);
	$smarty->assign("about_you",$about_you,true);
}



?>