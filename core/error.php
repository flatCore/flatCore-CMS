<?php

header("HTTP/1.0 404 Not Found");
header("Status: 404 Not Found");


$smarty->assign('title_404', $lang[title_404]);
$smarty->assign('msg_404', $lang[msg_404]);

$smarty->assign('page_title', "404 Page Not Found");

$output = $smarty->fetch("404.tpl");
$smarty->assign('page_content', $output);






?>