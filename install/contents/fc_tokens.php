<?php

$database = "user";
$table_name = "fc_tokens";

$cols = array(
  "token_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "user_id"  => 'INTEGER',
  "identifier"  => 'VARCHAR',
  "securitytoken" => 'VARCHAR',
  "time" => 'VARCHAR'  
  );

?>