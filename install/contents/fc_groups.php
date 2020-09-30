<?php

$database = "user";
$table_name = "fc_groups";

$cols = array(
  "group_id" => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "group_name" => "VARCHAR(50) NOT NULL DEFAULT ''",
  "group_description" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "group_user" => "VARCHAR(255) NOT NULL DEFAULT ''"
  );
?>