<?php

/**
 * database used for
 * acp -> pages -> comments
 * comment_parent = p + page_id e.g. p22
 *
 * acp -> chat -> entries
 * comment_parent = c
 */

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
