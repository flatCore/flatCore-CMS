<?php

$database = "content";
$table_name = DB_PREFIX."labels";

$cols = array(
  "label_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "label_hash"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "label_color"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "label_title" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "label_description" => "VARCHAR(255) NOT NULL DEFAULT ''"
  );

?>