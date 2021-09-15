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

	global $db_content;
	global $languagePack;
	
	if(empty($textlib_lang)) {
		$textlib_lang = $languagePack;
	}

	
	$textlibData = $db_content->get("fc_textlib", "*", [
		"AND" => [
			"textlib_name" => "$textlib_name",
			"textlib_lang" => "$textlib_lang"
		]
	]);
		
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
					$img = str_replace('../content/', '/content/', $img);
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


function get_textlib_id($textlib_name,$textlib_lang) {
	global $db_content;
	$textlib = $db_content->get("fc_textlib", "textlib_id", [
		"AND" => [
			"textlib_name" => "$textlib_name",
			"textlib_lang" => "$textlib_lang"
		]
	]);

	return $textlib;	
}


function get_all_textlibs() {
	
	global $db_content;

	$result = $db_content->select("fc_textlib", ["textlib_name", "textlib_content","textlib_lang"]);
	
	return $result;
}




?>