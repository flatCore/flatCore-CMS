<?php

//prohibit unauthorized access
require("core/access.php");

$sub_active[0] = "submenu";
$sub_active[1] = "submenu";
$sub_active[2] = "submenu";
$sub_active[3] = "submenu";
$sub_active[4] = "submenu";

switch ($sub) {

case "sys_pref":
	$subinc = "system.syspref";
	$sub_active[0] = "submenu_selected";
	break;
	
case "sys_textlib":
	$subinc = "system.textlib";
	$sub_active[1] = "submenu_selected";
	break;
	
case "stats":
	$subinc = "system.stats";
	$sub_active[2] = "submenu_selected";
	break;
	
case "backup":
	$subinc = "system.backup";
	$sub_active[3] = "submenu_selected";
	break;
	
case "update":
	$subinc = "system.update";
	$sub_active[4] = "submenu_selected";
	break;
	
default:
	$subinc = "system.syspref";
	$sub_active[0] = "submenu_selected";
	break;

}


if($_SESSION[acp_system] != "allowed"){
	$subinc = "no_access";
}


echo"<div id='wrapper'> ";

// content block
echo"<div id='contentbox'>";



include("./core/$subinc.php");

echo"</div>"; // eol div contentbox

echo"</div>"; // eol div wrapper


// sub navigation
echo"<div id='subnav'>";
echo"<div id='subnav-inner'>";

echo"<a class='$sub_active[0]' href='$_SERVER[PHP_SELF]?tn=system&sub=sys_pref'>$lang[system_preferences]</a>";
echo"<a class='$sub_active[1]' href='$_SERVER[PHP_SELF]?tn=system&sub=sys_textlib'>$lang[system_textlib]</a>";
echo"<a class='$sub_active[2]' href='$_SERVER[PHP_SELF]?tn=system&sub=stats'>$lang[system_statistics]</a>";
echo"<a class='$sub_active[3]' href='$_SERVER[PHP_SELF]?tn=system&sub=backup'>$lang[system_backup]</a>";
echo"<a class='$sub_active[4]' href='$_SERVER[PHP_SELF]?tn=system&sub=update'>$lang[system_update]</a>";

echo"</div>"; // sub navigation EOL



// liveBox
include("livebox.php");

echo"</div>";



?>