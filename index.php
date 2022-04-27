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
header("X-Frame-Options: SAMEORIGIN");

$fc_start_time = microtime(true);
require 'config.php';

define("FC_SOURCE", "frontend");

if(empty($_SESSION['visitor_csrf_token'])) {
	$_SESSION['visitor_csrf_token'] = md5(uniqid(rand(), TRUE));
}

/**
 * if there is no database config we start the installer
 * @var string $fc_db_content SQLite file from config.php or /content/config.php
 * @var string $database_host is set in config_database
 */

if(!is_file('config_database.php') && !is_file("$fc_db_content")) {
	header("location: /install/");
	die();
}

/**
 * connect the database
 * @var string $db_content
 * @var string $db_user
 * @var string $db_statistics
 * @var string $db_posts
 */

require FC_CORE_DIR.'/database.php';


/**
 * maintenance mode
 */

if(is_file(FC_CORE_DIR . "/maintenance.html")) {
	header("location:" . FC_INC_DIR . "/maintenance.html");
	die("We'll be back soon.");
}

/**
 * get the preferences
 * the most important for the frontend are
 * default information and/or metas
 * $fc_prefs['prefs_pagename'] $fc_prefs['prefs_pagetitle'] $fc_prefs['prefs_pagesubtitle'] $fc_prefs['prefs_pagedescription'] $fc_prefs['prefs_pagefavicon']
 * language
 * $fc_prefs['prefs_default_language']
 * user management
 * $fc_prefs['prefs_userregistration'] $fc_prefs['prefs_showloginform']
 * templates
 * $fc_prefs['prefs_template'] $fc_prefs['prefs_template_layout'] $fc_prefs['prefs_template_stylesheet']
 */

$fc_get_preferences = fc_get_preferences();

foreach($fc_get_preferences as $k => $v) {
	$key = $fc_get_preferences[$k]['option_key'];
	$value = $fc_get_preferences[$k]['option_value'];
	$fc_prefs[$key] = $value;
}
 
foreach($fc_prefs as $key => $val) {
	$$key = stripslashes($val); 
}


if($fc_prefs['prefs_dateformat'] == '') {
	$prefs_dateformat = 'Y-m-d';
}

if($fc_prefs['prefs_timeformat'] == '') {
	$prefs_timeformat = 'H:i:s';
}





$lang_dir = $fc_prefs['prefs_default_language'];

include 'lib/lang/'.$lang_dir.'/index.php';
$languagePack = $lang_sign;

if($_SESSION['user_class'] == "administrator") {
	$_SESSION['fc_admin_helpers'] = array();
}

/**
 * reserved $_GET['p'] parameters
 */
$a_allowed_p = array(
    'register',
    'account',
    'profile',
    'search',
    'sitemap',
    'logout',
    'password',
    'display_post',
    'display_product',
    'display_event',
    'checkout',
    'orders'
);

/*
 * mod_rewrite
 * $query defined by the .htaccess file
 * RewriteRule ^(.*)$ index.php?query=$1 [L,QSA]
 *
 */

$query = fc_clean_query($_GET['query']);

if(!isset($query)) {
	$query = '/';
}
	
if(is_file(FC_CONTENT_DIR.'/plugins/query.controller.php')) {
	include FC_CONTENT_DIR.'/plugins/query.controller.php';
}

if($query == 'logout' OR $_GET['goto'] == 'logout') {
	$user_logout = fc_end_user_session();
	$query = '/';
}

$fct_slug = $query;

$active_mods = fc_get_active_mods();
$cnt_active_mods = count($active_mods);

/**
 * get existing url from cache file
 * @var array $existing_url
 */
include FC_CONTENT_DIR . '/cache/active_urls.php';
if(in_array("$query", $existing_url)) {
	$query_is_cached = true;
}

/**
 * loop through installed modules
 */

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


if($fct_slug == '/' OR $fct_slug == '') {
	list($page_contents,$fc_nav) = fc_get_content('portal','page_sort');
} else {
	list($page_contents,$fc_nav) = fc_get_content($fct_slug,'permalink');
}

/* include modul index.php if exists */

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

/**
 * show preview
 */

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

if($page_contents['page_type_of_use'] == 'checkout') {
	$p = 'checkout';
}

