<?php
	
/**
 * database for addons
 *
 * type	= module | plugin | theme
 *
 */
 
$database = "content";
$table_name = "fc_addons";

$cols = array(
  "addon_id"  => 'INTEGER NOT NULL PRIMARY KEY',
  "addon_type"  => 'VARCHAR',
  "addon_dir"  => 'VARCHAR',
  "addon_name"  => 'VARCHAR',
  "addon_version"  => 'VARCHAR'
  );

?>
