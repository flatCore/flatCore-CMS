<?php


include("current_page.php");
include("nav.php");





/**
 * visitor - register -> SHOW THE FORM
 */

if($p == "register") {

	$form_url = "$_SERVER[PHP_SELF]?p=register";
	$smarty->assign("form_url","$form_url");

	if($pref_userregistration != "yes") {
	
		$smarty->assign("msg_title","$lang[legend_register]");
		$smarty->assign("msg_text","$lang[msg_register_intro_disabled]");
	
		$output = $smarty->fetch("status_message.tpl");
		$smarty->assign('page_content', $output);
	
	} else {
	
		$smarty->assign("legend_register","$lang[legend_register]");
		$smarty->assign("legend_required_fields","$lang[legend_required_fields]");
		$smarty->assign("legend_optional_fields","$lang[legend_optional_fields]");
	
		$smarty->assign("button_login","$lang[button_login]");
		$smarty->assign("label_firstname","$lang[label_firstname]");
		$smarty->assign("label_lastname","$lang[label_lastname]");
		$smarty->assign("label_username","$lang[label_username]");
		$smarty->assign("label_mail","$lang[label_mail]");
		$smarty->assign("label_mailrepeat","$lang[label_mailrepeat]");
		$smarty->assign("label_street","$lang[label_street]");
		$smarty->assign("label_nr","$lang[label_nr]");
		$smarty->assign("label_zip","$lang[label_zip]");
		$smarty->assign("label_town","$lang[label_town]");
		$smarty->assign("label_about_you","$lang[label_about_you]");
		$smarty->assign("label_psw","$lang[label_psw]");
		$smarty->assign("label_psw_repeat","$lang[label_psw_repeat]");
		$smarty->assign("button_send_register","$lang[button_send_register]");
	
		$smarty->assign("msg_register_intro","$lang[msg_register_intro]");
		$smarty->assign("msg_register_outro","$lang[msg_register_outro]");
	
	
		// INCLUDE/SHOW AGREEMENT TEXT
		$agreement_txt = get_textlib("agreement_text");
		$smarty->assign("agreement_text","$agreement_txt");
	
			if($_POST[send_registerform]) {
				include("user_register.php");
			}
	
		$output = $smarty->fetch("registerform.tpl");
		$smarty->assign('page_content', $output);
	
	}


}



/**
 * user - confirm new account
 */

if($p == "account") {

	$dbh = new PDO("sqlite:$fc_db_user");
	$sql = "UPDATE fc_user
					SET user_verified = 'verified'
					WHERE user_nick = '$user' AND user_activationkey = '$al' ";
	
	$cnt_changes = $dbh->exec($sql);
	$dbh = null;
	
	if($cnt_changes > 0){
		$account_msg = get_textlib("account_confirm");
		$account_msg = str_replace("{USERNAME}","$user",$account_msg);
		record_log("switch","user activated via mail - $user","5");
	} else {
		$account_msg = "";
	}
	
	$smarty->assign('page_content', $account_msg);

}



/**
 * user - edit profile -> show the form
 */

if(($p == "profile") AND ($goto != "logout")) {
	include("user_updateprofile.php");
}




/**
 * include search
 */

if($p == "search") {
	include("search.php");
}




/**
 * forgotten password
 */

if($p == "password") {
	include("password.php");
}


/**
 * show the sitemap
 */

if($p == "sitemap") {
	include("sitemap.php");
}



if($p == "404") {
	include("error.php");
	$show_404 = "false";
}


/**
 * no page, no content
 * assign the 404 template
 */


if((in_array("$p", $a_allowed_p)) OR ($p == "")) {
	$show_404 = "false";
}

if($show_404 == "true") {
	$smarty->assign("msg_404", "$lang[msg_404]");
	$smarty->assign("title_404", "$lang[title_404]");

	$output = $smarty->fetch("404.tpl");
	$smarty->assign('page_content', $output);
}


?>