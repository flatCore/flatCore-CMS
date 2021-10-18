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

if($page_favicon == "") {
	$page_favicon = "$prefs_pagefavicon";
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
/* shortcodes will be replaced in text_parser */
$shortcodes = fc_get_shortcodes();

foreach($mainmenu as $k => $v) {
	$mainmenu[$k]['page_linkname'] = text_parser($mainmenu[$k]['page_linkname']);
}

if(is_array($submenu)) {
	foreach($submenu as $k => $v) {
		$submenu[$k]['page_linkname'] = text_parser($submenu[$k]['page_linkname']);
	}
}

if(is_array($bcmenu)) {
	foreach($bcmenu as $k => $v) {
		$bcmenu[$k]['page_linkname'] = text_parser($bcmenu[$k]['page_linkname']);
	}
}

$arr_mainmenu = array_filter(array_values($mainmenu));
$arr_subnmenu = array_filter(array_values($submenu));

/* get the last key - it's the Home Link  */
$last_key = array_key_last($arr_mainmenu);

$smarty->assign('homepage_linkname', text_parser($arr_mainmenu[$last_key]['homepage_linkname']));
$smarty->assign('homepage_title', $arr_mainmenu[$last_key]['homepage_title']);
$smarty->assign('homepage_permalink', $arr_mainmenu[$last_key]['homepage_permalink']);

unset($arr_mainmenu[$last_key]['homepage_linkname'],$arr_mainmenu[$last_key]['homepage_title'],$arr_mainmenu[$last_key]['homepage_permalink'],$arr_mainmenu[$last_key]['page_linkname']);
$arr_mainmenu = array_filter(array_values($arr_mainmenu));

$smarty->assign('link_home', FC_INC_DIR . "/");
$smarty->assign('arr_menue', $arr_mainmenu);
$smarty->assign('arr_bcmenue', $bcmenu);
$smarty->assign('fc_sitemap', $fc_sitemap);

/* submenu only if $submenu != empty */
if(is_array($submenu) && count($submenu) >= 1) {
	$smarty->assign('arr_submenue', $arr_subnmenu);
	$smarty->assign('legend_toc', text_parser(FC_TOC_HEADER));
}

if($page_contents['page_sort'] == 'portal' OR $p == '') {
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
	$smarty->assign('modul_head_enhanced', $modul_head_enhanced, true);
	
	/* overwrite page's values by module */
	if($mod['page_title'] != "") {
		$page_title = $mod['page_title'];
	}
	if($mod['page_thumbnail'] != "") {
		$page_thumbnail = $mod['page_thumbnail'];
	}
	if($mod['page_favicon'] != "") {
		$page_favicon = $mod['page_favicon'];
	}
	if($mod['page_description'] != "") {
		$page_meta_description = $mod['page_description'];
	}	
	if($mod['page_keywords'] != "") {
		$page_meta_keywords = $mod['page_keywords'];
	}
	if($mod['page_robots'] != "") {
		$page_meta_robots = $mod['page_robots'];
	}
}


/* parse [include] [script] [plugin] etc. */
$parsed_content = text_parser($page_content.$modul_content);

if($parsed_content != $page_content) {
	$smarty->assign('page_content', $parsed_content,true);
} else {
	$smarty->assign('page_content', $page_content);
}

/**
 * check if page is protected
 * if post psw, store md5 hash in session
 * unset session via ?reset_page_psw
 */

if(isset($_POST['page_psw']) && $_POST['page_psw'] != '') {
	if(md5($_POST['page_psw']) === $page_psw) {
		$_SESSION['page_psw'] = md5($_POST['page_psw']);
	}
}

if(isset($_GET['reset_page_psw'])) {
	unset($_SESSION['page_psw']);
}

if($page_psw !== '' && $_SESSION['page_psw'] !== $page_psw) {
	$formaction = FC_INC_DIR . '/'.$fct_slug;
	$page_title = 'Password Protected Page';
	$page_meta_robots = 'noindex';
	
	$smarty->assign('formaction', $formaction);
	$smarty->assign('button_send', $lang['button_login']);
	$smarty->assign('label_psw_protected_page', $lang['label_psw_protected_page']);
	
	$output = $smarty->fetch("page_psw_input.tpl");
	$smarty->assign('page_content', $output);
	$smarty->assign('extra_content', "");
}


/* page thumbnails */

if($page_thumbnail == "") {
	$page_thumbnail = $prefs_pagethumbnail;
} else {
	$page_thumbnail_array = explode("<->", $page_thumbnail);
	$page_thumbnail = $page_thumbnail_array[0];
	if(count($page_thumbnail_array > 0)) {
		$page_thumbnail = array_shift($page_thumbnail_array);
		foreach($page_thumbnail_array as $t) {
			$t = str_replace('/content/', $fc_base_url.'content/', $t);
			$thumb[] = $t;
		}
		$smarty->assign('page_thumbnails', $thumb);
	}
}

/* fix path to thumbnails and favicon */
$page_thumbnail = str_replace('../content/', $fc_base_url.'content/', $page_thumbnail);
$page_favicon = str_replace('../content/', $fc_base_url.'content/', $page_favicon);

$smarty->assign('page_title', $page_title);
$smarty->assign('prefs_pagesglobalhead', $prefs_pagesglobalhead);
$smarty->assign('page_meta_author', $page_meta_author);
$smarty->assign('page_meta_date', date('Y-m-d', $page_lastedit));
$smarty->assign('page_meta_keywords', $page_meta_keywords);
$smarty->assign('page_meta_description', $page_meta_description);
$smarty->assign('page_thumbnail', $page_thumbnail);
$smarty->assign('page_favicon', $page_favicon);
$smarty->assign('page_hash', $page_hash);

if($page_meta_robots == "") {
	$page_meta_robots = "all";
}

if($page_status == 'draft') {
	$page_meta_robots = 'noindex, nofollow';
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
	include $le_cache_file;
} else {
	$arr_lastedit = get_lastedit();
}

for($i=0;$i<5;$i++) {
	$arr_lastedit[$i]['page_linkname'] = text_parser($arr_lastedit[$i]['page_linkname']);
}
$smarty->assign('arr_lastedit', $arr_lastedit);


/* most clicked */
$mc_cache_file = FC_CONTENT_DIR . "/cache/cache_mostclicked.php";
if(is_file("$mc_cache_file")) {
	$cache_life = 86400; // cache lifetime -> seconds
	if(time() - filemtime("$mc_cache_file") >= $cache_life) {
		cache_most_clicked();
	}
	include $mc_cache_file;
} else {
	$arr_mostclicked = get_most_clicked();
	cache_most_clicked();
}

for($i=0;$i<5;$i++) {
	$arr_mostclicked[$i]['linkname'] = text_parser($arr_mostclicked[$i]['linkname']);
}
$smarty->assign('arr_mostclicked', $arr_mostclicked,true);


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
	$text = get_textlib("no_access", $languagePack);
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
		$text = get_textlib("no_access", $languagePack);
		$smarty->assign('page_content', $text);
		$smarty->assign('extra_content', "");
	}

}


