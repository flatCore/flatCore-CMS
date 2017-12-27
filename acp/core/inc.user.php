<?php
//prohibit unauthorized access
require 'core/access.php';



$sub_active[0] = "submenu";
$sub_active[1] = "submenu";
$sub_active[2] = "submenu";
$sub_active[3] = "submenu";
$sub_active[4] = "submenu";

switch ($sub) {

case "list":
	$subinc = "user.list";
	$sub_active[0] = "submenu_selected";
	break;
	
case "edit":
	$subinc = "user.edit";
	$sub_active[1] = "submenu_selected";
	break;
	
case "new":
	$subinc = "user.edit";
	$sub_active[2] = "submenu_selected";
	break;
	
case "customize":
	$subinc = "user.customize";
	$sub_active[3] = "submenu_selected";
	break;
	
case "groups":
	$subinc = "user.groups";
	$sub_active[4] = "submenu_selected";
	break;
	
default:
	$subinc = "user.list";
	$sub_active[0] = "submenu_selected";
	break;

}



if($_SESSION['acp_user'] != "allowed" AND $subinc == "user.edit"){
	$subinc = "no_access";
}

if($_SESSION['acp_user'] != "allowed" AND $subinc == "user.groups"){
	$subinc = "no_access";
}




include './core/'.$subinc.'.php';


?>