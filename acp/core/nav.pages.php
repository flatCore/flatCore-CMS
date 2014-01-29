<?php
require("core/access.php");


if($sub == '') {
	$sub = 'list';
}

echo '<a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=list">'.$lang[page_list].'<span class="tri-left"></span></a>';
echo '<a class="sidebar-nav '.($sub == "new" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=new">'.$lang[new_page].'<span class="tri-left"></span></a>';

if($sub == "edit") {
	echo '<a class="sidebar-nav '.($sub == "edit" ? 'sidebar-nav-active' :'').'" href="#">'.$lang[page_edit].'<span class="tri-left"></span></a>';
} else {
	echo "<span class='sidebar-nav sidebar-nav-disabled'>$lang[page_edit]</span>";
}

echo '<a class="sidebar-nav '.($sub == "customize" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=customize">'.$lang[page_customize].'<span class="tri-left"></span></a>';
echo '<a class="sidebar-nav '.($sub == "snippets" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=snippets">'.$lang[snippets].'<span class="tri-left"></span></a>';
echo '<a class="sidebar-nav '.($sub == "rss" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=pages&sub=rss">RSS<span class="tri-left"></span></a>';

?>