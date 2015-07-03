<?php

$database = "content";
$table_name = "fc_labels";

$cols = array(
  "label_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "label_hash"  => 'VARCHAR',
  "label_color"  => 'VARCHAR',
  "label_title" => 'VARCHAR',
  "label_description" => 'VARCHAR'
  );

?>