<?php
require("core/access.php");

if($sub == '') {
	$sub = 'list';
}

echo '<ul class="nav">';

echo '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=user&sub=list">'.$icon['users'].' '.$lang['list_user'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "new" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=user&sub=new">'.$icon['user_plus'].' '.$lang['new_user'].'</a></li>';


if($sub == "edit") {
	echo '<li><a class="sidebar-nav '.($sub == "edit" ? 'sidebar-nav-active' :'').'" href="#">'.$icon['user_edit'].' '.$lang['edit_user'].'</a></li>';
} else {
	echo '<li><a href="#"><span class="sidebar-nav sidebar-nav-disabled">'.$icon['user_edit'].' '.$lang['edit_user'].'</span></a></li>';
}

echo '<li><a class="sidebar-nav '.($sub == "customize" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=user&sub=customize">'.$icon['user_cog'].' '.$lang['customize_user'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "groups" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=user&sub=groups">'.$icon['user_friends'].' '.$lang['edit_groups'].'</a></li>';

echo '</ul>';


?>