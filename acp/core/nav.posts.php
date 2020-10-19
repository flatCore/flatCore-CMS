<?php
require 'core/access.php';


if($sub == '') {
	$sub = 'list';
}

echo '<ul class="nav">';

echo '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=posts&sub=list">'.$icon['list'].' '.$lang['post_list'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "edit" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=posts&sub=edit">'.$icon['edit'].' '.$lang['post_edit'].'</a></li>';


echo '</ul>';

?>