if($page_contents['page_type_of_use'] == 'orders') {
	$p = 'orders';
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
	include_once 'core/tracker.php';
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




if(!empty($page_contents['page_modul'])) {
	include 'modules/'.$page_contents['page_modul'].'/index.php';
}

/* START SMARTY */
require_once('lib/Smarty/Smarty.class.php');
$smarty = new Smarty;
$smarty->compile_dir = 'content/cache/templates_c/';
$smarty->cache_dir = 'content/cache/cache/';
$cache_id = md5($fct_slug.$mod_slug);

if($fc_prefs['prefs_smarty_cache'] == 1) {
	$smarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
	if(is_numeric($fc_prefs['prefs_smarty_cache_lifetime'])) {
		$smarty->setCacheLifetime($fc_prefs['prefs_smarty_cache_lifetime']);
	}
} else {
	$smarty->setCaching(Smarty::CACHING_OFF);
}

if($fc_prefs['prefs_smarty_compile_check'] == 1) {
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

if($fc_prefs['prefs_usertemplate'] == 'on' OR $fc_prefs['prefs_usertemplate'] == 'overwrite') {
	
	/* set the theme - defined by the user */
	if(isset($_POST['set_theme'])) {
		$set_theme = 'styles/'.sanitizeUserInputs($_POST['set_theme']);
		if(is_dir($set_theme)) {
			$_SESSION['prefs_template'] = sanitizeUserInputs($_POST['set_theme']);
			unset($_SESSION['prefs_template_stylesheet']);
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

/**
 * assign all translations to smarty
 * @var array $lang
 */
foreach($lang as $key => $val) {
	$smarty->assign("lang_$key", $val);
}

foreach($fc_prefs as $key => $val) {
	$smarty->assign("$key", $val);
}


if($page_contents['page_posts_types'] != '' OR $page_contents['page_type_of_use'] == 'display_post' OR $page_contents['page_type_of_use'] == 'display_product') {
    if($page_contents['page_posts_types'] == 'p' OR $page_contents['page_type_of_use'] == 'display_product') {
        $p = 'list-products';
    } else if($page_contents['page_type_of_use'] == 'display_post') {
        include 'core/posts.php';
    } else {
        include 'core/posts.php';
    }
}

/**
 * categories for the blog
 * @var array $tpl_nav_cats
 */
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

if($user_logout != '') {
	$smarty->assign("msg_status","alert alert-success",true);
	$smarty->assign('msg_text', $lang['msg_logout'],true);
	$output = $smarty->fetch("status_message.tpl");
	$smarty->assign('msg_content', $output);
}

/* get permalink for orders page */
$orders_page = fc_get_type_of_use_pages('orders');
if($orders_page['page_permalink'] == '') {
	$orders_uri = '/orders/';
} else {
	$orders_uri = '/'.$orders_page['page_permalink'];
}

$smarty->assign('orders_uri', $orders_uri);


if($fc_prefs['prefs_posts_products_cart'] == 2 OR $fc_prefs['prefs_posts_products_cart'] == 3) {
	/* add product to the shopping cart */
	if(isset($_POST['add_to_cart'])) {
		$fc_cart = fc_add_to_cart();
	}
	
	/* get permalink for shopping cart */
	$checkout_page = fc_get_type_of_use_pages('checkout');
	if($checkout_page['page_permalink'] == '') {
		$sc_uri = '/checkout/';
	} else {
		$sc_uri = '/'.$checkout_page['page_permalink'];
	}
	
	$smarty->assign('shopping_cart_uri', $sc_uri);
	
	/* amount of items in the shopping cart */
	$cnt_items = fc_return_cart_amount();
	if($cnt_items > 0) {
		$smarty->assign('cnt_shopping_cart_items', $cnt_items);
	}
}


require 'core/user_management.php';
require 'core/switch.php';

if(is_file('styles/'.$fc_template.'/php/options.php')) {
	include 'styles/'.$fc_template.'/php/options.php';
}

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

$store = '';
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


/* track the hits */
if(!isset($preview)) {
	include_once 'core/tracker.php';
}

/* track more statistics */
if($fc_prefs['prefs_logfile'] == "on") {
	include_once 'core/logfile.php';
}


?>