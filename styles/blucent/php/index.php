<?php

/**
 * custom theme functions
 *
 * @package		styles/blucent
 * @author		Patrick Konstandin <support@flatfiler.de>
 *
 */



$page_content = theme_replacer($page_content);
$textlib_footer = theme_replacer($textlib_footer);


function theme_replacer($string) {
	$string = str_replace('<p>[', '[', $string);
	$string = str_replace(']</p>', ']', $string);

	/* twitter bootstrap */	
	
	$string = str_replace('[row-fluid]', '<div class="row-fluid">', $string);
	$string = str_replace('[/row-fluid]', '</div>', $string);
	
	$string = str_replace('[span1]', '<div class="span1">', $string);
	$string = str_replace('[span2]', '<div class="span2">', $string);
	$string = str_replace('[span3]', '<div class="span3">', $string);
	$string = str_replace('[span4]', '<div class="span4">', $string);
	$string = str_replace('[span5]', '<div class="span5">', $string);
	$string = str_replace('[span6]', '<div class="span6">', $string);
	$string = str_replace('[span7]', '<div class="span7">', $string);
	$string = str_replace('[span8]', '<div class="span8">', $string);
	$string = str_replace('[span9]', '<div class="span9">', $string);
	$string = str_replace('[span10]', '<div class="span10">', $string);
	$string = str_replace('[span11]', '<div class="span11">', $string);
	$string = str_replace('[span12]', '<div class="span12">', $string);
	$string = str_replace('[/span]', '</div>', $string);
	
	$string = str_replace('[spacer]', '<hr class="spacer">', $string);
	
	/* custom blucent shortcodes */
	
	$string = str_replace('[darkbox]', '<div class="darkBox">', $string);
	$string = str_replace('[/darkbox]', '</div>', $string);
	
	$string = str_replace('[brightbox]', '<div class="brightBox">', $string);
	$string = str_replace('[/brightbox]', '</div>', $string);
	
	return $string;
	
}
?>