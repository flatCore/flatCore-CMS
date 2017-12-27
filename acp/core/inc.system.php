<?php

//prohibit unauthorized access
require 'core/access.php';

switch ($sub) {

	case "sys_pref":
		$subinc = "system.syspref";
		break;
		
	case "sys_textlib":
		$subinc = "system.textlib";
		break;
		
	case "design":
		$subinc = "system.design";
		break;
		
	case "stats":
		$subinc = "system.stats";
		break;
		
	case "backup":
		$subinc = "system.backup";
		break;
		
	case "update":
		$subinc = "system.update";
		break;
		
	default:
		$subinc = "system.syspref";
		break;

}


if($_SESSION['acp_system'] != "allowed"){
	$subinc = "no_access";
}



include './core/'.$subinc.'.php';

?>