<?php
/**
 * flatCore - Free, Open Source, Content Management System
 * GNU General Public License (license.txt)
 *
 * https://www.flatcore.org
 * support@flatcore.org
 */

ini_set("url_rewriter.tags", '');
session_start();
error_reporting(0);

$fc_start_time = microtime(true);
require 'config.php';

define("FC_SOURCE", "frontend");

if(empty($_SESSION['visitor_csrf_token'])) {
	$_SESSION['visitor_csrf_token'] = md5(uniqid(rand(), TRUE));
}

if(!is_file('config_database.php') && !is_file("$fc_db_content")) {
	header("location: /install/");
	die();
}

require FC_CORE_DIR.'/database.php';

if(is_file(FC_CORE_DIR . "/maintance.html")) {
	header("location:" . FC_INC_DIR . "/maintance.html");
	die("We'll be back soon.");
}

$fc_prefs = fc_get_preferences();
$languagePack = $fc_prefs['prefs_default_language'];
$_SESSION['fc_admin_helpers'] = array();

/* all requests -> strip_tags */
foreach($_REQUEST as $key => $val) {
	$$key = strip_tags($val); 
}

/* reserved $_GET['p'] parameters */
$a_allowed_p = array('register', 'account', 'profile', 'search', 'sitemap', 'logout', 'password','display_post');

/*
 * mod_rewrite
 * $query defined by the .htaccess file
 * RewriteRule ^(.*)$ index.php?query=$1 [L,QSA]
 *
 */
 
if(!isset($query)) {
	$query = '/';
}
	
if(is_file(FC_CONTENT_DIR.'/plugins/query.controller.php')) {
	include FC_CONTENT_DIR.'/plugins/query.controller.php';
}

$fct_slug = $query;

$active_mods = fc_get_active_mods();
$cnt_active_mods = count($active_mods);

include FC_CONTENT_DIR . '/cache/active_urls.php';
if(in_array("$query", $existing_url)) {
	$query_is_cached = true;
}

for($i=0;$i<$cnt_active_mods;$i++) {
	
	$mod_permalink = $active_mods[$i]['page_permalink'];
	$mod_name = $active_mods[$i]['page_modul'];
	$permalink_length = strlen($mod_permalink);
	
	if(!empty($mod_permalink) && strpos("$query", "$mod_permalink") !== false) {
				
		if(strncmp($mod_permalink, $query, $permalink_length) == 0) {
			$mod_slug = substr($query, $permalink_length);
			$fct_slug = substr("$query",0,$permalink_length);
			if($query_is_cached == true) {
  			$fct_slug = $query;
			}
		}
	}	
}


if($query == '/') {
	list($page_contents,$fc_nav) = fc_get_content('portal','page_sort');
} else {
	list($page_contents,$fc_nav) = fc_get_content($fct_slug,'permalink');
}

foreach($active_mods as $mods) {
	if(is_file('modules/'.$mods['page_modul'].'/global/index.php')) {
		include 'modules/'.$mods['page_modul'].'/global/index.php';
	}
}

$p = $page_contents['page_id'];

if($p == "") {
	$p = "404";					
	foreach($a_allowed_p as $param) {
		if($query == "$param/") {
			$p = "$param";
		}
	}
	
	fc_check_funnel_uri($fct_slug);
	fc_check_shortlinks($fct_slug);
}

if(isset($preview) AND ($_SESSION['user_class'] == "administrator")) {
	$p = (int) $preview;
	list($page_contents,$fc_nav) = fc_get_content($p,'preview');
	unset($prefs_logfile);
}


if(isset($p) && preg_match('/[^0-9A-Za-z]/', $p)) {
	die('void id');
}


if(!is_array($page_contents) AND ($p != "")) {
	list($page_contents,$fc_nav) = fc_get_content($p);
}


/* no page contents -> switch to the homepage */
if($p == "" OR $p == "portal") {
	list($page_contents,$fc_nav) = fc_get_content('portal','page_sort');
}

/**
 * 404 page
 * if there is a page with type_of_use == 404, get the data
 * if not, we use the 404.tpl file
 */
if($p == "404") {
	list($page_contents,$fc_nav) = fc_get_content('404','type_of_use');
}


if($page_contents['page_type_of_use'] == 'register') {
	$p = 'register';
}


/* build absolute URL */
if($fc_prefs['prefs_cms_ssl_domain'] != '') {
	$fc_base_url = $fc_prefs['prefs_cms_ssl_domain'] . $fc_prefs['prefs_cms_base'];
} else {
	$fc_base_url = $fc_prefs['prefs_cms_domain'] . $fc_prefs['prefs_cms_base'];
}


/**
 * if $fct_slug is in prefs_deleted_resources
 * show HTTP Status Code 410 and die
 */

