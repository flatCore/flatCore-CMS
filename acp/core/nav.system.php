<?php
require 'core/access.php';

if($sub == '') {
	$sub = 'sys_pref';
}

echo '<ul class="nav">';

echo '<li><a class="sidebar-nav '.($sub == "sys_pref" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=sys_pref">'.$icon['cog'].' '.$lang['system_preferences'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "stats" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=stats">'.$icon['chart_bar'].' '.$lang['system_statistics'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "backup" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=backup">'.$icon['download'].' '.$lang['system_backup'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "update" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=update">'.$icon['sync_alt'].' '.$lang['system_update'].'</a></li>';

echo '</ul>';


?>