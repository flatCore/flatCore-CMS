<?php

$database = "user";
$table_name = "fc_tokens";

$cols = array(
  "token_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "user_id"  => 'INTEGER(12)',
  "identifier"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "securitytoken" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "time" => "VARCHAR(255) NOT NULL DEFAULT ''"
  );

?>