<?php

$database = "content";
$table_name = DB_PREFIX."comments";

$cols = array(
  "comment_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "comment_hash"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_parent"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_time"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_author"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_text" => "VARCHAR(20) NOT NULL DEFAULT ''"
  );

?>
