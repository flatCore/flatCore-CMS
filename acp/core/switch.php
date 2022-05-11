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
$active[6] = "topnav";
$active[7] = "topnav";
$active[8] = "topnav";
$active[9] = "topnav";

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

	case "addons":
		$active[2] = "topnav_selected";
		$maininc = "inc.addons";
		$navinc = "nav.addons";
		break;
	/* we remove this soon */
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

	case "events":
		$active[9] = "topnav_selected";
		$maininc = "inc.events";
		$navinc = "nav.events";
		break;

	case "posts":
		$active[6] = "topnav_selected";
		$maininc = "inc.posts";
		$navinc = "nav.posts";
		break;

    case "shop":
        $active[8] = "topnav_selected";
        $maininc = "inc.shop";
        $navinc = "nav.shop";
        break;

	case "reactions":
		$active[7] = "topnav_selected";
		$maininc = "inc.reactions";
		$navinc = "nav.reactions";
		break;	
		
	default:
		$active[0] = "topnav_selected";
		$maininc = "inc.dashboard";
		$navinc = "nav.dashboard";
		break;
	
}



?>