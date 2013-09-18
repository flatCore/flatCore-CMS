<?php

//prohibit unauthorized access
require("core/access.php");

$a = '';
if(isset($_GET['a'])) {
	$a = strip_tags($_GET['a']);
}


$arr_iMods = get_all_moduls();
$nbrModuls = count($arr_iMods);

$mod_subnav = "<a class='submenu_selected' href='$_SERVER[PHP_SELF]?tn=moduls&sub=list'>$lang[tn_moduls]</a>";


for($i=0;$i<$nbrModuls;$i++) {

	$modFolder = $arr_iMods[$i]['folder'];
	
	unset($modnav);
	
	include("../modules/$modFolder/info.inc.php");
	
	$mod_subnav .= "<a class='submenu' href='$_SERVER[PHP_SELF]?tn=moduls&sub=$modFolder&a=start'>$mod[name]</a>";
	
	
	//Show submenue of the current modul
	if($sub == "$modFolder") {
	
		for($x=0;$x<count($modnav);$x++) {
			$showlink = $modnav[$x]['link'];
			$incpage = $modnav[$x]['file'];
		
			if($a == $incpage) {
				$sub_link_class = "submenu_mods_selected";
			} else {
				$sub_link_class = "submenu_mods";
			}
		
			$mod_subnav .= "<a class='$sub_link_class' href='$_SERVER[PHP_SELF]?tn=moduls&sub=$modFolder&a=$incpage'>$showlink</a>";
		} // eo $x
		
		
		/**
		 * get preferences from
		 * info.inc.php
		 */
		
		$mod_name = "$mod[name]";
		$mod_version = "$mod[version]";
		$mod_db = "../$mod[database]";
	
	}
	

} // eol i





/* output */

echo"<div id='wrapper'> ";
echo"<div id='contentbox'>";

//include("$subinc.php");


if($_GET[a] == "") {
	include("mods.list.php");
} else {

	unset($mod);
	include("../modules/$sub/info.inc.php");
	include("../modules/$sub/backend/$a.php");

}

echo"</div>"; // eol div contenbox

echo"</div>"; // eol div wrapper





echo'<div id="subnav">';

// sub navigation
echo'<div id="subnav-inner">';
echo"$mod_subnav";
echo'</div>'; // sub navigation EOL

// liveBox
include("livebox.php");

echo'</div>';

?>