<?php

//prohibit unauthorized access
require("core/access.php");


$active[0] = "topnav";
$active[1] = "topnav";
$active[2] = "topnav";
$active[3] = "topnav";
$active[4] = "topnav";
$active[5] = "topnav";

switch ($tn) {

case "dashboard":
	$active[0] = "topnav_selected";
	$maininc = "inc.dashboard";
	break;
	
case "pages":
	$active[1] = "topnav_selected";
	$maininc = "inc.pages";
	break;
	
case "moduls":
	$active[2] = "topnav_selected";
	$maininc = "inc.moduls";
	break;

case "filebrowser":
	$active[3] = "topnav_selected";
	$maininc = "inc.filebrowser";
	$headinc = "head.filebrowser.dat";
	break;
	
case "user":
	$active[4] = "topnav_selected";
	$maininc = "inc.user";
	break;
	
case "system":
	$active[5] = "topnav_selected";
	$maininc = "inc.system";
	break;

	
default:
	$active[0] = "topnav_selected";
	$maininc = "inc.dashboard";
	break;
	
}

echo'<div id="topNavBox">';

echo"<a class='$active[0] styledTip' href='acp.php?tn=dashboard' title='$lang[tn_dashboard_desc]'><img src='images/mm-dashboard.png'> $lang[tn_dashboard]</a>";

echo"<a class='$active[1] styledTip' href='acp.php?tn=pages' title='$lang[tn_pages_desc]'><img src='images/mm-pages.png'> $lang[tn_pages]</a>";
echo"<a class='$active[2] styledTip' href='acp.php?tn=moduls' title='$lang[tn_moduls_desc]'><img src='images/mm-modules.png'> $lang[tn_moduls]</a>";
echo"<a class='$active[3] styledTip' href='acp.php?tn=filebrowser' title='$lang[tn_filebrowser_desc]'><img src='images/mm-files.png'> $lang[tn_filebrowser]</a>";
echo"<a class='$active[4] styledTip' href='acp.php?tn=user' title='$lang[tn_usermanagement_desc]'><img src='images/mm-user.png'> $lang[tn_usermanagement]</a>";
echo"<a class='$active[5] styledTip' href='acp.php?tn=system' title='$lang[tn_system_desc]'><img src='images/mm-system.png'> $lang[tn_system]</a>";


echo'</div>';




?>