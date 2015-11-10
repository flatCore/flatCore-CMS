<?php
/**
 * flatCore - Free, Open Source, Content Management System
 * GNU General Public License (license.txt)
 *
 * copyright 2010-2015, Patrick Konstandin
 * Information and contribution at http://www.flatcore.de
 * E-Mail: support@flatcore.de
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


require('config.php');

if(is_file(FC_CORE_DIR . "/maintance.html") OR (is_file($fc_db_content) == false)) {
		header("location:" . FC_INC_DIR . "/maintance.html");
		die("We'll be back soon.");
}



require(FC_CORE_DIR . '/core/functions.php');


/* reserved $_GET['p'] parameters */
$a_allowed_p = array('register', 'account', 'profile', 'search', 'sitemap', 'logout', 'password');


/*
 * mod_rewrite
 * $query defined by the .htaccess file
 * RewriteRule ^(.*)$ index.php?query=$1 [L,QSA]
 *
 */

if($query != "") {
	
	if(is_file(FC_CONTENT_DIR.'/plugins/query.controller.php')) {
		include(FC_CONTENT_DIR.'/plugins/query.controller.php');
	}

	$fct_slug = $query;

	if($fc_mod_rewrite == "permalink") {
	
		include(FC_CONTENT_DIR . "/cache/active_mods.php");
		$cnt_active_mods = count($active_mods);
		
		include(FC_CONTENT_DIR . "/cache/active_urls.php");
		if(in_array("$query", $existing_url)) {
			$query_is_cached = true;
		}
		
		for($i=0;$i<$cnt_active_mods;$i++) {
			
			$mod_permalink = $active_mods[$i]['page_permalink'];
			$mod_name = $active_mods[$i]['page_modul'];
			$permalink_length = strlen($mod_permalink);
			
			if(strpos("$query", "$mod_permalink") !== false) {
						
				if(strncmp($mod_permalink, $query, $permalink_length) == 0) {
    			$mod_slug = substr($query, $permalink_length);
    			$fct_slug = substr("$query",0,$permalink_length);
    			if($query_is_cached == true) {
      			$fct_slug = $query;
    			}
    		}
    	}
    	
			if(is_file('modules/'.$mod_name.'/global/index.php')) {
				include('modules/'.$mod_name.'/global/index.php');
  		}
			
		}

		list($page_contents,$fc_nav,$fc_prefs) = get_content($fct_slug,'permalink');
		$p = $page_contents['page_id'];
		
		
		if($p == "") {		
			$p = "404";					
			foreach($a_allowed_p as $param) {
				if($query == "$param/") {
					$p = "$param";
				}
			}
			
			fc_check_shortlinks($fct_slug);
			
		}
			
	}
} // eo $query


if($preview != "") {
	$p = (int) $preview;
	list($page_contents,$fc_nav,$fc_prefs) = get_content($p,'preview');
	unset($prefs_logfile);
}


if(preg_match('/[^0-9A-Za-z]/', $p)) {
	die('void id');
}


if(!is_array($page_contents) AND ($p != "")) {
	list($page_contents,$fc_nav,$fc_prefs) = get_content($p);
}


/* no page contents -> switch to the homepage */
if($p == "" OR $p == "portal") {
	list($page_contents,$fc_nav,$fc_prefs) = get_content('portal','page_sort');
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


/* START SMARTY */
require_once('lib/Smarty/Smarty.class.php');

$smarty = new Smarty;
$smarty->caching = false;
$smarty->compile_check = true;
//$smarty->debugging = true;

// default template
$fc_template = $prefs_template;
$fc_template_layout = $prefs_template_layout;

if($page_contents['page_template'] == "use_standard") {
	$fc_template = $prefs_template;
}

if($page_contents['page_template_layout'] == "use_standard") {
	$fc_template_layout = $prefs_template_layout;
}

if(is_dir('styles/'.$page_contents['page_template'].'/templates/')) {
	$fc_template = $page_contents['page_template'];
	$fc_template_layout = $page_contents['page_template_layout'];
}

$smarty->assign('fc_template', $fc_template);
$smarty->assign('fc_template_layout', $fc_template_layout);

include('core/definitions.php');

/* custom theme definitions and functions */
if(is_file('styles/'.$fc_template.'/php/definitions.php')) {
	include('styles/'.$fc_template.'/php/definitions.php');
}

if(is_file("styles/$fc_template/php/index.php")) {
	include("styles/$fc_template/php/index.php");
}

$smarty->template_dir = 'styles/'.$fc_template.'/templates/';
$smarty->compile_dir = 'content/cache/templates_c/';
$smarty->cache_dir = 'content/cache/cache/';


foreach($lang as $key => $val) {
	$smarty->assign("lang_$key", $val);
}

$smarty->assign('languagePack', $languagePack);

require("core/user_management.php");
require("core/switch.php");

if(is_file('styles/'.$fc_template.'/php/options.php')) {
	include('styles/'.$fc_template.'/php/options.php');
}

// parse template vars
$smarty->assign('prefs_pagetitle', $prefs_pagetitle);
$smarty->assign('prefs_pagesubtitle', $prefs_pagesubtitle);
$smarty->assign("p","$p");
$smarty->assign("fc_inc_dir", FC_INC_DIR);

$fc_end_time = microtime(true);
$fc_pageload_time = round($fc_end_time-$fc_start_time,4);
$smarty->assign('fc_start_time', $fc_start_time);
$smarty->assign('fc_end_time', $fc_end_time);
$smarty->assign('fc_pageload_time', $fc_pageload_time);

// display the template
$smarty->display('index.tpl');


/* track the hits */
if(!isset($preview)) {
	include_once('core/tracker.php');
}

/* track more statistics */
if($prefs_logfile == "on") {
	include_once('core/logfile.php');
}


?>

