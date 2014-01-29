<?php
require("core/access.php");


$a = '';
if(isset($_GET['a'])) {
	$a = strip_tags($_GET['a']);
}

if($sub == '') {
	$sub = 'list';
}


$arr_iMods = get_all_moduls();
$nbrModuls = count($arr_iMods);


$mod_subnav = '<a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=moduls&sub=list">'.$lang['tn_moduls'].'<span class="tri-left"></span></a>';

for($i=0;$i<$nbrModuls;$i++) {

	$modFolder = $arr_iMods[$i]['folder'];
	
	unset($modnav);
	
	include("../modules/$modFolder/info.inc.php");

	$mod_subnav .= '<a class="sidebar-nav '.($sub == "$modFolder" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=moduls&sub='.$modFolder.'&a=start">'.$mod['name'].'<span class="tri-left"></span></a>';
	
	//Show submenue of the current modul
	if($sub == "$modFolder") {
	
		for($x=0;$x<count($modnav);$x++) {
			$showlink = $modnav[$x]['link'];
			$incpage = $modnav[$x]['file'];
		
			if($a == $incpage) {
				$sub_link_class = "sidebar-sub-active";
			} else {
				$sub_link_class = "sidebar-sub";
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


echo $mod_subnav;

?>