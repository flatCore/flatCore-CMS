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


if($_SESSION['acp_system'] != "allowed"){
	$subinc = "no_access";
}



include("./core/$subinc.php");

?>