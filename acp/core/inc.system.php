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
		
	case "mail":
		$subinc = "system.mail";
		break;
		
	case "language":
		$subinc = "system.language";
		break;
		
	case "images":
		$subinc = "system.images";
		break;
		
	case "labels":
		$subinc = "system.labels";
		break;
		
	case "categories":
		$subinc = "system.categories";
		break;
		
	case "customize":
		$subinc = "system.customize";
		break;
		
	case "migrate":
		$subinc = "system.migrate";
		break;
		
	case "posts":
		$subinc = "system.posts";
		break;
		
	case "comments":
		$subinc = "system.comments";
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