<?php
require 'core/access.php';


$a = '';
if(isset($_GET['a'])) {
	$a = strip_tags($_GET['a']);
}

if($sub == '') {
	$sub = 'list';
}


$nbrModuls = count($all_mods);

echo '<ul class="nav">';

$mod_subnav = '<li><a class="sidebar-nav '.($sub == "list" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=moduls&sub=list">'.$icon['plugin'].' '.$lang['tn_moduls'].'</a></li>';

for($i=0;$i<$nbrModuls;$i++) {

	$modFolder = $all_mods[$i]['folder'];
	
	unset($modnav);	
	include '../modules/'.$modFolder.'/info.inc.php';
	$cnt_modnav = count($modnav);

	$mod_subnav .= '<li><a class="sidebar-nav '.($sub == "$modFolder" ? 'sidebar-nav-active' :'').'" href="acp.php?tn=moduls&sub='.$modFolder.'&a=start">'.$icon['caret_right_fill'].' '.$mod['name'].'</a></li>';
	
	//Show submenue of the current modul
	if($sub == "$modFolder") {
	
		for($x=0;$x<$cnt_modnav;$x++) {
			$showlink = $modnav[$x]['link'];
			$incpage = $modnav[$x]['file'];
		
			if($a == $incpage) {
				$sub_link_class = "sidebar-sub-active";
			} else {
				$sub_link_class = "sidebar-sub";
			}
		
			$mod_subnav .= '<li><a class="'.$sub_link_class.'" href="acp.php?tn=moduls&sub='.$modFolder.'&a='.$incpage.'">'.$icon['caret_right'].' '.$showlink.'</a></li>';
			if($x==($cnt_modnav-1)) {
				$mod_subnav .= '<li class="sidebar-sub-end"></li>';
			}
		} // eo $x
		
		
		
		
		/**
		 * get preferences from
		 * info.inc.php
		 */
		
		$mod_name = $mod['name'];
		$mod_version = $mod['version'];
		$mod_db = '../'.$mod['database'];
	
	}
	

} // eol i


echo $mod_subnav;

echo '</ul>';

?>