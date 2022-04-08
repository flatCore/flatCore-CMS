<?php
require __DIR__.'/access.php';


if($sub == '') {
    $sub = 'list';
}

echo '<ul class="nav">';

echo '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=shop&sub=list">'.$icon['file_earmark_post'].' '.$lang['post_list'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "features" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=shop&sub=features">'.$icon['list'].' '.$lang['post_features'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "orders" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=shop&sub=orders">'.$icon['shopping_basket'].' '.$lang['nav_orders'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "edit" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=shop&sub=edit">'.$icon['edit'].' '.$lang['post_new_edit'].'</a></li>';


echo '</ul>';