<?php

/**
 * create the navigation,
 * submenu and breadcrumb menu
 */
 
$dbh = new PDO("sqlite:$fc_db_content");

$sql = "SELECT page_id, page_language, page_linkname, page_permalink, page_title, page_sort, page_status
			  FROM fc_pages
			  WHERE page_status != 'draft' AND page_language = '$languagePack'
			  ORDER BY page_sort";

unset($result);  
$result = $dbh->query($sql)->fetchAll();

$dbh = null;


$menu = array();
$submenu = array();

$menu = show_mainmenu();
$submenu = show_menu($current_page_sort);
$bcmenu = breadcrumbs_menu($current_page_sort);
$fc_sitemap = show_sitemap();

if(is_array($submenu)) {
	foreach($submenu as $line) {
 		$sort_array[] = $line['page_sort'];
	}
	array_multisort($sort_array , $submenu);
}


$arr_mainmenu = @array_values($menu);
$arr_subnmenu = @array_values($submenu);

$smarty->assign('link_home', FC_INC_DIR . "/");
$smarty->assign('arr_menue', $arr_mainmenu);
$smarty->assign('arr_bcmenue', $bcmenu);
$smarty->assign('fc_sitemap', $fc_sitemap);

/**
 * send submenu
 * only if $submenu != empty
 */
 
if(count($submenu) >= 1) {

	$smarty->assign('arr_submenue', $arr_subnmenu);
	$smarty->assign('legend_toc', FC_TOC_HEADER);
	$output = $smarty->fetch("toc.tpl");
	$smarty->assign('toc_submenu', $output);

}


?>