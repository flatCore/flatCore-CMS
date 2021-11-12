<?php


/**
 * CLEAN VARS // URL PARAMETERS
 */

function clean_vars($var) {
	$chars = array('<', '>', '\\', '/', '=','..'); 
	$var = str_replace($chars, "", $var);
	$var = strip_tags($var);
	return $var;
}

function clean_filename($str) {
	$str = strtolower($str);
	$a = array('ä','ö','ü','ß',' - ',' + ',' / ','/'); 
	$b = array('ae','oe','ue','ss','-','-','-','-');
	$str = str_replace($a, $b, $str);
	$str = preg_replace('/\s/s', '_', $str);  // replace blanks -> '_'
	$str = preg_replace('/[^a-z0-9_-]/isU', '', $str); // only a-z 0-9
	$str = trim($str); 
	return $str; 
}

function fc_filter_filepath($str) {
	$str = strip_tags($str);
	$remove_chars = array('<','>','\\','=','@','(',')',' ',',','%','');
	$str = preg_replace('/\s/s', '_', $str);
	$str = str_replace($remove_chars, "", $str);
	return $str; 
}

function fc_return_clean_value($string) {
	$string = stripslashes($string);
	$remove_chars = array('$','`','{','}');
	$string = htmlentities($string, ENT_QUOTES, "UTF-8");
	$string = str_replace($remove_chars, "", $string);
	return $string;
}

function fc_clean_permalink($str) {
	$str = stripslashes($str);
	$str = strip_tags($str);
	$str = strtolower($str);
	$a = array('ä','ö','ü','ß',' + ','//','(',')',';','\'','\\','.','`','<','>','$'); 
	$b = array('ae','oe','ue','ss','-','/','','','','','','','','','','');
	$str = str_replace($a, $b, $str);
	$str = preg_replace('/\s/s', '_', $str);  // replace blanks -> '_'
	$str = htmlentities($str, ENT_QUOTES, "UTF-8");
	$str = trim($str);
	
	return $str; 
}

function fc_clean_query($str) {
	$str = stripslashes($str);
	$str = strip_tags($str);
	$str = strtolower($str);
	
	/* remove unsecure chars */
	$remove_chars = array('<','>','\\',';','@','(',')','`',',','%','$');
	$str = str_replace($remove_chars, "", $str);
	
	$a = array('ä','ö','ü','ß','+','//'); 
	$b = array('ae','oe','ue','ss','-','/');
	$str = str_replace($a, $b, $str);
	
	$str = preg_replace('/\s/s', '_', $str);  // replace blanks -> '_'
	$str = htmlentities($str, ENT_QUOTES, "UTF-8");
	$str = trim($str);
	
	return $str; 	
}


/**
 * sanitize user inputs
 */

function sanitizeUserInputs($str,$type='str',$flags=NULL) {
	
	if($type == 'str') {
		$str = trim($str);	
		$str = strip_tags($str);
		$str = filter_var($str, FILTER_SANITIZE_STRING);
		$str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
	}
	
	return $str;
	
}

/**
 * remove tags [include] [script] [plugin] and [snippet]
 */

function clean_visitors_input($text) {

	$text = preg_replace("/\[snippet\](.*?)\[\/snippet\]/esi","",$text);
	$text = preg_replace("/\[script\](.*?)\[\/script\]/esi","",$text);
	$text = preg_replace("/\[include\](.*?)\[\/include\]/esi","",$text);
	$text = preg_replace("/\[plugin=(.*?)\](.*?)\[\/plugin\]/esi","",$text);
	
	return $text;

}



?>