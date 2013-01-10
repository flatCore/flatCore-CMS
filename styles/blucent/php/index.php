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
	
	/* some icons */
	$string = str_replace('[icon-ok]', '<i class="icon-ok"></i>', $string);
	$string = str_replace('[icon-home]', '<i class="icon-home"></i>', $string);
	$string = str_replace('[icon-lock]', '<i class="icon-lock"></i>', $string);
	$string = str_replace('[icon-tag]', '<i class="icon-tag"></i>', $string);
	$string = str_replace('[icon-print]', '<i class="icon-print"></i>', $string);
	$string = str_replace('[icon-hdd]', '<i class="icon-hdd"></i>', $string);
	$string = str_replace('[icon-music]', '<i class="icon-music"></i>', $string);
	$string = str_replace('[icon-star]', '<i class="icon-star"></i>', $string);
	$string = str_replace('[icon-star-empty]', '<i class="icon-star-empty"></i>', $string);
	$string = str_replace('[icon-remove]', '<i class="icon-remove"></i>', $string);
	$string = str_replace('[icon-download]', '<i class="icon-download"></i>', $string);
	$string = str_replace('[icon-flag]', '<i class="icon-flag"></i>', $string);
	$string = str_replace('[icon-map-marker]', '<i class="icon-map-marker"></i>', $string);
	$string = str_replace('[icon-info-sign]', '<i class="icon-info-sign"></i>', $string);
	$string = str_replace('[icon-arrow-up]', '<i class="icon-arrow-up"></i>', $string);
	$string = str_replace('[icon-arrow-down]', '<i class="icon-arrow-down"></i>', $string);
	$string = str_replace('[icon-arrow-left]', '<i class="icon-arrow-left"></i>', $string);
	$string = str_replace('[icon-arrow-right]', '<i class="icon-arrow-right"></i>', $string);
	$string = str_replace('[icon-plus]', '<i class="icon-plus"></i>', $string);
	$string = str_replace('[icon-minus]', '<i class="icon-minus"></i>', $string);
	$string = str_replace('[icon-warning-sign]', '<i class="icon-warning-sign"></i>', $string);
	$string = str_replace('[icon-envelope]', '<i class="icon-envelope"></i>', $string);
	$string = str_replace('[icon-user]', '<i class="icon-user"></i>', $string);
	$string = str_replace('[icon-bookmark]', '<i class="icon-bookmark"></i>', $string);
	$string = str_replace('[icon-chevron-left]', '<i class="icon-chevron-left"></i>', $string);
	$string = str_replace('[icon-chevron-right]', '<i class="icon-chevron-right"></i>', $string);
	$string = str_replace('[icon-shopping-cart]', '<i class="icon-shopping-cart"></i>', $string);
	
	
	/* custom blucent shortcodes */
	
	$string = str_replace('[darkbox]', '<div class="darkBox">', $string);
	$string = str_replace('[/darkbox]', '</div>', $string);
	
	$string = str_replace('[brightbox]', '<div class="brightBox">', $string);
	$string = str_replace('[/brightbox]', '</div>', $string);
	
	return $string;
	
}
?>