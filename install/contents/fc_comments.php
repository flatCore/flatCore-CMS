<?php

$database = "content";
$table_name = "fc_comments";

$cols = array(
  "comment_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "comment_hash"  => 'VARCHAR',
  "comment_parent"  => 'VARCHAR',
  "comment_time"  => 'VARCHAR',
  "comment_author"  => 'VARCHAR',
  "comment_text" => 'VARCHAR'  
  );

?>
