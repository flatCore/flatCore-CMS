<?php

$database = "content";
$table_name = "fc_preferences";

$cols = array(
  "prefs_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "prefs_status"  => 'VARCHAR',
  "prefs_pagetitle"  => 'VARCHAR',
  "prefs_pagesubtitle" => 'VARCHAR',
  "prefs_pagethumbnail" => 'VARCHAR',
  "prefs_pagesglobalhead" => 'VARCHAR',
  "prefs_template" => 'VARCHAR',
  "prefs_usertemplate" => 'VARCHAR',
  "prefs_imagesuffix" => 'VARCHAR',
  "prefs_maximagewidth" => 'VARCHAR',
  "prefs_maximageheight" => 'VARCHAR',
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
  "prefs_rss_time_offset" => 'VARCHAR'
  
  );

?>