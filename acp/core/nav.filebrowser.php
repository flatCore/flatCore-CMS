<?php
require("core/access.php");

if($sub == '') {
	$sub = 'browse';
}

echo '<a class="sidebar-nav '.($sub == "browse" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=filebrowser&sub=browse">'.$lang['manage_files'].'<span class="tri-left"></span></a>';
echo '<a class="sidebar-nav" data-toggle="modal" data-target="#uploadModal" href="#">'.$lang['go_to_upload'].'</a>';
?>