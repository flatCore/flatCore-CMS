<?php

/**
 * database used for
 *
 * comment_type - p -> comments on pages
 *              - b -> comments on blog posts
 *              - c -> chat in the acp
 *
 * comment_status - 1 -> public
 *                - 2 -> wait for approval
 *
 * comment_parent_id -> if it's an answer
 *
 */

$database = "content";
$table_name = "fc_comments";

$cols = array(
  "comment_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "comment_parent_id"  => "INTEGER(12)",
  "comment_type"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_status"  => "INTEGER(12)",
  "comment_time"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_author"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_author_id"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_text" => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_lastedit"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "comment_lastedit_from"  => "VARCHAR(50) NOT NULL DEFAULT ''"
  );

?>
