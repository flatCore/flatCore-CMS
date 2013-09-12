<?php

//prohibit unauthorized access
require("core/access.php");


$template_file = file_get_contents("templates/modlist.tpl");



for($i=0;$i<$nbrModuls;$i++) {

	unset($listlinks, $modnav);
	
	$modFolder = $arr_iMods[$i][folder];
	
	include("../modules/$modFolder/info.inc.php");
	
	$listlinks = '<div class="btn-group">';
	for($x=0;$x<count($modnav);$x++) {
		$showlink = $modnav[$x][link];
		$incpage = $modnav[$x][file];
		$listlinks .= "<a class='btn btn-small' href='$_SERVER[PHP_SELF]?tn=moduls&sub=$modFolder&a=$incpage'>$showlink</a> ";
	}
	
	$listlinks .= '</div>';
	
	
	if(!is_file("../modules/$modFolder/icon.png")) {
		$tpl_icon = "images/modul-icon.png";
	} else {
		$tpl_icon = "../modules/$modFolder/icon.png";
	}
	
	unset($mod_livecode);
	if(is_file("../modules/$modFolder/backend/livecode.php")) {
		include("../modules/$modFolder/backend/livecode.php");
	}
	
	
	
	$tpl = $template_file;
	
	$tpl = str_replace("{\$MOD_NAME}", "$mod[name]","$template_file"); 
	$tpl = str_replace("{\$MOD_DESCRIPTION}", "$mod[description]","$tpl");
	$tpl = str_replace("{\$MOD_VERSION}", "$mod[version]","$tpl");
	$tpl = str_replace("{\$MOD_AUTHOR}", "$mod[author]","$tpl");
	$tpl = str_replace("{\$MOD_ICON}", "$tpl_icon","$tpl");
	$tpl = str_replace("{\$MOD_LIVECODE}", "$mod_livecode","$tpl");
	
	$tpl = str_replace("{\$MOD_NAV}", "$listlinks","$tpl");
	
	echo $tpl;

}


?>