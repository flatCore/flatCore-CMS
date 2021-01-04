<?php

/**
 * install flatCore
 * create the sqlite database files
 */

if(!defined('INSTALLER')) {
	header("location:../login.php");
	die("PERMISSION DENIED!");
}

require '../lib/Medoo.php';
use Medoo\Medoo;

$username = $_SESSION['temp_username'];
$mail = $_SESSION['temp_usermail'];
$psw = $_SESSION['temp_userpsw'];

$user_psw_hash = password_hash($psw, PASSWORD_DEFAULT);
$drm_string = "drm_acp_pages|drm_acp_files|drm_acp_user|drm_acp_system|drm_acp_editpages|drm_acp_editownpages|drm_moderator|drm_can_publish";
$user_verified = "verified";
$user_registerdate = time();

$prefs_cms_domain = $_SESSION['temp_prefs_cms_domain'];
$prefs_cms_ssl_domain = $_SESSION['temp_prefs_cms_ssl_domain'];
$prefs_cms_base = $_SESSION['temp_prefs_cms_base'];



if(isset($_POST['install_mysql'])) {
	/* we use MySQL */
	$db_type = 'mysql';
	
	if ($prefs_database_name == '' || $prefs_database_username == '' || $prefs_database_psw == '') {
		echo '<p><a href="javascript:history.back()" class="btn btn-default">'.$lang['pagination_backward'].'</a></p>';
		die('MISSING MYSQL INFORMATION');		
	}
	
	
	
	try {
		$database = new Medoo([
	
			'database_type' => 'mysql',
			'database_name' => "$prefs_database_name",
			'server' => "$prefs_database_host",
			'username' => "$prefs_database_username",
			'password' => "$prefs_database_psw",
		 
			'charset' => 'utf8',
			'port' => $prefs_database_port,
		 
			'prefix' => "$prefs_database_prefix"
		]);
	
	} catch (Exception $e) {
		echo '<p><a href="javascript:history.back()" class="btn btn-default">'.$lang['pagination_backward'].'</a></p>';
		die('CONNECTION ERROR');
	}  

	
  $config_db_content = "<?php\n";
  $config_db_content .= "$"."database_host = "."\"".$prefs_database_host."\";\n";
  $config_db_content .= "$"."database_user = "."\"".$prefs_database_username."\";\n";
  $config_db_content .= "$"."database_psw = "."\"".$prefs_database_psw."\";\n";
  $config_db_content .= "$"."database_name = "."\"".$prefs_database_name."\";\n";
  $config_db_content .= "$"."database_port = "."\"".$prefs_database_port."\";\n";
  $config_db_content .= "define("."\""."DB_PREFIX"."\"".", "."\"".$prefs_database_prefix."\");\n";
  $config_db_content .= "?>";
	
	$config_db_file = "../config_database.php";
	file_put_contents($config_db_file, $config_db_content);
	
	define("FC_PREFIX","$prefs_database_prefix");

	
} else {
	$db_type = 'sqlite';
	
	define("CONTENT_DB", "../$fc_db_content");
	define("USER_DB", "../$fc_db_user");
	define("STATS_DB", "../$fc_db_stats");
	define("POSTS_DB", "../$fc_db_posts");
	
	
	$db_content = new Medoo([
		'database_type' => 'sqlite',
		'database_file' => CONTENT_DB
	]);
	
	$db_user = new Medoo([
		'database_type' => 'sqlite',
		'database_file' => USER_DB
	]);
	
	$db_statistics = new Medoo([
		'database_type' => 'sqlite',
		'database_file' => STATS_DB
	]);
	
	$db_posts = new Medoo([
		'database_type' => 'sqlite',
		'database_file' => POSTS_DB
	]);

}

define("INDEX_DB", "../$fc_db_index");
$db_index = new Medoo([
	'database_type' => 'sqlite',
	'database_file' => INDEX_DB
]);


echo $db_type. ' Database<hr>';


