<?php
	
/**
 * database for themes
 *
 */
 
$database = 'content';
$table_name = 'fc_themes';

$cols = array(
  "theme_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "theme_name"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "theme_label"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "theme_value"  => "VARCHAR(255) NOT NULL DEFAULT ''"
  );

?>
