<?php

/**
 * USER LOGIN
 */

unset($status_msg);


if(isset($_POST['login'])) {
	
	$remember = false;
	if(isset($_POST['remember_me'])) {
		$remember = true;
	}
	
	fc_user_login($_POST['login_name'],$_POST['login_psw'],$acp=FALSE,$remember);
}



/**
 * show the login form or the user navigation
 */

if($_SESSION['user_nick'] != "") {

	$status_msg = $lang['msg_login_true'];
	$link_logout = $fc_base_url.'logout';
	$link_profile = FC_INC_DIR . "/profile/";

	/* user == administrator */
	if($_SESSION['user_class'] == "administrator"){
			$link_acp = FC_INC_DIR . "/" . FC_ACP_DIR . "/acp.php";
			if(is_numeric($page_contents['page_id'])) {
				$link_edit_page = FC_INC_DIR . '/' . FC_ACP_DIR . '/acp.php?tn=pages&sub=edit&editpage='.$page_contents['page_id'];
			} else {
				$link_edit_page = FC_INC_DIR . '/' . FC_ACP_DIR . '/acp.php?tn=pages';
			}
		} else {
			unset($link_acp,$lang['button_acp']);
	}
	
	$smarty->assign('status_msg', $status_msg,true);
	$smarty->assign('link_profile', $link_profile);
	$smarty->assign('lang_button_profile', $lang['button_profile']);
	$smarty->assign("link_logout","$link_logout");
	$smarty->assign('lang_button_logout', $lang['button_logout']);	
	$smarty->assign("link_acp","$link_acp");
	$smarty->assign('lang_button_acp', $lang['button_acp']);
	$smarty->assign("link_edit_page","$link_edit_page");
	$smarty->assign('lang_button_edit_page', $lang['button_acp_edit_page']);
	
	if(!isset($preview)) {
		$output = $smarty->fetch("statusbox.tpl",$cache_id);
		$smarty->assign('status_box', $output, true);
	}

} else {
	// show the login form
	
	if($prefs_showloginform == 'yes') {
		$smarty->assign("legend_login",$lang['legend_login']);
		$smarty->assign("label_login",$lang['label_login']);
		$smarty->assign("label_username",$lang['label_username']);
		$smarty->assign("label_psw",$lang['label_psw']);
		$smarty->assign("button_login",$lang['button_login']);
		$smarty->assign('status_msg', $status_msg);
		$smarty->assign('label_remember_me', $lang['label_remember_me']);
		$smarty->assign("p","$p");
		
		$show_register_link = FC_INC_DIR . "/register/";
		$show_forgotten_psw_link = FC_INC_DIR . "/password/";
		
		$smarty->assign("show_forgotten_psw_link","<a href='$show_forgotten_psw_link'>$lang[forgotten_psw]</a>");
		
		if($prefs_userregistration == "yes") {
			$smarty->assign("show_register_link","<a href='$show_register_link'>$lang[link_register]</a>");
			$smarty->assign("msg_register","$lang[msg_register]");
			$smarty->assign("link_register","$lang[link_register]");
		}
		
		$output = $smarty->fetch("loginbox.tpl",$cache_id);
		$smarty->assign('login_box', $output, true);
	}

} // eol show login form




?>