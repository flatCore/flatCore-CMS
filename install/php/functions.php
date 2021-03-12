<?php

if(!defined('INSTALLER')) {
	header("location:../login.php");
	die("PERMISSION DENIED!");
}

/* returns all cols of a existung database/table */
function get_collumns($database,$table_name) {
	
	global $db_content;
	global $db_user;
	global $db_statistics;
	global $db_posts;
	global $db_index;
	global $db_type;
	global $database_name;

	if($db_type == "mysql") {
		$query = "SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$database_name' AND TABLE_NAME = '$table_name'";
	} else {
		$query = "PRAGMA table_info(" . $table_name . ")";
	}	
	
	if($database == "content") {
		$data = $db_content->query($query)->fetchAll(PDO::FETCH_ASSOC);
	} else if($database == "user") {
		$data = $db_user->query($query)->fetchAll(PDO::FETCH_ASSOC);
	} else if($database == "tracker") {
		$data = $db_statistics->query($query)->fetchAll(PDO::FETCH_ASSOC);
	} else if($database == "posts") {
		$data = $db_posts->query($query)->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/* index is a sqlite file */
	if($database == "index") {
		$query = "PRAGMA table_info(" . $table_name . ")";
		$data = $db_index->query($query)->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	
	$meta = array();
	foreach ($data as $row) {
		$meta[$row['COLUMN_NAME']] = $row['DATA_TYPE']; /* mysql schema */
		$meta[$row['name']] = $row['type']; /* sqlite schema */
	}


	return $meta;
}



/*  check if table exists - returns the number of existing tables */
function table_exists($database,$table_name) {
	
	global $db_content;
	global $db_user;
	global $db_statistics;
	global $db_posts;
	global $db_type;
	global $database_name;
	global $db_index;
	global $db_type;
	
	if($db_type == "mysql") {
		$query = "SELECT count(*) FROM information_schema.TABLES WHERE (TABLE_SCHEMA = '$database_name') AND (TABLE_NAME = '$table_name')";
	} else {
		$query = "SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='$table_name'";
	}
	
	if($database == "content") {
		$cnt_tables = $db_content->query($query)->fetch();
	} else if($database == "user") {
		$cnt_tables = $db_user->query($query)->fetch();
	} else if($database == "tracker") {
		$cnt_tables = $db_statistics->query($query)->fetch();
	} else if($database == "posts") {
		$cnt_tables = $db_posts->query($query)->fetch();
	} else if($database == "index") {
		$query = "SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='$table_name'";
		$cnt_tables = $db_index->query($query)->fetch();
	}
	
	$cnt_tables = $cnt_tables[0];
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
		$table = FC_PREFIX.$table_name;
		$sql_string = "
		    CREATE TABLE $table (
		    $string
	        ) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci;
	    ";
		
	}

  return $sql_string;
}





function update_table($col_name,$type,$table_name,$database) {
	
	
	global $db_content;
	global $db_user;
	global $db_statistics;
	global $db_posts;
	global $db_type;
	global $database_name;
	global $db_index;
	
		
	$sql = "ALTER TABLE $table_name ADD $col_name $type";
	
	if($database == "content") {
		$db_content->query($sql);
	} else if($database == "user") {
		$db_user->query($sql);
	} else if($database == "tracker") {
		$db_statistics->query($sql);
	} else if($database == "posts") {
		$db_posts->query($sql);
	} else if($database == "index") {
		$db_index->query("DROP TABLE $table_name");
	}
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

function add_table($database,$table_name,$cols) {

	global $db_content;
	global $db_user;
	global $db_statistics;
	global $db_posts;
	global $db_type;
	global $db_index;
	global $database_name;


	if($db_type == 'sqlite') {

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
		
		$cols_string = substr(trim("$string"), 0,-1); // cut last commata and returns
	
	} else {

		foreach ($cols as $k => $v) {
			$cols_string .= "$k $cols[$k],\r";
		}
		$cols_string = substr(trim("$cols_string"), 0,-1);
	}
		
	$sql = "CREATE TABLE $table_name ( $cols_string )";
	
	if($database == "content") {
		$db_content->query($sql);
	} else if($database == "user") {
		$db_user->query($sql);
	} else if($database == "tracker") {
		$db_statistics->query($sql);
	} else if($database == "posts") {
		$db_posts->query($sql);
	} else if($database == "index") {
		$db_index->query($sql);
	}

}

// create virtual table

function add_virtual_table($db,$table_name,$cols) {
	
	global $db_index;

	foreach ($cols as $k => $v) {
		$cols_string .= "$k $cols[$k],";
	}
	
	$cols_string = substr(trim("$cols_string"), 0,-1); // cut last commata and returns

	$db_index->query("SET NAMES 'utf-8'");
	$db_index->query("CREATE VIRTUAL TABLE $table_name USING fts3($cols_string,tokenize=porter)");

		
}





?>