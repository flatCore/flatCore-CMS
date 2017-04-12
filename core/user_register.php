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



//yeah, create the new account
if($send_data == 'true') {

	$user_groups = "1";
	$user_registerdate = time();
	$user_verified = 'waiting';
	$drm_string = '';
	$psw_string = md5("$psw$username");
	$user_psw_hash = password_hash($psw, PASSWORD_DEFAULT);
	
	$user_activationkey = md5("$username$user_registerdate");
	$activation_url = "http://$_SERVER[HTTP_HOST]$_SERVER[SCRIPT_NAME]?p=account&user=$username&al=$user_activationkey";
	$user_activationlink = "<a href='$activation_url'>$activation_url</a>";
	
	$dbh = new PDO("sqlite:$fc_db_user");
	
	$sql = "INSERT INTO fc_user (
			user_id, user_nick, user_registerdate, user_verified, user_groups,
			user_drm, user_firstname, user_lastname, user_company,
			user_street, user_street_nbr, user_zipcode, user_city, user_public_profile,
			user_mail, user_newsletter, user_psw_hash, user_activationkey
			) VALUES (
			NULL,
			:username, :user_registerdate, :user_verified, :user_groups,
			:drm_string, :firstname, :name, :user_company,
			:street, :nr, :zip, :city, :about_you,
			:mail, :user_newsletter, :user_psw_hash, :user_activationkey )";
	
	$sth = $dbh->prepare($sql);
	
	$sth->bindParam(':username', $username, PDO::PARAM_STR);
	$sth->bindParam(':user_registerdate', $user_registerdate, PDO::PARAM_STR);
	$sth->bindParam(':user_verified', $user_verified, PDO::PARAM_STR);
	$sth->bindParam(':user_groups', $user_groups, PDO::PARAM_STR);
	$sth->bindParam(':drm_string', $drm_string, PDO::PARAM_STR);
	$sth->bindParam(':firstname', $firstname, PDO::PARAM_STR);
	$sth->bindParam(':name', $name, PDO::PARAM_STR);
	$sth->bindParam(':user_company', $user_company, PDO::PARAM_STR);
	$sth->bindParam(':street', $street, PDO::PARAM_STR);
	$sth->bindParam(':nr', $nr, PDO::PARAM_STR);
	$sth->bindParam(':zip', $zip, PDO::PARAM_STR);
	$sth->bindParam(':city', $city, PDO::PARAM_STR);
	$sth->bindParam(':about_you', $about_you, PDO::PARAM_STR);
	$sth->bindParam(':mail', $mail, PDO::PARAM_STR);
	$sth->bindParam(':user_newsletter', $user_newsletter, PDO::PARAM_STR);
	$sth->bindParam(':user_psw_hash', $user_psw_hash, PDO::PARAM_STR);
	$sth->bindParam(':user_activationkey', $user_activationkey, PDO::PARAM_STR);
	
	$count = $sth->execute();
	$dbh = null;
	
	/* generate the message */
	$email_msg = get_textlib("account_confirm_mail","$languagePack");
	$email_msg = str_replace("{USERNAME}","$username",$email_msg);
	$email_msg = str_replace("{SITENAME}","$prefs_pagetitle",$email_msg);
	$email_msg = str_replace("{ACTIVATIONLINK}","$user_activationlink",$email_msg);
	
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
			->setSubject("Registrierungsdaten | $prefs_pagetitle")
  		->setFrom(array("$prefs_mailer_adr" => "$prefs_mailer_name"))
  		->setTo(array("$mail" => "$username"))
  		->setBody("$email_msg", 'text/html');
  $result = $mailer->send($message);
	
	$smarty->assign("msg_status","alert alert-success");
	$smarty->assign("register_message",$lang['msg_register_success']);
	
	record_log("user_register","new user $username","6");
	
	$admin_notification_text  = $lang['msg_register_admin_notification_text'].'<hr>';
	$admin_notification_text .= 'Username: <b>'.$username.'</b><br>';
	$admin_notification_text .= 'E-Mail: '.$mail.'<br>';
	$admin_notification_text .= 'Server: '.$_SERVER['SERVER_NAME'].'<br>';
	mailto_admin("$lang[msg_register_admin_notification_subject]","$admin_notification_text");

} else {
	//oh no, don't create an new account
	
	$smarty->assign("msg_status","alert alert-danger");
	$smarty->assign("register_message",'<p><strong>'.$lang['msg_register_error'].'</strong></p><p>'.$register_message.'</p>');
	
	//show the entries again
	$smarty->assign("send_username","$username");
	$smarty->assign("send_mail","$mail");
	$smarty->assign("send_mailrepeat","$mailrepeat");
	$smarty->assign("send_firstname","$firstname");
	$smarty->assign("send_name","$name");
	$smarty->assign("send_zip","$zip");
	$smarty->assign("send_city","$city");
	$smarty->assign("send_street","$street");
	$smarty->assign("send_nr","$nr");
	$smarty->assign("about_you","$about_you");
}



?>