<?php

if(!defined('INSTALLER')) {
	header("location:../login.php");
	die("PERMISSION DENIED!");
}

/* returns all cols and types of a existung database/table */
function get_collumns($db,$table_name) {

	$dbh = new PDO("sqlite:$db");

	$result = $dbh->query("PRAGMA table_info(" . $table_name . ")");
	$result->setFetchMode(PDO::FETCH_ASSOC);
	$meta = array();
	foreach ($result as $row) {
		$meta[$row['name']] = $row['type'];
	}

return $meta;

}



/*  check if table exists - returns the number of existing tables */
function table_exists($db,$table_name) {

	$dbh = new PDO("sqlite:$db");

  $result = $dbh->query("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='$table_name'")->fetch();
	$cnt_tables = $result[0];
	
  return $cnt_tables;
}






/**
 * generate an sql query from templates (php files)
 * distinction is made between SQLite and MySQL
 * $db_type = 'sqlite' or 'mysql'
 * Note:	in  SQLite we have only NULL, INTEGER, REAL, TEXT and BLOB
 * 				we only need INTEGER and TEXT
 */

function fc_generate_sql_query($file,$db_type='sqlite') {
	
	include("contents/$file");
	$string = '';
	
	if($db_type == 'sqlite') {
		/* generate sqlite query */

		foreach ($cols as $k => $v) {
			
			if(strpos($v,'INTEGER') !== false) {
				$str = 'INTEGER';
			} else if(strpos($v,'VARCHAR') !== false) {
				$str = 'VARCHAR';
			} else {
				$str = 'TEXT';
			}
			
			if(strpos($v,'PRIMARY') !== false) {
				$str .= ' NOT NULL PRIMARY KEY';
			}
			
    	$string .= "$k $str,\r";
		}
		
		$string = substr(trim("$string"), 0,-1); // cut last commata and returns
		
		if($table_type == 'virtual') {
			
			$sql_string = "CREATE VIRTUAL TABLE $table_name USING fts3($string,tokenize=porter)";
			
		} else {
			$sql_string = "
				CREATE TABLE $table_name (
				$string
				)
			";		
		}
		
	} else {
		/* generate mysql query */

		foreach ($cols as $k => $v) {
    	$string .= "$k $v,\r"; 
		}
		
		$string = substr(trim("$string"), 0,-1); // cut last commata and returns
		$sql_string = "
		    CREATE TABLE $table_name (
		    $string
	        ) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci;
	    ";
		
	}

  return $sql_string;
}



function update_table($col_name,$type,$table_name,$db) {

$dbh = new PDO("sqlite:$db");

	$sql = "ALTER TABLE $table_name ADD $col_name $type";
	$dbh->exec($sql);

$dbh = null;


}

/*

CREATE TABLE table_name
(
column_name1 data_type,
column_name2 data_type,
column_name3 data_type,
....
)

*/

function add_table($db,$table_name,$cols) {


	foreach ($cols as $k => $v) {
		$cols_string .= "$k $cols[$k],\r";
	}
	
	$cols_string = substr(trim("$cols_string"), 0,-1); // cut last commata and returns
	
	$dbh = new PDO("sqlite:$db");
	
		$sql = "CREATE TABLE $table_name 
							(
							$cols_string
							)";
		$dbh->exec($sql);
	
	$dbh = null;


}

// create virtual table

function add_virtual_table($db,$table_name,$cols) {

	foreach ($cols as $k => $v) {
		$cols_string .= "$k $cols[$k],";
	}
	
	$cols_string = substr(trim("$cols_string"), 0,-1); // cut last commata and returns
	
	$dbh = new PDO("sqlite:$db");
	$dbh->query("SET NAMES 'utf-8'");
	$sql = "CREATE VIRTUAL TABLE $table_name USING fts3($cols_string,tokenize=porter)";
	$dbh->exec($sql);
	$dbh = null;
		
}





?>