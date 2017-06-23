<?php

//prohibit unauthorized access
require("core/access.php");


/**
 * including vars
 * tn -> mainscripts
 * sub -> subscripts
 */

if(!isset($_GET['tn'])){
	$tn = "dashboard";
} else {
	$tn = clean_vars($_GET['tn']);
}

if(!isset($_GET['sub'])){
	$sub = "";
} else {
	$sub = clean_vars($_GET['sub']);
}



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
		$navinc = "nav.dashboard";
		break;
		
	case "pages":
		$active[1] = "topnav_selected";
		$maininc = "inc.pages";
		$navinc = "nav.pages";
		break;
		
	case "moduls":
		$active[2] = "topnav_selected";
		$maininc = "inc.addons";
		$navinc = "nav.addons";
		break;
	
	case "filebrowser":
		$active[3] = "topnav_selected";
		$maininc = "inc.filebrowser";
		$navinc = "nav.filebrowser";
		$headinc = "head.filebrowser.dat";
		break;
		
	case "user":
		$active[4] = "topnav_selected";
		$maininc = "inc.user";
		$navinc = "nav.user";
		break;
		
	case "system":
		$active[5] = "topnav_selected";
		$maininc = "inc.system";
		$navinc = "nav.system";
		break;
	
		
	default:
		$active[0] = "topnav_selected";
		$maininc = "inc.dashboard";
		$navinc = "nav.dashboard";
		break;
	
}



?>