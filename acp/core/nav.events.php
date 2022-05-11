<?php
require 'core/access.php';


if($sub == '') {
    $sub = 'list';
}

echo '<ul class="nav">';

echo '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=events&sub=list">'.$icon['file_earmark_post'].' '.$lang['post_list'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "edit" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=events&sub=edit">'.$icon['edit'].' '.$lang['post_new_edit'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "bookings" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=events&sub=bookings">'.$icon['calendar_check'].' '.$lang['btn_bookings'].'</a></li>';

echo '</ul>';

?>