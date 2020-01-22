<?php
/**
 * flatCore - Free, Open Source, Content Management System
 * GNU General Public License (license.txt)
 *
 * copyright 2010-2018, Patrick Konstandin
 * Information and contribution at https://www.flatcore.org
 * E-Mail: support@flatcore.org
 */

ini_set("url_rewriter.tags", '');
session_start();
error_reporting(0);

$fc_start_time = microtime(true);

define("FC_SOURCE", "frontend");

/* all requests -> strip_tags */
foreach($_REQUEST as $key => $val) {
	$$key = strip_tags($val); 
}


require 'config.php';

if(is_file(FC_CORE_DIR . "/maintance.html") OR (is_file($fc_db_content) == false)) {
	header("location:" . FC_INC_DIR . "/maintance.html");
	die("We'll be back soon.");
}



require FC_CORE_DIR . '/core/functions.php';


/* reserved $_GET['p'] parameters */
$a_allowed_p = array('register', 'account', 'profile', 'search', 'sitemap', 'logout', 'password');

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
	list($page_contents,$fc_nav,$fc_prefs) = get_content('portal','page_sort');
} else {
	list($page_contents,$fc_nav,$fc_prefs) = get_content($fct_slug,'permalink');
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


if(isset($preview)) {
	$p = (int) $preview;
	list($page_contents,$fc_nav,$fc_prefs) = get_content($p,'preview');
	unset($prefs_logfile);
}


if(isset($p) && preg_match('/[^0-9A-Za-z]/', $p)) {
	die('void id');
}


if(!is_array($page_contents) AND ($p != "")) {
	list($page_contents,$fc_nav,$fc_prefs) = get_content($p);
}


/* no page contents -> switch to the homepage */
if($p == "" OR $p == "portal") {
	list($page_contents,$fc_nav,$fc_prefs) = get_content('portal','page_sort');
}

/**
 * 404 page
 * if there is a page with permalink == 404, get the data
 * if not, we use the 404.tpl file
 */
if($p == "404") {
	list($page_contents,$fc_nav,$fc_prefs) = get_content('404','permalink');
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

/* preferences (data from get_content() ) */
foreach($fc_prefs as $key => $val) {
	$$key = stripslashes($val); 
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


// default template
$fc_template = $prefs_template;
$fc_template_layout = $prefs_template_layout;

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
}

/* module has its own theme/template */
//echo $page_contents['page_modul'];

$smarty->assign('fc_template', $fc_template);
$smarty->assign('fc_template_layout', $fc_template_layout);

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

if($languagePack == 'de') {
	$smarty->assign("search_uri", '/suche/');
} else {
	$smarty->assign("search_uri", '/search/');
}

$smarty->assign('languagePack', $languagePack);

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

$fc_end_time = microtime(true);
$fc_pageload_time = round($fc_end_time-$fc_start_time,4);
$smarty->assign('fc_start_time', $fc_start_time,true);
$smarty->assign('fc_end_time', $fc_end_time,true);
$smarty->assign('fc_pageload_time', $fc_pageload_time,true);

$smarty->assign('prepend_head_code', $prepend_head_code);
$smarty->assign('append_head_code', $append_head_code);
$smarty->assign('prepend_body_code', $prepend_body_code);
$smarty->assign('append_body_code', $append_body_code);

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

