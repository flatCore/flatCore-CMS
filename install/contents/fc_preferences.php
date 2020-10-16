<?php

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
  "prefs_usertemplate" => "VARCHAR(50) NOT NULL DEFAULT ''",
  "prefs_default_language" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_imagesuffix" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_maximagewidth" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_maximageheight" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_uploads_remain_unchanged" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_filesuffix" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_maxfilesize" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_showfilesize" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_userregistration" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_showloginform" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_logfile" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_anonymize_ip" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_template_layout" => "VARCHAR(100) NOT NULL DEFAULT ''",
  "prefs_xml_sitemap" => "VARCHAR(20) NOT NULL DEFAULT ''",
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
  
  /* posts */
  
	"prefs_posts_url_pattern" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"prefs_posts_images_prefix" => "VARCHAR(20) NOT NULL DEFAULT ''",
	"prefs_posts_default_banner" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"prefs_posts_entries_per_page" => 'INTEGER(12)',
	"prefs_posts_products_default_tax" => 'INTEGER(12)',
	"prefs_posts_products_tax_alt1" => 'INTEGER(12)',
	"prefs_posts_products_tax_alt2" => 'INTEGER(12)',
	"prefs_posts_products_default_currency" => "VARCHAR(100) NOT NULL DEFAULT ''",
	"prefs_posts_event_time_offset" => "VARCHAR(100) NOT NULL DEFAULT ''"
  
  );

?>