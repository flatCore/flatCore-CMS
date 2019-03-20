<?php

//prohibit unauthorized access
require("core/access.php");

/* check in a new module */
if(isset($_GET['enable'])) {
	
	$modFolder = basename($_GET['enable']);
	include '../modules/'.$modFolder.'/info.inc.php';
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "INSERT INTO fc_addons	(
			addon_id , addon_type, addon_dir , addon_name , addon_version
			) VALUES (
			NULL, :addon_type, :addon_dir, :addon_name, :addon_version ) ";
			
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':addon_dir', $_GET['enable'], PDO::PARAM_STR);
	$sth->bindParam(':addon_name', $mod['name'], PDO::PARAM_STR);
	$sth->bindParam(':addon_version', $mod['version'], PDO::PARAM_STR);
	$sth->bindValue(':addon_type', "module", PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	$dbh = null;
	
	mods_check_in();
}

/* check out an existing module */
if(isset($_GET['disable'])) {
				
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "DELETE FROM fc_addons WHERE addon_dir = :disable";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':disable', $_GET['disable'], PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	$dbh = null;
	
	mods_check_in();
}

//$arr_iMods
$fc_addons = fc_get_addons($t='module');

$template_file = file_get_contents("templates/modlist.tpl");

if($nbrModuls < 1) {
	echo '<div class="alert alert-info">'.$lang['alert_no_modules'].'</div>';
}

for($i=0;$i<$nbrModuls;$i++) {

	unset($listlinks, $modnav);
	
	$modFolder = $arr_iMods[$i]['folder'];
	$bnt_check_in_out = '<a class="btn btn-sm btn-dark text-success" href="acp.php?tn=moduls&enable='.$modFolder.'">'.$lang['btn_mod_enable'].'</a>';
	
	foreach($fc_addons as $a) {
		if(in_array($modFolder, $a)) {
			$bnt_check_in_out = '<a class="btn btn-sm btn-dark text-danger" href="acp.php?tn=moduls&disable='.$modFolder.'">'.$lang['btn_mod_disable'].'</a>';
		}
	}
	
	
	include '../modules/'.$modFolder.'/info.inc.php';
	
	$listlinks = '<div class="btn-group">';
	for($x=0;$x<count($modnav);$x++) {
		$showlink = $modnav[$x]['link'];
		$incpage = $modnav[$x]['file'];
		$listlinks .= "<a class='btn btn-sm btn-dark' href='acp.php?tn=moduls&sub=$modFolder&a=$incpage'>$showlink</a> ";
	}
	
	$listlinks .= '</div>';
	
	
	if(!is_file("../modules/$modFolder/icon.png")) {
		$tpl_icon = "images/modul-icon.png";
	} else {
		$tpl_icon = "../modules/$modFolder/icon.png";
	}
	
	unset($mod_livecode);
	if(is_file("../modules/$modFolder/backend/livecode.php")) {
		include '../modules/'.$modFolder.'/backend/livecode.php';
	}
	
	
	
	$tpl = $template_file;
	
	$tpl = str_replace("{\$MOD_NAME}", "$mod[name]","$template_file"); 
	$tpl = str_replace("{\$MOD_DESCRIPTION}", "$mod[description]","$tpl");
	$tpl = str_replace("{\$MOD_VERSION}", "$mod[version]","$tpl");
	$tpl = str_replace("{\$MOD_AUTHOR}", "$mod[author]","$tpl");
	$tpl = str_replace("{\$MOD_ICON}", "$tpl_icon","$tpl");
	$tpl = str_replace("{\$MOD_LIVECODE}", "$mod_livecode","$tpl");
	$tpl = str_replace("{\$MOD_CHECK_IN_OUT}", "$bnt_check_in_out","$tpl");
	
	
	$tpl = str_replace("{\$MOD_NAV}", "$listlinks","$tpl");
	
	echo $tpl;

}


?>