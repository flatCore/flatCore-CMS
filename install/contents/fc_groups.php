<?php

$database = "user";
$table_name = DB_PREFIX."groups";

$cols = array(
  "group_id"  => 'INTEGER(3) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "group_name"  => 'VARCHAR(30) NOT NULL DEFAULT ""',
  "group_description"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "group_user" => "VARCHAR(255) NOT NULL DEFAULT ''"
  
  );

?>