<?php

$database = "content";
$table_name = DB_PREFIX."media";

$cols = array(
  "media_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "media_lang"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_type"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_file"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_title"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_description"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_keywords" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_group" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_url" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_alt" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_classes" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_priority" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_text"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_credit"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_license"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_meta"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_notes"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "media_labels"  => "VARCHAR(255) NOT NULL DEFAULT ''"
  );

?>
