<?php
require 'core/access.php';


if($sub == '') {
	$sub = 'list';
}

echo '<ul class="nav">';

echo '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=posts&sub=list">'.$icon['file_earmark_post'].' '.$lang['post_list'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "features" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=posts&sub=features">'.$icon['list'].' '.$lang['post_features'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "edit" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=posts&sub=edit">'.$icon['edit'].' '.$lang['post_new_edit'].'</a></li>';


echo '</ul>';

?>