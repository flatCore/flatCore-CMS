<?php
//error_reporting(E_ALL ^E_NOTICE);

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
		$reset_token = random_text('alnum',32);
		$reset_link = $fc_base_url."password/?token=$reset_token";
		
		/* input token */
		$db_user->update("fc_user", [
				"user_reset_psw" => "$reset_token"
					], [
				"user_mail" => $mail
			]);
		
		/* generate the message */
		$email_msg = str_replace("{USERNAME}","$user_nick",$lang['forgotten_psw_mail_info']);
		$email_msg = str_replace("{RESET_LINK}","$reset_link",$email_msg);

        $mail_data['tpl'] = 'mail.tpl';
        $mail_data['subject'] = $lang['forgotten_psw_mail_subject'].' '.$prefs_pagetitle;
        $mail_data['preheader'] = $lang['forgotten_psw_mail_subject'].' '.$prefs_pagetitle;
        $mail_data['title'] = $lang['forgotten_psw_mail_subject'].' '.$prefs_pagetitle;
        $mail_data['salutation'] = "Password reset | $user_nick";
        $mail_data['body'] = "$email_msg";

        $build_html_mail = fc_build_html_file($mail_data);
		
		/* send register mail to the new user */

		$recipient = array('name' => $user_nick, 'mail' => $mail);
        $send_reset_mail = fc_send_mail($recipient,$mail_data['subject'],$build_html_mail);
		
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
	
	
	$db_user->update("fc_user", [
		"user_psw_hash" => "$update_user_psw",
		"user_reset_psw" => ""
	], [
		"user_id" => $user_id
	]);
	
	$email_msg = $lang['forgotten_psw_mail_update'];
	$email_msg = str_replace("{USERNAME}","$user_nick",$email_msg);
	$email_msg = str_replace("{temp_psw}","$temp_psw",$email_msg);

	/* send register mail to the new user */

    $mail_data['tpl'] = 'mail.tpl';
    $mail_data['subject'] = $lang['forgotten_psw_mail_subject'].' '.$prefs_pagetitle;
    $mail_data['preheader'] = $lang['forgotten_psw_mail_subject'].' '.$prefs_pagetitle;
    $mail_data['title'] = $lang['forgotten_psw_mail_subject'].' '.$prefs_pagetitle;
    $mail_data['salutation'] = "New Password | $user_nick";
    $mail_data['body'] = "$email_msg";

    $build_html_mail = fc_build_html_file($mail_data);

	$recipient = array('name' => $user_nick, 'mail' => $user_mail);
	$send_reset_mail = fc_send_mail($recipient,$mail_data['subject'],$build_html_mail);
	
	$psw_message = $lang['msg_forgotten_psw_step2'];
	
	if($psw_message != "") {
		$smarty->assign("msg_status","alert alert-info");
		$smarty->assign("psw_message","$psw_message");
	}


}

if($page_contents['page_permalink'] != '') {
	$smarty->assign("form_url", '/'.$page_contents['page_permalink']);
} else {
	$form_url = FC_INC_DIR . "/password/";
	$smarty->assign("form_url","$form_url");
}

$smarty->assign("forgotten_psw","$lang[forgotten_psw]");
$smarty->assign("forgotten_psw_intro","$lang[forgotten_psw_intro]");
$smarty->assign("label_mail","$lang[label_mail]");
$smarty->assign("button_send","$lang[button_send]");
$smarty->assign("legend_ask_for_psw","$lang[legend_ask_for_psw]");

$output = $smarty->fetch("password.tpl");
$smarty->assign('page_content', $output);

?>