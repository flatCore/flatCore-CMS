<?php
require 'core/access.php';

if($sub == '') {
	$sub = 'sys_pref';
}

echo '<ul class="nav">';
echo '<li><a class="sidebar-nav '.($sub == "sys_pref" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=sys_pref">'.$icon['cog'].' '.$lang['system_preferences'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "mail" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=mail">'.$icon['at'].' '.$lang['system_mail'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "language" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=language">'.$icon['language'].' '.$lang['system_language'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "images" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=images">'.$icon['images'].' '.$lang['system_images'].'</a></li>';

echo '<li class="mt-2"><a class="sidebar-nav '.($sub == "labels" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=labels">'.$icon['tags'].' '.$lang['labels'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "categories" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=categories">'.$icon['bookmark'].' '.$lang['categories'].'</a></li>';

echo '<li class="mt-2"><a class="sidebar-nav '.($sub == "posts" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=posts">'.$icon['pencil_ruler'].' '.$lang['tn_posts'].'</a></li>';
echo '<li class=""><a class="sidebar-nav '.($sub == "comments" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=comments">'.$icon['comments'].' '.$lang['tn_comments'].'</a></li>';

echo '<li class="mt-2"><a class="sidebar-nav '.($sub == "customize" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=customize">'.$icon['table'].' '.$lang['customize_database'].'</a></li>';

echo '<li class="mt-2"><a class="sidebar-nav '.($sub == "stats" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=stats">'.$icon['chart_bar'].' '.$lang['system_statistics'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "backup" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=backup">'.$icon['download'].' '.$lang['system_backup'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "update" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=update">'.$icon['sync_alt'].' '.$lang['system_update'].'</a></li>';
echo '</ul>';


?>