/* Queries for new tables */

$sql_user_table = fc_generate_sql_query("fc_user.php",$db_type);
$sql_groups_table = fc_generate_sql_query("fc_groups.php",$db_type);
$sql_tokens_table = fc_generate_sql_query("fc_tokens.php",$db_type);

$sql_feeds_table = fc_generate_sql_query("fc_feeds.php",$db_type);
$sql_pages_table = fc_generate_sql_query("fc_pages.php",$db_type);
$sql_pages_cache_table = fc_generate_sql_query("fc_pages_cache.php",$db_type);
$sql_preferences_table = fc_generate_sql_query("fc_preferences.php",$db_type);
$sql_textlib_table = fc_generate_sql_query("fc_textlib.php",$db_type);
$sql_comments_table = fc_generate_sql_query("fc_comments.php",$db_type);
$sql_media_table = fc_generate_sql_query("fc_media.php",$db_type);
$sql_labels_table = fc_generate_sql_query("fc_labels.php",$db_type);
$sql_categories_table = fc_generate_sql_query("fc_categories.php",$db_type);
$sql_addons_table = fc_generate_sql_query("fc_addons.php",$db_type);

$sql_posts_table = fc_generate_sql_query("fc_posts.php",$db_type);

$sql_hits_table = fc_generate_sql_query("fc_hits.php",$db_type);
$sql_log_table = fc_generate_sql_query("fc_log.php",$db_type);

$sql_index_excludes_table = fc_generate_sql_query("fc_index_excludes.php",'sqlite');
$sql_index_items_table = fc_generate_sql_query("fc_index_items.php",'sqlite');


if($db_type == 'mysql') {
	
	$dbh_user = $database;
	$dbh_content = $database;
	$dbh_statistics = $database;
	$dbh_posts = $database;
	
} else {
	
	$dbh_user = $db_user;
	$dbh_content = $db_content;
	$dbh_statistics = $db_statistics;
	$dbh_posts = $db_posts;
	
}

$dbh_user->query($sql_user_table);
$dbh_user->query($sql_tokens_table);
$dbh_user->query($sql_groups_table);

$dbh_user->insert("fc_user", [
	"user_class" => "administrator",
	"user_nick" => "$username",
	"user_verified" => "verified",
	"user_registerdate" => "$user_registerdate",
	"user_drm" => "$drm_string",
	"user_mail" => "$mail",
	"user_psw_hash" => "$user_psw_hash"
]);



/**
 * get basic contents
 */


$portal_content = file_get_contents("contents/text_welcome.txt");
$example_content = file_get_contents("contents/text_example.txt");
$footer_content = file_get_contents("contents/text_footer.txt");
$agreement_content = file_get_contents("contents/text_agreement.txt");
$email_confirm_content = file_get_contents("contents/text_email_confirm.txt");
$page_lastedit = time();


$dbh_content->query($sql_pages_table);
$dbh_content->query($sql_pages_cache_table);
$dbh_content->query($sql_preferences_table);
$dbh_content->query($sql_textlib_table);
$dbh_content->query($sql_comments_table);
$dbh_content->query($sql_media_table);
$dbh_content->query($sql_feeds_table);
$dbh_content->query($sql_labels_table);
$dbh_content->query($sql_categories_table);
$dbh_content->query($sql_addons_table);

/* insert two example pages */

$dbh_content->insert("fc_pages", [
	"page_language" => "$languagePack",
	"page_linkname" => "Home",
	"page_title" => "Homepage",
	"page_status" => "public",
	"page_content" => "$portal_content",
	"page_lastedit" => "$page_lastedit",
	"page_lastedit_from" => "$username",
	"page_template" => "default",
	"page_template_layout" => "layout_portal.tpl",
	"page_sort" => "portal",
	"page_meta_author" => "$username",
	"page_meta_date" => "$page_lastedit",
	"page_meta_keywords" => "Lorem,ipsum,dolor,sit",
	"page_meta_description" => "Test Meta Description",
	"page_meta_robots" => "all"
]);

