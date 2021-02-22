<?php

/**
 * custom theme functions
 *
 * @package		styles/default
 * @author		Patrick Konstandin <support@flatcore.org>
 *
 */

/* theme_text_parser extends the basic text_parser */

function theme_text_parser($str) {
	
	$str = theme_replacer($str);

	return $str;
}


function theme_replacer($string) {

	/* twitter bootstrap grid */	
	
	$string = str_replace('[row]', '<div class="row">', $string);
	$string = str_replace('[/row]', '</div>', $string);
	
	$string = str_replace('[col1]', '<div class="col-md-1">', $string);
	$string = str_replace('[col2]', '<div class="col-md-2">', $string);
	$string = str_replace('[col3]', '<div class="col-md-3">', $string);
	$string = str_replace('[col4]', '<div class="col-md-4">', $string);
	$string = str_replace('[col5]', '<div class="col-md-5">', $string);
	$string = str_replace('[col6]', '<div class="col-md-6">', $string);
	$string = str_replace('[col7]', '<div class="col-md-7">', $string);
	$string = str_replace('[col8]', '<div class="col-md-8">', $string);
	$string = str_replace('[col9]', '<div class="col-md-9">', $string);
	$string = str_replace('[col10]', '<div class="col-md-10">', $string);
	$string = str_replace('[col11]', '<div class="col-md-11">', $string);
	$string = str_replace('[col12]', '<div class="col-md-12">', $string);
	$string = str_replace('[/col]', '</div>', $string);
	
	$string = str_replace('[spacer]', '<hr class="spacer">', $string);
	$string = str_replace('[shadow]', '<hr class="hr-shadow">', $string);

	
	return $string;
	
}


?>