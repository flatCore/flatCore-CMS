<?php

$database = "content";
$table_name = "fc_feeds";

$cols = array(
  "feed_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "feed_subid"  => 'VARCHAR',
  "feed_name"  => 'VARCHAR',
  "feed_title" => 'VARCHAR',
  "feed_text" => 'VARCHAR',
  "feed_time" => 'VARCHAR',
  "feed_url" => 'VARCHAR'
  
  );

?>