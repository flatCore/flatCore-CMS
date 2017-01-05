<?php

$database = "tracker";
$table_name = DB_PREFIX."log";

$cols = array(
  "log_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "log_time"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "log_trigger"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "log_entry"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "log_priority"  => 'INTEGER(12)'
  
  );

?>