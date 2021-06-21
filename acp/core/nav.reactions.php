<?php
require 'core/access.php';


if($sub == '') {
	$sub = 'comments';
}

echo '<ul class="nav">';

echo '<li><a class="sidebar-nav '.($sub == "comments" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=reactions&sub=comments">'.$icon['comments'].' '.$lang['reactions_comments'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "votings" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=reactions&sub=votings">'.$icon['thumbs_up'].' '.$lang['reactions_votings'].'</a></li>';
echo '<li><a class="sidebar-nav '.($sub == "events" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=reactions&sub=events">'.$icon['calendar_check'].' '.$lang['reactions_events'].'</a></li>';

echo '</ul>';

?>