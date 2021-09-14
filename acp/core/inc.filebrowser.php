<?php

//prohibit unauthorized access
require 'core/access.php';

$sub_active[0] = "submenu";
$sub_active[1] = "submenu";
$sub_active[2] = "submenu";

switch ($sub) {

case "browse":
	$subinc = "files.browser";
	$sub_active[0] = "submenu_selected";
	break;
	
case "edit":
	$subinc = "files.edit";
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


include $subinc.'.php';

?>