<?php

$database = "tracker";
$table_name = "hits";

$cols = array(
  "hits_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "page_id"  => 'INTEGER',
  "counter"  => 'INTEGER'
  
  );

?>