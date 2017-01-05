<?php

$database = "tracker";
$table_name = DB_PREFIX."hits";

$cols = array(
  "hits_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "page_id"  => 'INTEGER(12)',
  "counter"  => 'INTEGER(12)'
  
  );

?>