/* draft pages for administrators only */
if(($page_status == "draft") AND ($_SESSION['user_class'] != "administrator")){
	$text = get_textlib("no_access", $languagePack);
	$smarty->assign('page_content', $text);
	$smarty->assign('extra_content', "");
}


/* comments */

if(($page_comments == 1 OR $post_data['post_comments'] == 1) && $prefs_comments_mode != 3) {
	/* comments are activated for this page */
	
	$show_comments_form = FALSE;
	
	if($prefs_comments_authorization == 1 && $_SESSION['user_nick'] != '') {
		// comments allowed for registered users
		$show_comments_form = TRUE;
		$smarty->assign("comment_name_readonly","readonly");
		$smarty->assign("input_name",$_SESSION['user_nick']);
		$smarty->assign("comment_mail_readonly","readonly");
		$smarty->assign("input_mail",$_SESSION['user_mail']);
	}
	
	if($prefs_comments_authorization == 3) {
		// comments allowed for all
		$show_comments_form = TRUE;
	}
	
	if($prefs_comments_authorization == 2) {
		// comments allowed for all - name and E-Mail are mandatory
		$show_comments_form = TRUE;
		$comment_form_intro = $lang['comment_msg_auth2'];
	}

	if(isset($_POST['send_user_comment'])) {
		$save_comment = fc_write_comment($_POST);

		if($save_comment > 0) {
			$form_response = '<div class="alert alert-success">'.$lang['comment_msg_sucess'].'</div>';
		} else {
			$form_response = '<div class="alert alert-danger">'.$lang['comment_msg_fail'].'</div>';
		}
		
		$smarty->assign("form_response",$form_response);
	}
	

	if($show_comments_form === TRUE) {
		$smarty->assign("label_name",$lang['label_name']);
		$smarty->assign("label_mail",$lang['label_mail']);
		$smarty->assign("label_mail_helptext",$lang['label_mail_helptext']);
		$smarty->assign("btn_send_comment",$lang['btn_send_comment']);
		$smarty->assign("post_id",$post_data['post_id']);
		
		$form_action = '/'.$fct_slug.$mod_slug;
		$smarty->assign("form_action",$form_action);
		
		$smarty->assign("label_comment",$lang['label_comment']);
		if(isset($_GET['cid']) && is_numeric($_GET['cid'])) {
			$cid = (int) $_GET['cid'];
			$smarty->assign("label_comment",$lang['label_comment_answer'].' #'.$cid);
			$smarty->assign("parent_id",$cid);
		}
	
		$smarty->assign("comment_form_title",$lang['comment_form_title']);
		$smarty->assign("comment_form_intro",$comment_form_intro);
		$comments_form = $smarty->fetch("comments/comment_form.tpl",$cache_id);
		$smarty->assign('comment_form', $comments_form, true);
		$smarty->assign('comment_send_success', $fc_snippet_comment_send_success, true);
		
	}
	
	/* show stored comments */
	
	if(is_numeric($page_contents['page_id'])) {
		$filter['relation_id'] = (int) $page_contents['page_id'];
		$filter['type'] = 'p';
	}
	
	if(is_numeric($post_data['post_id'])) {
		$filter['relation_id'] = (int) $post_data['post_id'];
		$filter['type'] = 'b';
	}
	
	if(is_file(FC_CORE_DIR.'/styles/'.$fc_template.'/templates/comments/comment_entry.tpl')){
		$comment_tpl = file_get_contents(FC_CORE_DIR.'/styles/'.$fc_template.'/templates/comments/comment_entry.tpl');
	} else {
		$comment_tpl = file_get_contents(FC_CORE_DIR.'/styles/default/templates/comments/comment_entry.tpl');
	}
	
	$comments = fc_get_comments(0,100,$filter);
	$cnt_comment = count($comments);
	
	$sorting = [];
	foreach ($comments as $comment_key => $comment) {
		if($comment['comment_parent_id'] == '') {
			$comment['comment_parent_id'] = 0;
		}
	  $sorting[$comment['comment_parent_id']][$comment_key] = $comment['comment_id'];
	}

	$thread = fc_list_comments_thread($comments, $sorting, $comment_tpl, 0);

	$smarty->assign('show_page_comments', 'true', true);
	$smarty->assign('comments_thread', $thread);
	$comments_title = str_replace('{cnt_comments}', $cnt_comment, $lang['comments_title']);
	$smarty->assign('comments_intro', "<p>$comments_title</p>");

}



