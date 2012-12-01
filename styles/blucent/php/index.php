<?php

/**
 * custom theme functions
 *
 * @package		styles/blucent
 * @author		Patrick Konstandin <support@flatfiler.de>
 *
 */




/**
 * some shortcodes for twitter's fluid grid
 */


$page_content = str_replace('<p>[', '[', $page_content);
$page_content = str_replace(']</p>', ']', $page_content);

$page_content = str_replace('[row-fluid]', '<div class="row-fluid">', $page_content);
$page_content = str_replace('[/row-fluid]', '</div>', $page_content);


$page_content = preg_replace("/\[span(.*)\](.*)\[\/span\]/Usi", "<div class=\"span\\1\">\\2</div>", $page_content);


?>