$deleted_resources = explode(PHP_EOL, $fc_prefs['prefs_deleted_resources']);
if(in_array("/$fct_slug", $deleted_resources)) {
	header("HTTP/1.0 410 Gone");
	header("Status: 410 Gone");
	die('HTTP/1.0 410 Gone');
}

/* if is set page_redirect, we can stop here and go straight to the desired location */
if($page_contents['page_redirect'] != '') {
	include_once('core/tracker.php');
	$redirect = $page_contents['page_redirect'];
	$redirect_code = (int) $page_contents['page_redirect_code'];
	header("location: $redirect",TRUE,$redirect_code);
	exit;
}


/* default $languagePack is defined in config.php */
if(is_dir("lib/lang/$page_contents[page_language]") AND ($page_contents['page_language'] != '')) {
	$languagePack = $page_contents['page_language'];
}

/* include language */
require(FC_CORE_DIR . "/lib/lang/index.php");

/* preferences (data from fc_get_preferences() ) */
foreach($fc_prefs as $key => $val) {
	$$key = stripslashes($val); 
}


if($prefs_dateformat == '') {
	$prefs_dateformat = 'Y-m-d';
}

if($prefs_timeformat == '') {
	$prefs_timeformat = 'H:i:s';
}


if(!empty($page_contents['page_modul'])) {
	include 'modules/'.$page_contents['page_modul'].'/index.php';
}

/* START SMARTY */
require_once('lib/Smarty/Smarty.class.php');
$smarty = new Smarty;
$smarty->compile_dir = 'content/cache/templates_c/';
$smarty->cache_dir = 'content/cache/cache/';
$cache_id = md5($fct_slug.$mod_slug);

if($prefs_smarty_cache == 1) {
	$smarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
	if(is_numeric($prefs_smarty_cache_lifetime)) {
		$smarty->setCacheLifetime($prefs_smarty_cache_lifetime);
	}
} else {
	$smarty->setCaching(Smarty::CACHING_OFF);
}

if($prefs_smarty_compile_check == 1) {
	$smarty->compile_check = true;
} else {
	$smarty->compile_check = false;
}


/* reset of the user-defined theme */
if(isset($_POST['reset_theme'])) {
	unset($_SESSION['prefs_template'],$_SESSION['prefs_template_stylesheet']);
}

/**
 * $prefs_usertemplate - off|on|overwrite
 * this option is intended for theme developers
 */

if($prefs_usertemplate == 'on' OR $prefs_usertemplate == 'overwrite') {
	
	/* set the theme - defined by the user */
	if(isset($_POST['set_theme'])) {
		$set_theme = 'styles/'.sanitizeUserInputs($_POST['set_theme']);
		if(is_dir($set_theme)) {
			$_SESSION['prefs_template'] = sanitizeUserInputs($_POST['set_theme']);
		}
	}
	
	/**
	 * set the theme and stylesheet - defined by the user
	 * example: $_POST['set_theme_stylesheet'] = './styles/default/css/dark.css';
	 */
	 
	if(isset($_POST['set_theme_stylesheet'])) {
		$set_theme_stylesheet = explode("/",$_POST['set_theme_stylesheet']);
		
		$set_theme_folder = $set_theme_stylesheet[2];
		$set_stylesheet = $set_theme_stylesheet[4];
		
		if(is_dir("./styles/$set_theme_folder")) {
			$_SESSION['prefs_template'] = sanitizeUserInputs($set_theme_folder);
		}
		
		if(is_file("./styles/$set_theme_folder/css/$set_stylesheet")) {
			$_SESSION['prefs_template_stylesheet'] = sanitizeUserInputs($set_stylesheet);
		}
	}
	
	
	if($_SESSION['prefs_template'] != '') {
		$prefs_template = $_SESSION['prefs_template'];
	}
	
	if($_SESSION['prefs_template_stylesheet'] != '') {
		$prefs_template_stylesheet = $_SESSION['prefs_template_stylesheet'];
	}

}

// default template
$fc_template = $prefs_template;
$fc_template_layout = $prefs_template_layout;
$fc_template_stylesheet = $prefs_template_stylesheet;

if($page_contents['page_template'] == "use_standard") {
	$fc_template = $prefs_template;
}

if($page_contents['page_template_layout'] == "use_standard") {
	$fc_template_layout = $prefs_template_layout;
}

/* page has its own theme/template */
if(is_dir('styles/'.$page_contents['page_template'].'/templates/')) {
	$fc_template = $page_contents['page_template'];
	$fc_template_layout = $page_contents['page_template_layout'];
	$fc_template_stylesheet = $page_contents['page_template_stylesheet'];
	
	if($prefs_usertemplate == 'overwrite') {
		/* the user theme has the same tpl file, so we can overwrite */
		if(is_file('./styles/'.$_SESSION['prefs_template'].'/templates/'.$page_contents['page_template_layout'])) {
			$fc_template = $prefs_template;
			$fc_template_layout = $page_contents['page_template_layout'];
			$fc_template_stylesheet = $prefs_template_stylesheet;
		}
	}
	
}




