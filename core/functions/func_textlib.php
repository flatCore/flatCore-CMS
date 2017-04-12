<?php

/**
 * Print Messages
 * Source: lang/$lang/msg/$file.txt
 */

function print_msg($file,$l) {	
	$text = file_get_contents("lib/lang/$l/msg/$file");
	$text = nl2br($text);
	return($text);
}


/**
 * Text Snippets
 */


function get_textlib($textlib_name,$textlib_lang) {

	global $fc_db_content;
	global $languagePack;
	
	if(empty($textlib_lang)) {
		$textlib_lang = $languagePack;
	}
	
	try {
		$dbh = new PDO("sqlite:$fc_db_content");
		//$text = $dbh->quote($text);
		$sql = 'SELECT * FROM fc_textlib WHERE textlib_name = :textlib_name AND textlib_lang = :textlib_lang ';
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':textlib_name', $textlib_name, PDO::PARAM_STR);
		$sth->bindParam(':textlib_lang', $textlib_lang, PDO::PARAM_STR);
		$sth->execute();
		$textlibData = $sth->fetch(PDO::FETCH_ASSOC);
		$dbh = null;
		
		foreach($textlibData as $k => $v) {
	   		$$k = stripslashes($v);
		}

		return $textlib_content;	
	}
	
	catch (PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
}



function get_textlib_by_fn($fn) {

	global $fc_db_content;

	$dbh = new PDO("sqlite:$fc_db_content");
	$fn = $dbh->quote($fn);
	$sql = "SELECT * FROM fc_textlib WHERE textlib_name = $fn ";
	$result = $dbh->query($sql);
	$result= $result->fetch(PDO::FETCH_ASSOC);
	$dbh = null;

	foreach($result as $k => $v) {
   		$$k = stripslashes($v);
	}

	return $textlib_content;
}



function get_all_textlibs() {
	
	global $fc_db_content;
	
	$dbh = new PDO("sqlite:$fc_db_content");
	$sql = "SELECT textlib_name, textlib_content, textlib_lang FROM fc_textlib";
	$result = $dbh->query($sql);
	$result= $result->fetchAll(PDO::FETCH_ASSOC);
	$dbh = null;
	
	return $result;
}




?>