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

		if($textlib_theme != '' OR $textlib_theme != 'use_standard') {
			
			$get_tpl_file = 'styles/'.$textlib_theme.'/templates/'.$textlib_template;
			
			if(is_file("$get_tpl_file")) {
				$tpl_file = file_get_contents($get_tpl_file);
				
				$snippet_thumbnail_array = explode("<->", $textlib_images);
				if(count($snippet_thumbnail_array) > 0) {
					foreach($snippet_thumbnail_array as $img) {
						$tpl_file = str_replace('{$snippet_img_src}',$img,$tpl_file);						
					}
				}
				
				$tpl_file = str_replace('{$snippet_title}',$textlib_title,$tpl_file);
				$tpl_file = str_replace('{$snippet_text}',$textlib_content,$tpl_file);
				$tpl_file = str_replace('{$snippet_teaser}',$textlib_teaser,$tpl_file);
				$tpl_file = str_replace('{$snippet_classes}',$textlib_classes,$tpl_file);
				$tpl_file = str_replace('{$snippet_url}',$textlib_permalink,$tpl_file);
				$tpl_file = str_replace('{$snippet_url_name}',$textlib_permalink_name,$tpl_file);
				$tpl_file = str_replace('{$snippet_url_title}',$textlib_permalink_title,$tpl_file);
				$tpl_file = str_replace('{$snippet_url_classes}',$textlib_permalink_classes,$tpl_file);
				return $tpl_file;
			}
			
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