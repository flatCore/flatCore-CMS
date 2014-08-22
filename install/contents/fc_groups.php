<?php

$database = "user";
$table_name = "fc_groups";

$cols = array(
  "group_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "group_name"  => 'VARCHAR',
  "group_description"  => 'VARCHAR',
  "group_user" => 'VARCHAR'
  
  );

?>