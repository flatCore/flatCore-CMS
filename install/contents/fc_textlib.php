<?php

$database = "content";
$table_name = "fc_textlib";

$cols = array(
  "textlib_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "textlib_name"  => 'VARCHAR',
  "textlib_title"  => 'VARCHAR',
  "textlib_content"  => 'VARCHAR',
  "textlib_keywords"  => 'VARCHAR',
  "textlib_groups"  => 'VARCHAR',
  "textlib_labels"  => 'VARCHAR',
  "textlib_notes"  => 'VARCHAR',
  "textlib_lastedit"  => 'VARCHAR',
  "textlib_lastedit_from"  => 'VARCHAR',
  "textlib_lang" => 'VARCHAR'
  );

?>