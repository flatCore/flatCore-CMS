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

if($p == "404") {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	$smarty->assign('page_title', "404 Page Not Found");
	$output = $smarty->fetch("404.tpl");
	$smarty->assign('page_content', $output);
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
	$output = $smarty->fetch("404.tpl");
	$smarty->assign('page_content', $output);
}


?>