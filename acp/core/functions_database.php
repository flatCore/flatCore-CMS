<?php

/**
 * Database Functions
 * @author Patrick Konstandin
 * @since 26.05.2012
 */



/**
 * Generate SQL UPDATE query with prepared statements
 * Example: "UPDATE myTable SET name = :name WHERE id = 1
 *
 * @param array		$fields	contains the fields that should be changed | 'name' => 'STR'
 * @param	string	$table	name of the table
 * @param	string	$where
 */
function generate_sql_update_str($fields,$table,$where) {
  
  $str = '';
  
  foreach($fields as $key => $val) {
  	$str .=	"$key = :$key".', '; 
  }
  
  $str = substr($str, 0, -2);
  
  $sql_str = "UPDATE $table SET $str $where";
  
  return $sql_str;	
  
}




/**
 * Generate SQL INSERT query with prepared statements
 * Example: "INSERT INTO myTable ( name, age ) VALUES ( :name, :age )
 *
 * @param array		$fields	contains the fields that should be changed | 'name' => 'STR'
 * @param	string	$table	name of the table
 */
function generate_sql_insert_str($fields,$table) {
  
  $cols = '';
  $vals = '';
  
  foreach($fields as $key => $val) {
  	$cols .=	"$key".', '; 
  }
  
  $cols = substr($cols, 0, -2);

  foreach($fields as $key => $val) {
  	if($val == "NULL") {
  		$vals .= "NULL, ";
  	} else {
	  	$vals .=	":$key".', '; 
  	}
  	
  }
  
  $vals = substr($vals, 0, -2);

  
  $sql_str = "INSERT INTO $table ( $cols ) VALUES ( $vals )";
  
  return $sql_str;	
  
}




/**
 * returns the prepared statements
 * Example: $sth->bindParam(':name', $name, PDO::PARAM_STR);
 *
 * @param array		$field	the prepared statements
 * @param	string	$db			the database object
 */


function generate_bindParam_str($field,$db) {

  foreach($field as $key => &$val) {
  	if($val != "NULL") {
	  	$db->bindParam(":$key", $_POST[$key], setPDOConstant($val));
  	}
  	
  }
  
}


/**
 * returns PDO Constants
 *
 * @param	string	$db
 */
 
function setPDOConstant($val) {

  	if($val == "STR") {
  		return PDO::PARAM_STR;
  	}
  	
  	if($val == "INT") {
  		return PDO::PARAM_INT;
  	}		

}


?>