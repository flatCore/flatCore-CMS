<?php

if(!defined('INSTALLER')) {
	header("location:../login.php");
	die("PERMISSION DENIED!");
}

/* returns all cols and types of a existung database/table */
// Unter MySQL wird dies nicht funktionieren, auf jedenfall nicht bei shared Hosting
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
			} else {
				$str = 'TEXT';
			}
			
			if(strpos($v,'PRIMARY') !== false) {
				$str .= ' NOT NULL PRIMARY KEY';
			}
			
    	$string .= "$k $str,\r";
		}
		
		$string = substr(trim("$string"), 0,-1); // cut last commata and returns
		$sql_string = "
		    CREATE TABLE $table_name (
		    $string
	        )
	    ";
		
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



/* generate an sql query from templates (php files) */
/* !!! deprecated */
function generate_sql_query($file) {

	include("contents/$file");
	
	$string = "";

	foreach ($cols as $k => $v) {
    	$string .= "$k $v,\r"; 
	}

	$string = substr(trim("$string"), 0,-1); // cut last commata and returns
	
        $sql_string = "
		    CREATE TABLE $table_name (
		    $string
	        ) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci;
	    ";
    

  /* return the sql string */
  return $sql_string;
}


// auch noch ändern !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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





?>