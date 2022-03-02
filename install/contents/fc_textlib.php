<?php
	
/**
 * textlib_type	-> snippet_core, snippet or shortcode
 * 					-> product_feature
 */

$database = "content";
$table_name = "fc_textlib";

$cols = array(
  "textlib_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "textlib_type"  => "VARCHAR(50) NOT NULL DEFAULT ''",
  "textlib_shortcode"  => "VARCHAR(50) NOT NULL DEFAULT ''",
  "textlib_name"  => "VARCHAR(50) NOT NULL DEFAULT ''",
  "textlib_title"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_content"  => "LONGTEXT NOT NULL DEFAULT ''",
  "textlib_teaser"  => "LONGTEXT NOT NULL DEFAULT ''",
  "textlib_keywords"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_classes"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_permalink"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_permalink_name"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_permalink_title"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_permalink_classes"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_images" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_groups"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_labels"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_template"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_theme"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_notes"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_lastedit"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_lastedit_from"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_lang" => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_status"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "textlib_priority" => 'INTEGER(12)'
  );

?>