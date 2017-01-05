<?php

$database = "content";
$table_name = DB_PREFIX."textlib";

$cols = array(
  "textlib_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "textlib_name"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_title"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_content"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_keywords"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_labels"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_notes"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_lastedit"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_lastedit_from"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_lang" => "VARCHAR(255) NOT NULL DEFAULT ''"
  );

?>