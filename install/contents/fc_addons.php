<?php
	
/**
 * database for addons
 *
 * type	= module | plugin | theme
 *
 */
 
$database = 'content';
$table_name = 'fc_addons';

$cols = array(
  "addon_id"  => 'INTEGER(12) NOT NULL PRIMARY KEY AUTO_INCREMENT',
  "addon_type"  => "VARCHAR(20) NOT NULL DEFAULT ''",
  "addon_dir"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "addon_name"  => "VARCHAR(255) NOT NULL DEFAULT ''",
  "addon_version"  => "VARCHAR(255) NOT NULL DEFAULT ''"
  );

?>