$smarty->assign('fc_template', $fc_template);
$smarty->assign('fc_template_layout', $fc_template_layout);

if($fc_template_stylesheet != '') {
	$smarty->assign('fc_template_stylesheet', basename($fc_template_stylesheet));
}

include 'core/definitions.php';

/* custom theme definitions and functions */
if(is_file('styles/'.$fc_template.'/php/definitions.php')) {
	include 'styles/'.$fc_template.'/php/definitions.php';
}

if(is_file("styles/$fc_template/php/index.php")) {
	include 'styles/'.$fc_template.'/php/index.php';
}

$smarty->template_dir = 'styles/'.$fc_template.'/templates/';


foreach($lang as $key => $val) {
	$smarty->assign("lang_$key", $val);
}

foreach($fc_prefs as $key => $val) {
	$smarty->assign("$key", $val);
}


if(!empty($page_contents['page_posts_categories'])) {
	include 'core/posts.php';
}

if($page_contents['page_type_of_use'] == 'display_post') {
	include 'core/posts.php';
}

$smarty->assign('nav_categories', $tpl_nav_cats);

$tyo_search = fc_get_type_of_use_pages('search');
$smarty->assign("search_uri", '/'.$tyo_search['page_permalink']);

/* legal pages */
$legal_pages = fc_get_legal_pages();
$cnt_legal_pages = count($legal_pages);
if($cnt_legal_pages > 0) {
	$smarty->assign('legal_pages', $legal_pages);
}



$smarty->assign('languagePack', $languagePack);
$smarty->assign("page_id", $page_contents['page_id']);

require 'core/user_management.php';
require 'core/switch.php';

if(is_file('styles/'.$fc_template.'/php/options.php')) {
	include 'styles/'.$fc_template.'/php/options.php';
}

// parse template vars
$smarty->assign('prefs_pagename', $prefs_pagename);
$smarty->assign('prefs_pagetitle', $prefs_pagetitle);
$smarty->assign('prefs_pagesubtitle', $prefs_pagesubtitle);
$smarty->assign('prefs_pagedescription', $prefs_pagedescription);
$smarty->assign("p","$p");
$smarty->assign("fc_inc_dir", FC_INC_DIR);

$fc_page_url = $fc_base_url;
if($fct_slug != '' AND $fct_slug != '/') {
	$fc_page_url .= $fct_slug;
}
if($mod_slug != '') {
	$fc_page_url .= $mod_slug;
}

$smarty->assign('fc_page_url', $fc_page_url,true);

$fc_end_time = microtime(true);
$fc_pageload_time = round($fc_end_time-$fc_start_time,4);
$smarty->assign('fc_start_time', $fc_start_time,true);
$smarty->assign('fc_end_time', $fc_end_time,true);
$smarty->assign('fc_pageload_time', $fc_pageload_time,true);

$smarty->assign('prepend_head_code', $prepend_head_code);
$smarty->assign('append_head_code', $append_head_code);
$smarty->assign('prepend_body_code', $prepend_body_code);
$smarty->assign('append_body_code', $append_body_code);


if($_SESSION['user_class'] == "administrator") {
	$store = $_SESSION['fc_admin_helpers'];

	if(isset($store['snippet'])) {
		$smarty->assign('admin_helpers_snippets', $store['snippet']);
	}
	if(isset($store['plugin'])) {
		$store['plugin'] = array_unique($store['plugin']);
		$smarty->assign('admin_helpers_plugins', $store['plugin']);
	}
	if(isset($store['shortcodes'])) {
		$store['shortcodes'] = array_unique($store['shortcodes']);
		$smarty->assign('admin_helpers_shortcodes', $store['shortcodes']);
	}
	if(isset($store['images'])) {
		$store['images'] = array_unique($store['images']);
		$smarty->assign('admin_helpers_images', $store['images']);
	}
	if(isset($store['files'])) {
		$store['files'] = array_unique($store['files']);
		$smarty->assign('admin_helpers_files', $store['files']);
		
	}
}


// display the template
$smarty->display('index.tpl',$cache_id);

if(($p == "clearallcache") AND ($_SESSION['user_class'] == "administrator")) {
	$smarty->clearAllCache();
}


/* track the hits */
if(!isset($preview)) {
	include_once 'core/tracker.php';
}

/* track more statistics */
if($prefs_logfile == "on") {
	include_once 'core/logfile.php';
}


?>

