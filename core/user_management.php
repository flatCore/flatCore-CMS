<?php

/**
 * USER LOGIN
 */

unset($status_msg);


if($goto == "logout") {
	session_destroy();
	unset($_SESSION[user_nick]);
	$smarty->assign("msg_status","success");
	$smarty->assign('msg_text', "$lang[msg_signed_out]");
	$output = $smarty->fetch("status_message.tpl");
	$smarty->assign('msg_content', $output);
}


if(isset($_POST[login])) {

	$login_name = strip_tags($_POST[login_name]);
	$login_psw  = strip_tags($_POST[login_psw]);
	
	$dbh = new PDO("sqlite:$fc_db_user");
	
	$sql = "SELECT user_id, user_class, user_nick, user_mail, user_psw, user_drm, user_firstname, user_lastname
			FROM fc_user
			WHERE user_nick = :login_name AND user_verified = 'verified' ";
		
	$sth = $dbh->prepare($sql);
	$sth->bindValue(':login_name', "$login_name", PDO::PARAM_STR);
	$sth->execute();
	
	$result = $sth->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	
	$login_hash  = "$login_psw"."$login_name";
	
	// check userdata - write data in session
	if(md5($login_hash) == "$result[user_psw]") {
		$_SESSION[user_id] = "$result[user_id]";
		$_SESSION[user_nick] = "$result[user_nick]";
		$_SESSION[user_class] = "$result[user_class]";
		$_SESSION[user_psw] = "$result[user_psw]";
		$_SESSION[user_firstname] = "$result[user_firstname]";
		$_SESSION[user_lastname] = "$result[user_lastname]";
		$_SESSION[user_mail] = "$result[user_mail]";
	
		$arr_drm = explode("|", $result[user_drm]);
	
		if($arr_drm[0] == "drm_acp_pages")	{  $_SESSION[acp_pages] = "allowed";  }
		if($arr_drm[1] == "drm_acp_files")	{  $_SESSION[acp_files] = "allowed";  }
		if($arr_drm[2] == "drm_acp_user")	{  $_SESSION[acp_user] = "allowed";  }
		if($arr_drm[3] == "drm_acp_system")	{  $_SESSION[acp_system] = "allowed";  }
		if($arr_drm[4] == "drm_acp_editpages")	{  $_SESSION[acp_editpages] = "allowed";  }
		if($arr_drm[5] == "drm_acp_editownpages")	{  $_SESSION[acp_editownpages] = "allowed";  }
		if($arr_drm[6] == "drm_moderator")	{  $_SESSION[drm_moderator] = "allowed";  }
		if($arr_drm[7] == "drm_can_publish")	{  $_SESSION[drm_can_publish] = "true";  }
	
	} else {
		session_destroy(); // NO correct login
	}


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
		
		
		if($fc_mod_rewrite == "auto") {
			$show_register_link = FC_INC_DIR . "/system/register/";
			$show_forgotten_psw_link = FC_INC_DIR . "/system/password/";
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