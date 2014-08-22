<?php

$database = "tracker";
$table_name = "log";

$cols = array(
  "log_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "log_time"  => 'VARCHAR',
  "log_trigger"  => 'VARCHAR',
  "log_entry"  => 'VARCHAR',
  "log_priority"  => 'INTEGER'
  
  );

?>