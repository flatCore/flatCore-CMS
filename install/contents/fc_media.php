<?php

$database = "content";
$table_name = "fc_media";

$cols = array(
  "media_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "media_type"  => 'VARCHAR',
  "media_file"  => 'VARCHAR',
  "media_title"  => 'VARCHAR',
  "media_description"  => 'VARCHAR',
  "media_keywords" => 'VARCHAR',
  "media_text"  => 'VARCHAR',
  "media_credit"  => 'VARCHAR',
  "media_meta"  => 'VARCHAR'
  );

?>
