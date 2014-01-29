<?php
require("core/access.php");

if($sub == '') {
	$sub = 'browse';
}

echo '<a class="sidebar-nav '.($sub == "browse" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=filebrowser&sub=browse">'.$lang['manage_files'].'<span class="tri-left"></span></a>';
echo '<a class="sidebar-nav '.($sub == "upload" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=filebrowser&sub=upload">'.$lang['go_to_upload'].'<span class="tri-left"></span></a>';


?>