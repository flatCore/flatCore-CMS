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
	
	$plugins = array();
	$scanned_directory = array_diff(scandir(FC_CONTENT_DIR.'/plugins/'), array('..', '.','.DS_Store'));
	foreach($scanned_directory as $p) {
		
		$path_parts = pathinfo($p);
		if($path_parts['extension'] == 'php') {
			$plugins[] = $p;
		} else {
			if((is_dir(FC_CONTENT_DIR.'/plugins/'.$p)) && (is_file(FC_CONTENT_DIR.'/plugins/'.$p.'/index.php'))) {
				$plugins[] = $p;
			}
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
	
	global $db_content;
	$result = array();
	
	if($t == 'module') {
		$type = 'module';
	} else {
		$type = 'theme';
	}

	$result = $db_content->select("fc_addons", "*", [
	"addon_type" => "$type"
	]);
	
	return $result;
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
 * return available stylesheets from a theme
 * find css files theme_*.css
 */
 
function fc_get_stylesheets($theme){
	
	$stylesheets = glob('../styles/'.$theme.'/css/theme_*.css');
	
	if(is_array($stylesheets) && (count($stylesheets) > 0)){
		return $stylesheets;
	} else {
		return '0';
	}
	
	
}



/**
 * check in active modules and pages with posts
 * generate array from pages containing a module or post categories
 * and from addon_dir -> content.sqlite3
 * store in ... cache/active_mods.php
 */

function mods_check_in() {
	
	global $db_content;
	
	$pages = array();
	$mods = array();
	$m = array();

	$mods = $db_content->select("fc_addons", "addon_dir", [
	"addon_type" => "module"
	]);
	
	for($i=0;$i<count($mods);$i++) {
		$m[]['page_modul'] = $mods[$i];
		$m[]['page_permalink'] = 'NULL';
	}
		
	$pages = $db_content->select("fc_pages", ["page_modul","page_permalink","page_posts_categories","page_type_of_use"]);	
	$items = array_merge($pages, $m);
	
	$cnt_items = count($items);
	$x = 0;
	for($i=0;$i<$cnt_items;$i++) {
	
		if($items[$i]['page_modul'] != "" OR $items[$i]['page_posts_categories'] != "" OR $items[$i]['page_type_of_use'] == "display_post") {
			
			if($items[$i]['page_posts_categories'] != '') {
				$items[$i]['page_modul'] = 'fc_post';
			}
			
			if($items[$i]['page_type_of_use'] == 'display_post') {
				$items[$i]['page_modul'] = 'fc_post';
			}
			
			$string .= "\$active_mods[$x]['page_modul'] = \"" . $items[$i]['page_modul'] . "\";\n";
			$string .= "\$active_mods[$x]['page_permalink'] = \"" . $items[$i]['page_permalink'] . "\";\n";			
			$x++;
		}
	
	}
	
	$str = "<?php\n$string\n?>";
		
	$file = FC_CONTENT_DIR . "/cache/active_mods.php";
	file_put_contents($file, $str, LOCK_EX);

}

/**
 * write/update theme options
 * $data (array) $data['theme'] -> name of the theme
 * values are prefixed by 'theme' f.e. $data['theme_']
 */

function fc_write_theme_options($data) {
	
	global $db_content;
	
	$db_content->delete("fc_themes", [
		"theme_name" => $data['theme']
	]);
	
	foreach($data as $key => $value) {
		
		if($key == 'theme') {
			$theme = $value;
			continue;
		}
		
		if((strstr($key, '_', true)) == 'theme') {	
			$db_content->insert("fc_themes", ["theme_name" => $data['theme'],"theme_label" => "$key","theme_value" => "$value"]);
		}
		
		
	}

}






?>