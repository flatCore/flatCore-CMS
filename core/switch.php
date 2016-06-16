<?php

if(is_array($page_contents)) {

	foreach($page_contents as $k => $v) {
  	$$k = stripslashes($v);
   	
   	/* if we have custom fields, assign the values to smarty */
   	if(preg_match("/custom_/i", $k)) {
	   	$v = text_parser($v);
   		$smarty->assign("$k", stripslashes($v));
   	}
	}
} else {
	$show_404 = "true";
}

$current_page_sort = "$page_sort";

if($page_title == "") {
	$page_title = "$prefs_pagetitle";
}
if($page_thumbnail == "") {
	$page_thumbnail = "$prefs_pagethumbnail";
}


/**
 * gereate mainmenu, submenu, breadcrumps and sitemap
 */

$mainmenu = array();
$submenu = array();

$mainmenu = show_mainmenu();
$submenu = show_menu($current_page_sort);
$bcmenu = breadcrumbs_menu($current_page_sort);
$fc_sitemap = show_sitemap();

$arr_mainmenu = @array_values($mainmenu);
$arr_subnmenu = @array_values($submenu);

$smarty->assign('link_home', FC_INC_DIR . "/");
$smarty->assign('arr_menue', $arr_mainmenu);
$smarty->assign('arr_bcmenue', $bcmenu);
$smarty->assign('fc_sitemap', $fc_sitemap);

/* submenu only if $submenu != empty */
if(count($submenu) >= 1) {
	$smarty->assign('arr_submenue', $arr_subnmenu);
	$smarty->assign('legend_toc', FC_TOC_HEADER);
}


if($p == "") {
	$smarty->assign('homelink_status', "$fc_defs[main_nav_class_active]");
} else {
	$smarty->assign('homelink_status', "$fc_defs[main_nav_class]");
}

$smarty->assign('body_template', $fc_template_layout);


$textlib_footer = get_textlib('footer_text',"$languagePack");
$textlib_global_extracontent = get_textlib('extra_content_text',"$languagePack");

$textlibs = get_all_textlibs();

$cnt_textlibs = count($textlibs);

for($i=0;$i<$cnt_textlibs;$i++) {
	$snippet_lang = $textlibs[$i]['textlib_lang'];
	$snippet_key = "fc_snippet_" . str_replace("-","_",$textlibs[$i]['textlib_name']);
	/* assign the correct snippet by $languagePack */
	if($snippet_lang == $languagePack) {
		$smarty->assign("$snippet_key", text_parser(stripslashes($textlibs[$i]['textlib_content'])));
		$matched_snippets[] = $textlibs[$i]['textlib_name'];
	}
	/* if we have no match by $languagePack - assign the last snippet with the same textlib_name */
	if(!in_array($textlibs[$i]['textlib_name'], $matched_snippets)) {
		$smarty->assign("$snippet_key", text_parser(stripslashes($textlibs[$i]['textlib_content'])));
	}
}


/* include modul */
if($page_modul != "") {
	include("modules/$page_modul/index.php");
	$page_content = "$page_content $modul_content";
	$smarty->assign('modul_head_enhanced', $modul_head_enhanced);
}


/* parse [include] [script] [plugin] etc. */
$page_content = text_parser($page_content);

$smarty->assign('page_content', "$page_content");
$smarty->assign('page_title', "$page_title");
$smarty->assign('prefs_pagesglobalhead', "$prefs_pagesglobalhead");
$smarty->assign('page_meta_author', $page_meta_author);
$smarty->assign('page_meta_date', date('Y-m-d', $page_lastedit));
$smarty->assign('page_meta_keywords', $page_meta_keywords);
$smarty->assign('page_meta_description', $page_meta_description);
$smarty->assign('page_thumbnail', $page_thumbnail);
$smarty->assign('page_hash', $page_hash);

if($page_meta_robots == "") {
	$page_meta_robots = "all";
}

$smarty->assign('page_meta_robots', $page_meta_robots);
$smarty->assign('page_meta_enhanced', $page_meta_enhanced);

