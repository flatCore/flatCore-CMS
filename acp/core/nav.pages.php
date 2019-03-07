<?php
require 'core/access.php';


if($sub == '') {
	$sub = 'list';
}

echo '<ul class="nav">';

echo '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=list">'.$icon['sitemap'].' '.$lang['page_list'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "new" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=new">'.$icon['plus'].' '.$lang['new_page'].'</a></li>';

if($sub == "edit") {
	echo '<li><a class="sidebar-nav '.($sub == "edit" ? 'sidebar-nav-active' :'').'" href="#">'.$icon['edit'].' '.$lang['page_edit'].'</a></li>';
} else {
	echo '<li><a class="sidebar-nav sidebar-nav-disabled" href="#">'.$icon['edit'].' '.$lang['page_edit'].'</a></li>';
}

echo '<li><a class="sidebar-nav '.($sub == "customize" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=customize">'.$icon['cogs'].' '.$lang['page_customize'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "snippets" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=snippets">'.$icon['clipboard'].' '.$lang['snippets'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "rss" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=rss">'.$icon['rss'].' RSS</a></li>';

echo '</ul>';

?>