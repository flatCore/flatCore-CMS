<?php
require 'core/access.php';


if($sub == '') {
	$sub = 'list';
}

echo '<ul class="nav">';

echo '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=comments&sub=list">'.$icon['list'].' '.$lang['post_list'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "edit" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=comments&sub=edit">'.$icon['edit'].' '.$lang['post_new_edit'].'</a></li>';


echo '</ul>';

?>