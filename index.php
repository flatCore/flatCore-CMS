<?php
/**
 * flatCore - Free, Open Source, Content Management System
 * GNU General Public License (license.txt)
 *
 * copyright 2010-2013, Patrick Konstandin
 * Information and contribution at http://www.flatcore.de
 * E-Mail: support@flatfiler.de
 */
 
ini_set("url_rewriter.tags", '');
session_start();
error_reporting(0);


/* all requests -> strip_tags */
foreach($_REQUEST as $key => $val) {
	$$key = strip_tags($val); 
}


require('config.php');


if(is_dir(FC_CORE_DIR . "/install/")) {
		header("location:" . FC_INC_DIR . "/maintance.html");
		die("We'll be back soon.");
}


require(FC_CORE_DIR . "/lib/lang/$languagePack/frontend/dict.php");
require(FC_CORE_DIR . '/core/get_preferences.php');
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

	$fct_slug = $query;

	if($fc_mod_rewrite == "permalink") {
	
		include(FC_CONTENT_DIR . "/cache/active_mods.php");
		$cnt_active_mods = count($active_mods);
		
		include(FC_CONTENT_DIR . "/cache/active_urls.php");
		if(in_array("$query", $existing_url)) {
			$query_is_cached = true;
		}
		
		for($i=0;$i<$cnt_active_mods;$i++) {
			
			$mod_permalink = $active_mods[$i][page_permalink];
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
			
		}
	

		$page_contents = get_content_by_permalink($fct_slug);
		$p = $page_contents['page_id'];
		
		
		if($p == "") {		
			$p = "404";					
			foreach($a_allowed_p as $param) {
				if($query == "$param/") {
					$p = "$param";
				}
			}
		}
			
	}
		
} // eo $query



if($preview != "") {
	$p = (int) $preview;
	$page_contents = get_content_for_preview($p);
	unset($pref_logfile);
}
    


if(preg_match('/[^0-9A-Za-z]/', $p)) {
	die('void id');
}


if(!is_array($page_contents) AND ($p != "")) {
	$page_contents = get_content($p);
}


/* no page contents -> switch to the homepage */
if($p == "" OR $p == "portal") {
	$page_contents = get_content_by_pagesort('portal');
}


/* START SMARTY */
require_once('lib/Smarty/Smarty.class.php');

$smarty = new Smarty;
$smarty->caching = false;
$smarty->compile_check = true;
//$smarty->debugging = true;

// default template
$fc_template = $pref_template;
$fc_template_layout = $pref_template_layout;

if($page_contents[page_template] == "use_standard") {
	$fc_template = $pref_template;
}

if($page_contents[page_template_layout] == "use_standard") {
	$fc_template_layout = $pref_template_layout;
}

if(is_dir('styles/'.$page_contents[page_template].'/templates/')) {
	$fc_template = $page_contents[page_template];
	$fc_template_layout = $page_contents[page_template_layout];
}


include('core/definitions.php');
if(is_file('styles/'.$fc_template.'/php/definitions.php')) {
	include('styles/'.$fc_template.'/php/definitions.php');
}

$smarty->template_dir = 'styles/'.$fc_template.'/templates/';
$smarty->compile_dir = 'content/cache/templates_c/';
$smarty->cache_dir = 'content/cache/cache/';


foreach($lang as $key => $val) {
	$smarty->assign("lang_$key", $val);
}

require("core/user_management.php");
require("core/switch.php");


// parse template vars
$smarty->assign('pref_pagetitle', $pref_pagetitle);
$smarty->assign('pref_pagesubtitle', $pref_pagesubtitle);
$smarty->assign("p","$p");
$smarty->assign("fc_inc_dir", FC_INC_DIR);


// display the template
$smarty->display('index.tpl');


/* track the hits */
if(!isset($preview)) {
	include_once('core/tracker.php');
}

/* track more statistics */
if($pref_logfile == "on") {
	include_once('core/logfile.php');
}

?>

