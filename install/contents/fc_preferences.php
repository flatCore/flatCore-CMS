<?php

$database = "content";
$table_name = DB_PREFIX."preferences";

$cols = array(
  "prefs_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "prefs_status"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_pagetitle"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_pagesubtitle" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_pagethumbnail" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_pagesglobalhead" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_template" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_usertemplate" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_imagesuffix" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_maximagewidth" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_maximageheight" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_filesuffix" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_maxfilesize" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_showfilesize" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_userregistration" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_showloginform" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_logfile" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_template_layout" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_xml_sitemap" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_mailer_adr" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_mailer_name" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "prefs_rss_time_offset" => "VARCHAR(20) NOT NULL DEFAULT ''"
  
  );

?>