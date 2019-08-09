<?php
/**
 * prohibit unauthorized access
 */
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){ 
	die ('<h2>Direct File Access Prohibited</h2>');
}


/**
 * get all installed Plugins
 * return as array
 */

function get_all_plugins() {
	
	$dir = "../content/";
	$plugins = array();
	$scanned_directory = array_diff(scandir('../'.FC_CONTENT_DIR.'/plugins/'), array('..', '.','.DS_Store'));
	foreach($scanned_directory as $p) {
		
		$path_parts = pathinfo($p);
		if($path_parts['extension'] != 'php') {
			continue;
		} else {
			$plugins[] = $p;
		}
		
	}
	return $plugins;
}



/**
 * get all installed Moduls
 * return as array
 */

function get_all_moduls() {

	$mdir = "../modules";
	$cntMods = 0;
	$scanned_directory = array_diff(scandir($mdir), array('..', '.','.DS_Store'));
		
	foreach($scanned_directory as $mod_folder) {
		if(is_file("$mdir/$mod_folder/info.inc.php")) {
			include $mdir.'/'.$mod_folder.'/info.inc.php';
			$arr_iMods[$cntMods]['name'] = $mod['name'];
			$arr_iMods[$cntMods]['folder'] = $mod_folder;
			$cntMods++;		
		}
	}

	return($arr_iMods);
}

/**
 * get all addons stored in table fc_addons
 * type = theme | module
 */
 
function fc_get_addons($t='module') {
	
	$result = array();
	
	if($t == 'module') {
		$type = 'module';
	} else {
		$type = 'theme';
	}
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "SELECT * FROM fc_addons WHERE addon_type = '$type' ";

	foreach ($dbh->query($sql) as $row) {
		$result[] = $row;
	}
	
	$dbh = null;
	
	return($result);
		
}



/**
 * show all installed templates
 * return as array
 */

function get_all_templates() {

	//templates folder
	$sdir = "../styles";
	$cntStyles = 0;
	$scanned_directory = array_diff(scandir($sdir), array('..', '.','.DS_Store'));
	
	foreach($scanned_directory as $tpl_folder) {
		if(is_dir("$sdir/$tpl_folder")) {
			$arr_Styles[] = "$tpl_folder";
		}	
	}

	return($arr_Styles);
}



/**
 * check in active modules
 * generate array from pages containing a module
 * and from addon_dir -> content.sqlite3
 * store in ... cache/active_mods.php
 */

function mods_check_in() {
	
	$pages = array();
	$mods = array();
	$m = array();

	$dbh = new PDO("sqlite:".CONTENT_DB);
	
	$sql_get_mods = "SELECT addon_dir FROM fc_addons WHERE addon_type = 'module'";
	foreach ($dbh->query($sql_get_mods) as $row) {
		$mods[] = $row;
	}
	
	for($i=0;$i<count($mods);$i++) {
		$m[]['page_modul'] = $mods[$i]['addon_dir'];
		$m[]['page_permalink'] = 'NULL';
	}
	
	
	$sql = "SELECT page_modul, page_permalink FROM fc_pages";
	
	foreach ($dbh->query($sql) as $row) {
		$pages[] = $row;
	}
	
	$dbh = null;
	
	$items = array_merge($pages, $m);
	
	$cnt_items = count($items);
	$x = 0;
	for($i=0;$i<$cnt_items;$i++) {
	
		if($items[$i]['page_modul'] != "") {
			$string .= "\$active_mods[$x]['page_modul'] = \"" . $items[$i]['page_modul'] . "\";\n";
			$string .= "\$active_mods[$x]['page_permalink'] = \"" . $items[$i]['page_permalink'] . "\";\n";			
			$x++;
		}
	
	}
	
	$str = "<?php\n$string\n?>";
		
	$file = "../" . FC_CONTENT_DIR . "/cache/active_mods.php";
	file_put_contents($file, $str, LOCK_EX);

}



?>