if($page_head_styles != "") {
	$smarty->assign('page_head_styles', "<style type=\"text/css\"> $page_head_styles </style>\n");
}

$smarty->assign('page_head_enhanced', $page_head_enhanced);

$textlib_footer = text_parser($textlib_footer);
$smarty->assign("textlib_footer","$textlib_footer");

/* show extra content if != empty */
if($page_extracontent != "") {
	$page_extracontent = text_parser($page_extracontent);
	$smarty->assign('page_extracontent', stripslashes($page_extracontent));
}

/* show global extra content if != empty */
if($textlib_global_extracontent != "") {
	$textlib_global_extracontent = text_parser($textlib_global_extracontent);
	$smarty->assign('page_global_extracontent', $textlib_global_extracontent);
}



/* last edit */
$le_cache_file = FC_CONTENT_DIR . "/cache/cache_lastedit.php";
if(is_file("$le_cache_file")) {
	include("$le_cache_file");
} else {
	$arr_lastedit = get_lastedit();
}

$smarty->assign('arr_lastedit', $arr_lastedit);


/* most clicked */
$mc_cache_file = FC_CONTENT_DIR . "/cache/cache_mostclicked.php";
if(is_file("$mc_cache_file")) {
	$cache_life = 86400; // cache lifetime -> seconds
	if(time() - filemtime("$mc_cache_file") >= $cache_life) {
		cache_most_clicked();
	}
	include("$mc_cache_file");
} else {
	$arr_mostclicked = get_most_clicked();
	cache_most_clicked();
}

$smarty->assign('arr_mostclicked', $arr_mostclicked);


/* tags */
$tc_cache_file = FC_CONTENT_DIR . "/cache/cache_keywords.html";
if(is_file("$tc_cache_file")) {
	$page_keywords = file_get_contents($tc_cache_file);
} else {
	$page_keywords = get_keywords();
}

$smarty->assign('page_keywords', $page_keywords);


/* private pages, for admins only */
if(($page_status == "private") AND ($_SESSION['user_class'] != "administrator")) {
	$text = get_textlib("no_access");
	$smarty->assign('page_content', $text);
	$smarty->assign('extra_content', "");
}


/**
 * pages for usergroups
 * -> access if $_SESSION[user_id] is in selected usergroups
 * -> access for administrators
 */
if($page_usergroup != "") {

	$arr_checked_groups = explode("<|-|>",$page_usergroup);

	for($i=0;$i<count($arr_checked_groups);$i++) {
		$is_user_in_group[] = is_user_in_group("$_SESSION[user_id]","$arr_checked_groups[$i]");
	}

	if((!in_array("true",$is_user_in_group)) AND ($_SESSION['user_class'] != "administrator")) {
		$text = get_textlib("no_access");
		$smarty->assign('page_content', $text);
		$smarty->assign('extra_content', "");
	}

}


/* draft pages for administrators only */
if(($page_status == "draft") AND ($_SESSION['user_class'] != "administrator")){
	$text = get_textlib("no_access");
	$smarty->assign('page_content', $text);
	$smarty->assign('extra_content', "");
}



if($p == "register") {

	$form_url = FC_INC_DIR . "/register/";
	$smarty->assign("form_url","$form_url");

	if($prefs_userregistration != "yes") {
	
		$smarty->assign("msg_title","$lang[legend_register]");
		$smarty->assign("msg_text","$lang[msg_register_intro_disabled]");	
		$output = $smarty->fetch("status_message.tpl");
		$smarty->assign('page_content', $output);
	
	} else {
		
		// INCLUDE/SHOW AGREEMENT TEXT
		$agreement_txt = get_textlib("agreement_text");
		$smarty->assign("agreement_text","$agreement_txt");
	
		if($_POST['send_registerform']) {
			include("user_register.php");
		}
	
		$output = $smarty->fetch("registerform.tpl");
		$smarty->assign('page_content', $output);
	
	}
}


/* confirm new account */
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


/* edit profile */
if(($p == "profile") AND ($goto != "logout")) {
	include("user_updateprofile.php");
}


/* include search */
if($p == "search") {
	include("search.php");
}

/* forgotten password */
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