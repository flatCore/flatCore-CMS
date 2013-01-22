<?php

/**
 * frontend @core
 * FEED SMARTY - ASSIGN CONTENTS FROM THE CURRENT PAGE
 */

if($p == "") {
	$smarty->assign('homelink_status', "$fc_defs[main_nav_class_active]");
} else {
	$smarty->assign('homelink_status', "$fc_defs[main_nav_class]");
}


$smarty->assign('body_template', $fc_template_layout);


$textlib_footer = get_textlib("footer_text");
$textlib_global_extracontent = get_textlib("extra_content_text");

$textlibs = get_all_textlibs();

$cnt_textlibs = count($textlibs);

for($i=0;$i<$cnt_textlibs;$i++) {
	$textlib_key = "fc_snippet_" . str_replace("-","_",$textlibs[$i][textlib_name]);
	$smarty->assign("$textlib_key", stripslashes($textlibs[$i][textlib_content]));
}



/**
 * fill vars with data
 * example: $page_linkname = stripslashes($page_contents[page_linkname]);
 */

if(is_array($page_contents)) {

	foreach($page_contents as $k => $v) {
  	$$k = stripslashes($v);
   	
   	/* if we have custom fields, assign the values to smarty */
   	if(preg_match("/custom_/i", $k)) {
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






/**
 * include modul
 */

if($page_modul != "") {
	
	include("modules/$page_modul/index.php");
	$page_content = "$page_content $modul_content";
	$smarty->assign('modul_head_enhanced', $modul_head_enhanced);

}



/**
 * function text_parser | @func_basics.php
 */
 
$page_content = text_parser($page_content);

/** 
 * custom theme functions
 */

if(is_file("styles/$fc_template/php/index.php")) {
	include("styles/$fc_template/php/index.php");
}


$smarty->assign('page_content', "$page_content");
$smarty->assign('page_title', "$page_title");
$smarty->assign('prefs_pagesglobalhead', "$prefs_pagesglobalhead");
$smarty->assign('page_meta_author', $page_meta_author);
$smarty->assign('page_meta_date', date('Y-m-d', $page_lastedit));
$smarty->assign('page_meta_keywords', $page_meta_keywords);
$smarty->assign('page_meta_description', $page_meta_description);


if($page_meta_robots == "") {
	$page_meta_robots = "all";
}

$smarty->assign('page_meta_robots', $page_meta_robots);
$smarty->assign('page_meta_enhanced', $page_meta_enhanced);

if($page_head_styles != "") {
	$smarty->assign('page_head_styles', "<style type=\"text/css\"> $page_head_styles </style>\n");
}

$smarty->assign('page_head_enhanced', $page_head_enhanced);
$smarty->assign("legend_searchbox","$lang[legend_searchbox]");
$smarty->assign("lang_button_search","$lang[button_search]");

$textlib_footer = text_parser($textlib_footer);
$smarty->assign("textlib_footer","$textlib_footer");

/* show extra content if != empty */
if($page_extracontent != "") {
	$page_extracontent = text_parser($page_extracontent);
	$smarty->assign('page_extracontent', stripslashes($page_extracontent));
	$output = $smarty->fetch("extracontent.tpl");
	$smarty->assign('extra_content', $output);
}

/* show global extra content if != empty */
if($textlib_global_extracontent != "") {
	$textlib_global_extracontent = text_parser($textlib_global_extracontent);
	$smarty->assign('page_global_extracontent', $textlib_global_extracontent);
	$output = $smarty->fetch("extracontent_global.tpl");
	$smarty->assign('extra_global_content', $output);
}








/**
 * tag cloud | most clicked | last edit
 */


	
/* last edit */
$le_cache_file = FC_CONTENT_DIR . "/cache/cache_lastedit.php";
if(is_file("$le_cache_file")) {
	include("$le_cache_file");
} else {
	$arr_lastedit = get_lastedit();
}
$smarty->assign('legend_lastedit', $lang[legend_lastedit]);
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

$smarty->assign('legend_mostclicked', $lang[legend_mostclicked]);
$smarty->assign('arr_mostclicked', $arr_mostclicked);

/* tags */
$tc_cache_file = FC_CONTENT_DIR . "/cache/cache_keywords.html";
if(is_file("$tc_cache_file")) {
	$page_keywords = file_get_contents($tc_cache_file);
} else {
	$page_keywords = get_keywords();
}

$smarty->assign('legend_tags', $lang[legend_tags]);
$smarty->assign('page_keywords', $page_keywords);





/**
 * private pages, for admins only
 */

if(($page_status == "private") AND ($_SESSION[user_class] != "administrator")) {
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

	if((!in_array("true",$is_user_in_group)) AND ($_SESSION[user_class] != "administrator")) {
		$text = get_textlib("no_access");
		$smarty->assign('page_content', $text);
		$smarty->assign('extra_content', "");
	}

}





/**
 * draft pages for administrators only
 */

if(($page_status == "draft") AND ($_SESSION[user_class] != "administrator")){
	$text = get_textlib("no_access");
	$smarty->assign('page_content', $text);
	$smarty->assign('extra_content', "");
}








?>