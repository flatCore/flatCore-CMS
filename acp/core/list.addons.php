<?php

//prohibit unauthorized access
require 'core/access.php';

/* check in a new module */
if(isset($_GET['enable'])) {
	
	$modFolder = basename($_GET['enable']);
	include '../modules/'.$modFolder.'/info.inc.php';
	
	$db_content->insert("fc_addons", [
		"addon_type" => "module",
		"addon_dir" => $_GET['enable'],
		"addon_name" => $mod['name'],
		"addon_version" => $mod['version']
	]);
	
	
	mods_check_in();
}

/* check out an existing module */
if(isset($_GET['disable'])) {
	
	$db_content->delete("fc_addons", [
		"AND" => [
			"addon_dir" => $_GET['disable']
		]
	]);
	
	mods_check_in();
}

//$arr_iMods
$fc_addons = fc_get_addons($t='module');

$template_file = file_get_contents("templates/modlist.tpl");
$modal_template_file = file_get_contents("templates/bs-modal.tpl");

if($nbrModuls < 1) {
	echo '<div class="alert alert-info">'.$lang['alert_no_modules'].'</div>';
}

for($i=0;$i<$nbrModuls;$i++) {

	unset($listlinks, $modnav);
	
	$modFolder = $all_mods[$i]['folder'];
	$bnt_check_in_out = '<a class="btn btn-sm btn-fc text-success" href="acp.php?tn=moduls&enable='.$modFolder.'">'.$lang['btn_mod_enable'].'</a>';
		
	foreach($fc_addons as $a) {
		if(in_array($modFolder, $a)) {
			$bnt_check_in_out = '<a class="btn btn-sm btn-fc text-danger" href="acp.php?tn=moduls&disable='.$modFolder.'">'.$lang['btn_mod_disable'].'</a>';
		}
	}
		
	include '../modules/'.$modFolder.'/info.inc.php';
	
	$listlinks = '<div class="btn-group">';
	for($x=0;$x<count($modnav);$x++) {
		$showlink = $modnav[$x]['link'];
		$incpage = $modnav[$x]['file'];
		$listlinks .= "<a class='btn btn-sm btn-fc' href='acp.php?tn=moduls&sub=$modFolder&a=$incpage'>$showlink</a> ";
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
	
	$poster_img = '';
	if(is_file("../modules/$modFolder/backend/poster.jpg")) {
		$poster_img = '<a href="acp.php?tn=moduls&sub='.$modFolder.'&a=start"><img src="../modules/'.$modFolder.'/backend/poster.jpg" class="card-img-top"></a>';
	} else {
		$poster_img = '<a href="acp.php?tn=moduls&sub='.$modFolder.'&a=start"><img src="images/poster-addons.jpg" class="card-img-top"></a>';
	}
	
	
	
	$btn_help_text = '';
	$modal = '';
	if(is_file('../modules/'.$modFolder.'/readme.md')) {
		$addon_id = 'addonID'.$i;
		$btn_help_text = '<button type="button" class="btn btn-sm btn-fc" data-toggle="modal" data-target="#'.$addon_id.'">'.$icon['question'].'</button>';
		
		$modal_body_text = file_get_contents('../modules/'.$modFolder.'/readme.md');
		$Parsedown = new Parsedown();
		$modal_body = $Parsedown->text($modal_body_text);
		
		$modal = $modal_template_file;
		$modal = str_replace('{modalID}', $addon_id, $modal);
		$modal = str_replace('{modalTitle}', $mod['name'], $modal);
		$modal = str_replace('{modalBody}', $modal_body, $modal);
		echo $modal;
	}
	
	
	
	$tpl = $template_file;
	
	$tpl = str_replace("{\$MOD_NAME}", "$mod[name]","$template_file"); 
	$tpl = str_replace("{\$MOD_DESCRIPTION}", "$mod[description]","$tpl");
	$tpl = str_replace("{\$MOD_VERSION}", "$mod[version]","$tpl");
	$tpl = str_replace("{\$MOD_AUTHOR}", "$mod[author]","$tpl");
	$tpl = str_replace("{\$MOD_ICON}", "$poster_img","$tpl");
	$tpl = str_replace("{\$MOD_LIVECODE}", "$mod_livecode","$tpl");
	$tpl = str_replace("{\$MOD_CHECK_IN_OUT}", "$bnt_check_in_out","$tpl");
	$tpl = str_replace("{\$MOD_README}", "$btn_help_text","$tpl");
	
	$tpl = str_replace("{\$MOD_NAV}", "$listlinks","$tpl");
	
	echo $tpl;

}


?>