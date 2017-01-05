<?php

$database = "content";
$table_name = DB_PREFIX."feeds";

$cols = array(
  "feed_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "feed_subid"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "feed_name"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "feed_title" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "feed_text" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "feed_time" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "feed_url" => "VARCHAR(255) NOT NULL DEFAULT ''"
  
  );

?>