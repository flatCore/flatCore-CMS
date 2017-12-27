<?php

$database = "content";
$table_name = "fc_preferences";

$cols = array(
  "prefs_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "prefs_status"  => 'VARCHAR',
  "prefs_pagetitle"  => 'VARCHAR',
  "prefs_pagesubtitle" => 'VARCHAR',
  "prefs_pagethumbnail" => 'VARCHAR',
  "prefs_pagethumbnail_prefix" => 'VARCHAR',
  "prefs_pagefavicon" => 'VARCHAR',
  "prefs_pagesglobalhead" => 'VARCHAR',
  "prefs_nbr_page_versions" => 'INTEGER',
  "prefs_template" => 'VARCHAR',
  "prefs_usertemplate" => 'VARCHAR',
  "prefs_imagesuffix" => 'VARCHAR',
  "prefs_maximagewidth" => 'VARCHAR',
  "prefs_maximageheight" => 'VARCHAR',
  "prefs_uploads_remain_unchanged" => 'VARCHAR',
  "prefs_filesuffix" => 'VARCHAR',
  "prefs_maxfilesize" => 'VARCHAR',
  "prefs_showfilesize" => 'VARCHAR',
  "prefs_userregistration" => 'VARCHAR',
  "prefs_showloginform" => 'VARCHAR',
  "prefs_logfile" => 'VARCHAR',
  "prefs_template_layout" => 'VARCHAR',
  "prefs_xml_sitemap" => 'VARCHAR',
  "prefs_mailer_adr" => 'VARCHAR',
  "prefs_mailer_name" => 'VARCHAR',
  "prefs_mailer_type" => 'VARCHAR',
  "prefs_mailer_return_path" => 'VARCHAR',
  "prefs_smtp_host" => 'VARCHAR',
  "prefs_smtp_port" => 'VARCHAR',
  "prefs_smtp_encryption" => 'VARCHAR',
  "prefs_smtp_authentication" => 'VARCHAR',
  "prefs_smtp_username" => 'VARCHAR',
  "prefs_smtp_psw" => 'VARCHAR',
  "prefs_rss_time_offset" => 'VARCHAR',
  "prefs_cms_domain" => 'VARCHAR',
  "prefs_cms_ssl_domain" => 'VARCHAR',
  "prefs_cms_base" => 'VARCHAR',
  "prefs_smarty_cache" => 'INTEGER',
  "prefs_smarty_cache_lifetime" => 'INTEGER',
  "prefs_smarty_compile_check" => 'INTEGER'
  );

?>