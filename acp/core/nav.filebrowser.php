<?php
require("core/access.php");

if($sub == '') {
	$sub = 'browse';
}

echo '<ul class="nav">';

echo '<li><a class="sidebar-nav '.($sub == "browse" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=filebrowser&sub=browse">'.$icon['folder_open'].' '.$lang['manage_files'].'</a></li>';
echo '<li><a class="sidebar-nav" data-toggle="modal" data-target="#uploadModal" href="#">'.$icon['upload'].' '.$lang['go_to_upload'].'</a></li>';

echo '</ul>';

?>