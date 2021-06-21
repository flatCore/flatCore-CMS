<?php
	
/**
 *
 * prefs_status - active -> this set will be used by the system
 *
 * prefs_comments_mode - 1 -> all comments must be activated by an admin
 *                     - 2 -> all comments will be public immediately
 *                     - 3 -> all comment functions deactivated
 *
 * prefs_comments_autoclose -> time (seconds) when comment section is closed
 *
 * prefs_comments_authorization - 1 -> only registered users
 *                              - 2 -> user have to fill out name and e-mail
 *                              - 3 -> accept all anonymous inputs
 *
 * prefs_comments_max_entries -> maximum number of entries per thread
 * prefs_comments_max_level -> maximum depth of a thread
 *
 */

$database = "content";
$table_name = "fc_preferences";

$cols = array(
  "prefs_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "prefs_status"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_pagename"  => "VARCHAR(500) NOT NULL DEFAULT ''",
  "prefs_pagetitle"  => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_pagesubtitle" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_pagedescription" => "VARCHAR(500) NOT NULL DEFAULT ''",
  "prefs_pagethumbnail" => "VARCHAR(50) NOT NULL DEFAULT ''",
  "prefs_pagethumbnail_prefix" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_pagefavicon" => "VARCHAR(50) NOT NULL DEFAULT ''",
  "prefs_pagesglobalhead" => "VARCHAR(500) NOT NULL DEFAULT ''",
  "prefs_nbr_page_versions" => 'INTEGER(12)',
  "prefs_template" => "VARCHAR(50) NOT NULL DEFAULT ''",
  "prefs_template_layout" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_template_stylesheet" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_usertemplate" => "VARCHAR(50) NOT NULL DEFAULT ''",
  "prefs_default_language" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_imagesuffix" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_maximagewidth" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_maximageheight" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_maxtmbwidth" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_maxtmbheight" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_uploads_remain_unchanged" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_filesuffix" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_maxfilesize" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_showfilesize" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_userregistration" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_showloginform" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_logfile" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_anonymize_ip" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_xml_sitemap" => "VARCHAR(20) NOT NULL DEFAULT ''",
  
  /* Date and Time */
  
  "prefs_timezone" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_dateformat" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_timeformat" => "VARCHAR(100) NOT NULL DEFAULT ''",
  
  /* E-Mail */
  
  "prefs_mailer_adr" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_mailer_name" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_mailer_type" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_mailer_return_path" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_smtp_host" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_smtp_port" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_smtp_encryption" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_smtp_authentication" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_smtp_username" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_smtp_psw" => "VARCHAR(100) NOT NULL DEFAULT ''",
  
  "prefs_rss_time_offset" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_cms_domain" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_cms_ssl_domain" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_cms_base" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_smarty_cache" => 'INTEGER(12)',
  "prefs_smarty_cache_lifetime" => 'INTEGER(12)',
  "prefs_smarty_compile_check" => 'INTEGER(12)',
  "prefs_deleted_resources" => "VARCHAR(500) NOT NULL DEFAULT ''",
  "prefs_default_publisher" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_publisher_mode" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_acp_session_lifetime" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_pagesort_minlength" => "VARCHAR(20) NOT NULL DEFAULT ''",
  
  /* posts */
  
	"prefs_posts_url_pattern" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"prefs_posts_images_prefix" => "VARCHAR(20) NOT NULL DEFAULT ''",
	"prefs_posts_default_banner" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"prefs_posts_entries_per_page" => 'INTEGER(12)',
	"prefs_posts_products_default_tax" => 'INTEGER(12)',
	"prefs_posts_products_tax_alt1" => 'INTEGER(12)',
	"prefs_posts_products_tax_alt2" => 'INTEGER(12)',
	"prefs_posts_products_default_currency" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"prefs_posts_event_time_offset" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"prefs_posts_default_guestlist" => 'INTEGER(12)',
	"prefs_posts_default_votings" => 'INTEGER(12)',
	
	/* comments */
  
  "prefs_comments_mode" => 'INTEGER(12)',
  "prefs_comments_autoclose" => 'INTEGER(12)',
  "prefs_comments_authorization" => 'INTEGER(12)',
  "prefs_comments_max_entries" => 'INTEGER(12)',
  "prefs_comments_max_level" => 'INTEGER(12)'
  
  );

?>