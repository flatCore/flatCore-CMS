<?php
require("core/access.php");

if($sub == '') {
	$sub = 'list';
}

echo '<a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=user&sub=list">'.$lang['list_user'].'<span class="tri-left"></span></a>';
echo '<a class="sidebar-nav '.($sub == "new" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=user&sub=new">'.$lang['new_user'].'<span class="tri-left"></span></a>';


if($sub == "edit") {
	echo '<a class="sidebar-nav '.($sub == "edit" ? 'sidebar-nav-active' :'').'" href="#">'.$lang[edit_user].'<span class="tri-left"></span></a>';
} else {
	echo '<span class="sidebar-nav sidebar-nav-disabled">'.$lang['edit_user'].'</span>';
}

echo '<a class="sidebar-nav '.($sub == "customize" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=user&sub=customize">'.$lang['customize_user'].'<span class="tri-left"></span></a>';
echo '<a class="sidebar-nav '.($sub == "groups" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=user&sub=groups">'.$lang['edit_groups'].'<span class="tri-left"></span></a>';


?>