$dbh_content->insert("fc_pages", [
	"page_language" => "$languagePack",
	"page_linkname" => "Testseite",
	"page_permalink" => "test/",
	"page_title" => "Testseite",
	"page_status" => "public",
	"page_content" => "$example_content",
	"page_lastedit" => "$page_lastedit",
	"page_lastedit_from" => "$username",
	"page_template" => "default",
	"page_template_layout" => "layout_portal.tpl",
	"page_sort" => "100",
	"page_meta_author" => "$username",
	"page_meta_date" => "$page_lastedit",
	"page_meta_keywords" => "Lorem,ipsum,dolor,sit",
	"page_meta_description" => "Testseite Meta Description",
	"page_meta_robots" => "all"
]);

/* insert preferences */

$dbh_content->insert("fc_preferences", [
	"prefs_status" => "active",
	"prefs_pagename" => "flatCore",
	"prefs_pagetitle" => "flatCore CMS",
	"prefs_pagesubtitle" => "Content Management System",
	"prefs_template" => "default",
	"prefs_template_layout" => "layout_default.tpl",
	"prefs_showloginform" => "yes",
	"prefs_xml_sitemap" => "off",
	"prefs_logfile" => "off",
	"prefs_rss_time_offset" => 86400,
	"prefs_cms_domain" => "$prefs_cms_domain",
	"prefs_cms_ssl_domain" => "$prefs_cms_ssl_domain",
	"prefs_cms_base" => "$prefs_cms_base",
	"prefs_default_language" => "$languagePack",
	"prefs_nbr_page_versions" => 25,
	"prefs_acp_session_lifetime" => 86400,
	"prefs_posts_entries_per_page" => 10,
	"prefs_posts_event_time_offset" => 86400,
	"prefs_default_language" => "$l",
	"prefs_comments_mode" => 3,
	"prefs_comments_authorization" => 1,
	"prefs_comments_max_entries" => 100,
	"prefs_comments_max_level" => 3,
	"prefs_pagesort_minlength" => 3,
	"prefs_maximagewidth" => 1024,
	"prefs_maximageheight" => 1024,
	"prefs_maxfilesize" => 2500
]);



/* insert snippets */

$dbh_content->insert("fc_textlib", [
	[
		"textlib_name" => "footer_text",
		"textlib_content" => "$footer_content",
		"textlib_lang" => "$languagePack"
		
	],[
		
		"textlib_name" => "extra_content_text",
		"textlib_content" => "",
		"textlib_lang" => "$languagePack"
			
	],[
	
		"textlib_name" => "agreement_text",
		"textlib_content" => "$agreement_content",
		"textlib_lang" => "$languagePack"	
		
	],[
	
		"textlib_name" => "account_confirm",
		"textlib_content" => "<p>Dein Account wurde erfolgreich freigeschaltet.</p>",
		"textlib_lang" => "$languagePack"		
		
	],[
		
		"textlib_name" => "account_confirm_mail",
		"textlib_content" => "$email_confirm_content",
		"textlib_lang" => "$languagePack"	
		
	],[
		
		"textlib_name" => "no_access",
		"textlib_content" => "Zugriff verweigert...",
		"textlib_lang" => "$languagePack"	
		
	]
]);




/**
 * Logfiles and Klicks
 */


$dbh_statistics->query($sql_hits_table);
$dbh_statistics->query($sql_log_table);


/* posts table */

$dbh_posts->query($sql_posts_table);

/**
 * DATABASE INDEX
 */

$db_index->query($sql_index_excludes_table);
$db_index->query("SET NAMES 'utf-8'");
$db_index->query($sql_index_items_table);


echo '<div class="alert alert-success">'.$lang['installed'].' | Admin: '.$username.'</div>';
echo '<hr><a class="btn btn-success" href="../acp/index.php">'.$lang['link_admin'].'</a><hr>';




?>