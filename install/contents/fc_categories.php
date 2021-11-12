<?php

$database = "content";
$table_name = "fc_categories";

$cols = array(
  "cat_id" => 'INTEGER(50) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "cat_lang"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "cat_name" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "cat_name_clean" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "cat_hash" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "cat_description" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "cat_thumbnail" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "cat_sort" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "cat_counter" => 'INTEGER(50)'
  );

?>