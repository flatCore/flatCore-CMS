<?php

// PDO variable
$pdo = NULL;


// MySQL database functions
function dbquery($query, $execute=array()) {
    global $pdo;
    $result = $pdo->prepare($query);
    if (!$result) {
        print_r($result->errorInfo());
        return FALSE;
    } else {
        $result->execute($execute);
        return $result;
    }
}


function dbcount($field, $table, $conditions = "") {
    global $pdo;
    $cond = ($conditions ? " WHERE ".$conditions : "");
    $result = $pdo->prepare("SELECT COUNT".$field." FROM ".$table.$cond);
    if (!$result) {
        print_r($result->errorInfo());
        return FALSE;
    } else {
        $result->execute();
        return $result->fetchColumn();
    }
}

function dbresult($query, $row) {
    global $pdo;
    $data = $query->fetchAll();
    if (!$query) {
        print_r($query->errorInfo());
        return FALSE;
    } else {
        $result = $query->getColumnMeta(0);
        return $data[$row][$result['name']];
    }
}

function dbrows($query) {
    return $query->rowCount();
}

function dbarray($query) {
    global $pdo;
    $query->setFetchMode(PDO::FETCH_ASSOC);
    return $query->fetch();
}

function dbarraynum($query) {
    global $pdo;
    $query->setFetchMode(PDO::FETCH_NUM);
    return $query->fetch();
}

function dbconnect($db_type, $db_host, $db_user='', $db_pass='', $db_name='') {
    global $pdo;
    try {
        if($db_type=='mysql'){
            $pdo = new PDO("mysql:host=".$db_host.";dbname=".$db_name.";charset=utf8", $db_user, $db_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
        } else {
            $pdo = new PDO("sqlite:".$db_host);
        }
    } catch (PDOException $error) {
        if($db_type=='mysql'){
            die("<strong>Unable to select MySQL database</strong><br />".$error->getMessage());
        } else {
            die("<strong>Unable to select SQLite database</strong><br />".$error->getMessage());
        }
    }
}

function dblast_insert_id(){
    global $pdo;
    $id = $pdo->lastInsertId();
    return $id;
}

function table_exists($db, $table){
	
	if($db != ""){
		$dbh = new PDO("sqlite:$db");

  		$result = $dbh->query("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='$table_name'")->fetch();
		$cnt_tables = $result[0];
	
  		return $cnt_tables;
	}else{
    	global $pdo;

    	$results = dbquery("SHOW TABLES LIKE '$table'");
    	if(!$results) {
        	return false;
    	}
    	if(dbrows($results)>0){
        	return true;
    	}else return false;
	}
}
?>