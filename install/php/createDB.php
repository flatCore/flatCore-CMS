<?php

/**
 * install flatCore
 * create the sqlite database files
 */


if(!defined('INSTALLER')) {
	header("location:../login.php");
	die("PERMISSION DENIED!");
}

$username = $_POST['username'];
$mail = $_POST['mail'];
$psw = $_POST['psw'];

$user_psw_hash = password_hash($psw, PASSWORD_DEFAULT);
$drm_string = "drm_acp_pages|drm_acp_files|drm_acp_user|drm_acp_system|drm_acp_editpages|drm_acp_editownpages|drm_moderator|drm_can_publish";
$user_verified = "verified";
$user_registerdate = time();

/**
 * DATABASE USER
 */

if(!isset($db_host)) {
	$dbh = dbconnect('sqlite','../'.$fc_db_user);
	$db_type = 'sqlite';
	define("DB_PREFIX", "fc_");
} else {
	$dbh = dbconnect('mysql', $db_host, $db_user, $db_pass, $db_name);
	$db_type = 'mysql';
}

$sql_user_table = fc_generate_sql_query("fc_user.php","$db_type");
$sql_groups_table = fc_generate_sql_query("fc_groups.php","$db_type");





dbquery($sql_user_table);
dbquery($sql_groups_table);

$sql_insert_admin = "INSERT INTO ".DB_PREFIX."user (
		user_id, user_class, user_nick, user_verified, user_registerdate, user_drm, user_mail, user_psw_hash
	) VALUES (
		NULL, 'administrator', :username, 'verified', :user_registerdate, :drm_string, :mail, :user_psw_hash
	)";

dbquery($sql_insert_admin, array(':username' => $username,
								 ':user_registerdate' => $user_registerdate,
								 ':drm_string' => $drm_string,
								 ':mail' => $mail,
								 ':user_psw_hash' => $user_psw_hash,
	));



/**
 * DATABASE CONTENT
 */

$sql_feeds_table = fc_generate_sql_query("fc_feeds.php","$db_type");
$portal_content = file_get_contents("contents/text_welcome.txt");
$example_content = file_get_contents("contents/text_example.txt");
$footer_content = file_get_contents("contents/text_footer.txt");
$agreement_content = file_get_contents("contents/text_agreement.txt");
$email_confirm_content = file_get_contents("contents/text_email_confirm.txt");
$page_lastedit = time();


$sql_portal_site = "INSERT INTO ".DB_PREFIX."pages (
							page_id , page_language , page_linkname ,
							page_title , page_status , page_content ,
							page_lastedit ,	page_lastedit_from , page_template ,
							page_template_layout , page_sort , page_meta_author ,
							page_meta_date , page_meta_keywords , page_meta_description ,
							page_meta_robots , page_meta_enhanced , page_head_styles ,
							page_head_enhanced , page_modul, page_authorized_users
						) VALUES (
							NULL, '$languagePack', 'Startseite', 'Home',
							'public', '$portal_content', '$page_lastedit',
							'Installer', 'blucent', 'layout_portal.tpl',
							'portal', 'Installer', '$page_lastedit',
							'Lorem, ipsum, dolor, sit', 'Testseite',
							'all', '', '',
							'',	'',	'' )
							";



$sql_first_site = "INSERT INTO ".DB_PREFIX."pages (
						page_id , page_language , page_linkname ,
						page_title , page_status , page_permalink ,
						page_content , page_lastedit , page_lastedit_from ,
						page_template ,	page_sort ,	page_meta_author ,
						page_meta_date , page_meta_keywords , page_meta_description ,
						page_meta_robots , page_meta_enhanced ,	page_head_styles ,
						page_head_enhanced , page_modul, page_authorized_users 
						) VALUES (
						NULL, '$languagePack', 'Testseite',
						'flatCore',	'public', 'flatcore/',
						'$example_content', '$page_lastedit', 'Installer',
						'use_standard', '10', 'Installer',
						'$page_lastedit', 'Lorem, ipsum, dolor, sit', 'Testseite',
						'all', '', '',
						'', '', '' ) ";


