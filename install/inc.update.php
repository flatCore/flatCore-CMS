<?php

/**
 * copy maintance.hml to root folder
 * update existing tables
 */

if(!defined('INSTALLER')) {
	header("location:login.php");
	die("PERMISSION DENIED!");
}

if($_SESSION['user_class'] != "administrator"){
	//move to login or die
	header("location:login.php");
	die("PERMISSION DENIED!");
}

copy('maintance.html', '../maintance.html');

echo '<h2>UPDATE</h2>';

/* build an array from all php files in folder contents */
$all_tables = glob("contents/*.php");


for($i=0;$i<count($all_tables);$i++) {

	unset($db_path,$table_name);
	include $all_tables[$i]; // returns $cols and $table_name
	
	if($database == "content") {
		$db_path = "../$fc_db_content";
	}

	if($database == "user") {
		$db_path = "../$fc_db_user";
	}

	if($database == "tracker") {
		$db_path = "../$fc_db_stats";
	}


	$is_table = table_exists("$db_path","$table_name");

	if($is_table < 1) {
		add_table("$db_path","$table_name",$cols);
		$table_updates[] = "New Table: <b>$table_name</b> in Database <b>$database</b>";
	}


	$existing_cols = get_collumns("$db_path","$table_name");


	foreach ($cols as $k => $v) {
   
  		if(!array_key_exists("$k", $existing_cols)) {
  			//update_table -> column, type, table, database
  			update_table("$k","$cols[$k]","$table_name","$db_path");
  			$col_updates[] = "New Column: <b>$k</b> in table <b>$table_name</b>";	
  		}
     
	} // eo foreach




	/* updates are done, check all columns again */

	$existing_cols = get_collumns("$db_path","$table_name");

	foreach ($cols as $b => $x) {
       
  		if(!array_key_exists("$b", $existing_cols)) {
  			$fails[] = "Missing Column: <b>$b</b> - table: <b>$table_name</b>";  	
  		} else {
  			$wins[] = "Column <b>$b</b> in table <b>$table_name</b> is ready";
  	}
  
	} // eo foreach


} // EO $i



/* echo fails and wins */

if(is_array($fails)) {
	echo "<h3>" . count($fails) . " ERRORS</h3>";
	
	foreach ($fails as $value) {
			echo"<span class='red'>$value</span><br />";
		}
	
} else {
	echo "<h3>" . count($wins) . " Columns are ready</h3>";
	
	if(is_array($table_updates)) {
		foreach ($table_updates as $value) {
			echo"<span class='green'>$value</span><br />";
		}
	}
	
	if(is_array($col_updates)) {
		foreach ($col_updates as $value) {
			echo"<span class='green'>$value</span><br />";
		}
	}
	
}


if(is_file('../maintance.html')) {
	unlink('../maintance.html');
}





?>