/* register */

if($p == "register") {

	if($page_contents['page_permalink'] != '') {
		$smarty->assign("form_url", '/'.$page_contents['page_permalink']);
	} else {
		$form_url = FC_INC_DIR . "/register/";
		$smarty->assign("form_url","$form_url");
	}

	if($prefs_userregistration != "yes") {
	
		$smarty->assign("msg_title",$lang['legend_register']);
		$smarty->assign("msg_text",$lang['msg_register_intro_disabled']);	
		$output = $smarty->fetch("status_message.tpl",$cache_id);
		$smarty->assign('page_content', $output, true);
	
	} else {
		
		// INCLUDE/SHOW AGREEMENT TEXT
		$agreement_txt = get_textlib("agreement_text", $languagePack);
		$smarty->assign("agreement_text",$agreement_txt);
	
		if($_POST['send_registerform']) {
			include 'user_register.php';
		}
	
		$output = $smarty->fetch("registerform.tpl",$cache_id);
		$smarty->assign('page_content', $output, true);
	
	}
}


/* confirm new account */
if($p == "account") {
	
	$user = fc_return_clean_value($_GET['user']);
	$al = fc_return_clean_value($_GET['al']);
	
	$verify = $db_content->update("fc_user", [
		"user_verified" => 'verified'
		], [
			"AND" => [
			"user_nick" => $user,
			"user_activationkey" => $al
		]
	]);
	
	$cnt_changes = $verify->rowCount();
	
	
	if($cnt_changes > 0){
		$account_msg = get_textlib("account_confirm", $languagePack);
		$account_msg = str_replace("{USERNAME}","$user",$account_msg);
		record_log("switch","user activated via mail - $user","5");
	} else {
		$account_msg = "";
	}
	
	$smarty->assign('page_content', $account_msg, true);
}


/* edit profile */
if(($p == "profile" OR $page_type_of_use == 'profile') AND ($goto != "logout")) {
	include 'user_updateprofile.php';
}


/* include search */
if($p == 'search' OR $page_permalink == 'suche/' OR $page_permalink == 'search/' OR $page_type_of_use == 'search') {
	include 'search.php';
}

/* forgotten password */
if($p == "password" OR $page_type_of_use == 'password') {
	include 'password.php';
}


if($p == "404") {
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	
	if($page_contents['page_permalink'] == '') {
	
		$smarty->assign('page_title', "404 Page Not Found");
		$output = $smarty->fetch("404.tpl");
		$smarty->assign('page_content', $output);
	}
	
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
