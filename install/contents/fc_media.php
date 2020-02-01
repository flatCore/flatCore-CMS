<?php

$database = "content";
$table_name = "fc_media";

$cols = array(
  "media_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "media_lang"  => 'VARCHAR',
  "media_type"  => 'VARCHAR',
  "media_file"  => 'VARCHAR',
  "media_title"  => 'VARCHAR',
  "media_description"  => 'VARCHAR',
  "media_keywords" => 'VARCHAR',
  "media_group" => 'VARCHAR',
  "media_url" => 'VARCHAR',
  "media_alt" => 'VARCHAR',
  "media_classes" => 'VARCHAR',
  "media_priority" => 'VARCHAR',
  "media_text"  => 'VARCHAR',
  "media_credit"  => 'VARCHAR',
  "media_license"  => 'VARCHAR',
  "media_version"  => 'VARCHAR',
  "media_meta"  => 'VARCHAR',
  "media_notes"  => 'VARCHAR',
  "media_labels"  => 'VARCHAR',
  "media_filesize"  => 'VARCHAR',
  "media_lastedit"  => 'VARCHAR'
  );

?>
