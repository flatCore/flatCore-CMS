<?php

/**
 * USER LOGIN
 */

unset($status_msg);

if($goto == 'logout') {
	session_destroy();
	unset($_SESSION['user_nick']);
	$smarty->assign("msg_status","success");
	$smarty->assign('msg_text', "$lang[msg_logout]");
	$output = $smarty->fetch("status_message.tpl");
	$smarty->assign('msg_content', $output);
}


if(isset($_POST[login])) {

	$login_name = strip_tags($_POST['login_name']);
	$login_psw  = strip_tags($_POST['login_psw']);
	
	fc_user_login($login_name,$login_psw);


}



/**
 * show the login form or the user navigation
 */

if($_SESSION[user_nick] != "") {

	$status_msg = "$lang[msg_login_true]";
	$link_logout = "$_SERVER[PHP_SELF]?goto=logout";

	if($fc_mod_rewrite == "permalink") {
		$link_profile = FC_INC_DIR . "/profile/";
	} else {
		$link_profile = "$_SERVER[PHP_SELF]?p=profile";
	}



	/* user == administrator */
	if($_SESSION[user_class] == "administrator"){
			$link_acp = FC_INC_DIR . "/" . FC_ACP_DIR . "/acp.php";
			if($p != "") {
				$link_edit_page = FC_INC_DIR . "/" . FC_ACP_DIR . "/acp.php?tn=pages&sub=edit&editpage=$p";
			} else {
				$link_edit_page = FC_INC_DIR . "/" . FC_ACP_DIR . "/acp.php?tn=pages";
			}
		} else {
			unset($link_acp,$lang[button_acp]);
	}
	
	$smarty->assign('status_msg', $status_msg);
	$smarty->assign('link_profile', $link_profile);
	$smarty->assign('lang_button_profile', $lang[button_profile]);
	$smarty->assign("link_logout","$link_logout");
	$smarty->assign('lang_button_logout', $lang[button_logout]);	
	$smarty->assign("link_acp","$link_acp");
	$smarty->assign('lang_button_acp', $lang[button_acp]);
	$smarty->assign("link_edit_page","$link_edit_page");
	$smarty->assign('lang_button_edit_page', $lang[button_acp_edit_page]);
	
	if(!isset($preview)) {
		$output = $smarty->fetch("statusbox.tpl");
		$smarty->assign('status_box', $output);
	}

} else {
	// show the login form
	
	if($prefs_showloginform == 'yes') {
		$smarty->assign("legend_login","$lang[legend_login]");
		$smarty->assign("label_login","$lang[label_login]");
		$smarty->assign("label_username","$lang[label_username]");
		$smarty->assign("label_psw","$lang[label_psw]");
		$smarty->assign("button_login","$lang[button_login]");
		$smarty->assign('status_msg', $status_msg);
		$smarty->assign("p","$p");
		
		if($fc_mod_rewrite == "permalink") {
			$show_register_link = FC_INC_DIR . "/register/";
			$show_forgotten_psw_link = FC_INC_DIR . "/password/";
		} else {
			$show_register_link = "$_SERVER[PHP_SELF]?p=register";
			$show_forgotten_psw_link = "$_SERVER[PHP_SELF]?p=password";
		}
		
		$smarty->assign("show_forgotten_psw_link","<a href='$show_forgotten_psw_link'>$lang[forgotten_psw]</a>");
		
		if($prefs_userregistration == "yes") {
			$smarty->assign("show_register_link","<a href='$show_register_link'>$lang[link_register]</a>");
			$smarty->assign("msg_register","$lang[msg_register]");
			$smarty->assign("link_register","$lang[link_register]");
		}
		
		$output = $smarty->fetch("loginbox.tpl");
		$smarty->assign('login_box', $output);
	}

} // eol show login form




?>