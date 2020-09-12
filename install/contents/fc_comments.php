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
  "comment_hash"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_parent"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_time"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_author"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_text" => "VARCHAR(20) NOT NULL DEFAULT ''"
  );

?>
