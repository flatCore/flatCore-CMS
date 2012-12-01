<?php

//prohibit unauthorized access
require("core/access.php");

$sub_active[0] = "submenu";
$sub_active[1] = "submenu";
$sub_active[2] = "submenu";

switch ($sub) {

case "browse":
	$subinc = "files.browser";
	$sub_active[0] = "submenu_selected";
	break;

case "upload":
	$subinc = "files.upload";
	$sub_active[1] = "submenu_selected";
	break;

default:
	$subinc = "files.browser";
	$sub_active[0] = "submenu_selected";
	break;

}




echo"<div id='wrapper'> ";
echo"<div id='contentbox'> ";


include("$subinc.php");



echo"</div>"; // eol div contenbox

echo"</div>"; // eol div wrapper


echo"<div id='subnav'>";
// sub navigation
echo"<div id='subnav-inner'>";


echo"<a class='$sub_active[0]' href='$_SERVER[PHP_SELF]?tn=filebrowser&sub=browse'>$lang[manage_files]</a>";
echo"<a class='$sub_active[1]' href='$_SERVER[PHP_SELF]?tn=filebrowser&sub=upload'>$lang[go_to_upload]</a>";



echo"</div>"; // sub navigation EOL

// liveBox
include("livebox.php");
echo"</div>";
?>