$sql_insert_prefs = "INSERT INTO ".DB_PREFIX."preferences (
		prefs_id, prefs_status, prefs_pagetitle,
		prefs_pagesubtitle, prefs_template, prefs_showloginform, prefs_xml_sitemap,
		prefs_imagesuffix, prefs_maximagewidth, prefs_maximageheight, prefs_maxfilesize,
		prefs_logfile, prefs_template_layout, prefs_rss_time_offset
		) VALUES (
		NULL, 'active', 'Diese Homepage',
		'rockt mit SQLite und PHP5', 'blucent', 'yes', 'off',
		'jpg jpeg gif png', '600', '500', '2800',
		'on', 'layout_default.tpl', '216000' )";

$sql_tl_footer_text = "INSERT INTO ".DB_PREFIX."textlib (
						textlib_id , textlib_name , textlib_content , textlib_lang 
						) VALUES (
						NULL , 'footer_text' , '$footer_content' , 'de' )";

$sql_tl_extra_content_text = "INSERT INTO ".DB_PREFIX."textlib (
								textlib_id , textlib_name , textlib_content , textlib_lang
								) VALUES (
								NULL , 'extra_content_text' , '' , 'de' )";

$sql_tl_agreement_text = "INSERT INTO ".DB_PREFIX."textlib (
							textlib_id , textlib_name , textlib_content , textlib_lang
							) VALUES (
							NULL , 'agreement_text' , '$agreement_content' , 'de' )";

$sql_tl_account_confirm = "INSERT INTO ".DB_PREFIX."textlib (
							textlib_id , textlib_name , textlib_content , textlib_lang 
							) VALUES (
							NULL , 'account_confirm' , '<p>Dein Account wurde erfolgreich freigeschaltet.</p>' , 'de' )";

$sql_tl_account_confirm_mail = "INSERT INTO ".DB_PREFIX."textlib (
								textlib_id , textlib_name , textlib_content , textlib_lang 
								) VALUES ( 
								NULL , 'account_confirm_mail' , '$email_confirm_content' , 'de' )";

$sql_tl_no_access = "INSERT INTO ".DB_PREFIX."textlib (
						textlib_id , textlib_name , textlib_content , textlib_lang 
						) VALUES (
						NULL , 'no_access' , 'Zugriff verweigert...' , 'de' )";


$sql_pages_table = fc_generate_sql_query("fc_pages.php","$db_type");
$sql_pages_cache_table = fc_generate_sql_query("fc_pages_cache.php","$db_type");
$sql_preferences_table = fc_generate_sql_query("fc_preferences.php","$db_type");
$sql_textlib_table = fc_generate_sql_query("fc_textlib.php","$db_type");
$sql_comments_table = fc_generate_sql_query("fc_comments.php","$db_type");
$sql_media_table = fc_generate_sql_query("fc_media.php","$db_type");
$sql_labels_table = fc_generate_sql_query("fc_labels.php","$db_type");
$sql_addons_table = fc_generate_sql_query("fc_addons.php","$db_type");


if(!isset($db_host)) {
	$dbh = dbconnect('sqlite','../'.$fc_db_content);
}

	dbquery($sql_pages_table);
	dbquery($sql_pages_cache_table);
	dbquery($sql_preferences_table);
	dbquery($sql_textlib_table);
	dbquery($sql_comments_table);
	dbquery($sql_media_table);
	dbquery($sql_feeds_table);
	dbquery($sql_portal_site);
	dbquery($sql_first_site);
	dbquery($sql_tl_footer_text);
	dbquery($sql_tl_extra_content_text);
	dbquery($sql_tl_agreement_text);
	dbquery($sql_tl_account_confirm);
	dbquery($sql_tl_account_confirm_mail);
	dbquery($sql_tl_no_access);
	dbquery($sql_labels_table);
	dbquery($sql_addons_table);

	dbquery($sql_insert_prefs);



/**
 * DATABASE TRACKER
 */

$sql_hits_table = fc_generate_sql_query("fc_hits.php","$db_type");
$sql_log_table = fc_generate_sql_query("fc_log.php","$db_type");


if(!isset($db_host)) {
	$dbh = dbconnect('sqlite','../'.$fc_db_stats);
}

dbquery($sql_hits_table);
dbquery($sql_log_table);


echo '<div class="alert alert-success">'.$lang['installed'].' | Admin: '.$username.'</div>';
echo '<hr><a class="btn" href="../acp/index.php">'.$lang['link_admin'].'</a><hr>';

?>