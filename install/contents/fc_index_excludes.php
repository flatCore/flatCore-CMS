<?php

$database = "tracker";
$table_name = "excludes";

$cols = array(
  "item_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "item_element"  => 'VARCHAR',
  "item_attributes"  => 'VARCHAR',
  "item_url"  => 'VARCHAR'
  
  );

?>