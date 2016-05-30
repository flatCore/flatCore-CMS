<?php
require("core/access.php");

if($sub == '') {
	$sub = 'sys_pref';
}

echo '<a class="sidebar-nav '.($sub == "sys_pref" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=sys_pref">'.$lang['system_preferences'].'<span class="tri-left"></span></a>';
echo '<a class="sidebar-nav '.($sub == "stats" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=stats">'.$lang['system_statistics'].'<span class="tri-left"></span></a>';
echo '<a class="sidebar-nav '.($sub == "backup" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=backup">'.$lang['system_backup'].'<span class="tri-left"></span></a>';
echo '<a class="sidebar-nav '.($sub == "update" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=system&sub=update">'.$lang['system_update'].'<span class="tri-left"></span></a>';

?>