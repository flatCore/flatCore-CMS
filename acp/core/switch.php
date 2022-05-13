<?php

//prohibit unauthorized access
require __DIR__."/access.php";

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

    case "pages":
		$active[1] = "topnav_selected";
		$maininc = "inc.pages";
		break;

    case "moduls":
    case "addons":
		$active[2] = "topnav_selected";
		$maininc = "inc.addons";
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

	case "events":
		$active[9] = "topnav_selected";
		$maininc = "inc.events";
		break;

	case "posts":
		$active[6] = "topnav_selected";
		$maininc = "inc.posts";
		break;

    case "shop":
        $active[8] = "topnav_selected";
        $maininc = "inc.shop";
        break;

	case "reactions":
		$active[7] = "topnav_selected";
		$maininc = "inc.reactions";
		break;

    case "dashboard":
    default:
		$active[0] = "topnav_selected";
		$maininc = "inc.dashboard";
		break;
}