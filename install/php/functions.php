<?php


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






/* generate an sql query from templates (php files) */
function generate_sql_query($file) {

	include("contents/$file");

	foreach ($cols as $k => $v) {
    	$string .= "$k $v,\r"; 
	}

	$string = substr(trim("$string"), 0,-1); // cut last commata and returns

	$sql_string = "
		CREATE TABLE $table_name (
		$string
	  )
	";

  /* return the sql string */
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





?>