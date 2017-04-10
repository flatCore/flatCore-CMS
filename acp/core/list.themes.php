<?php

//prohibit unauthorized access
require 'core/access.php';


if(isset($_POST['saveDesign'])) {

	// all incoming data -> strip_tags
	foreach($_POST as $key => $val) {
		$$key = @strip_tags($val); 
	}
	
	// template
	$select_template = explode("<|-|>", $_POST['select_template']);
	$prefs_template = $select_template[0];
	$prefs_template_layout = $select_template[1];
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	
	$pdo_fields = array(
		'prefs_template' => 'STR',
		'prefs_template_layout' => 'STR'
	);

	$sql = generate_sql_update_str($pdo_fields,"fc_preferences","WHERE prefs_id = 1");
	$sth = $dbh->prepare($sql);

	generate_bindParam_str($pdo_fields,$sth);
	$sth->bindParam(':prefs_template', $prefs_template, PDO::PARAM_STR);
	$sth->bindParam(':prefs_template_layout', $prefs_template_layout, PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	
	$dbh = null;
	
	if($cnt_changes == TRUE) {
		$sys_message = "{OKAY} $lang[db_changed]";
		record_log($_SESSION['user_nick'],"edit system design <b>$prefs_template</b>","6");
	} else {
		$sys_message = "{ERROR} $lang[db_not_changed]";
		record_log($_SESSION['user_nick'],"error on saving system design","11");
	}

}

if($sys_message != ""){
	print_sysmsg("$sys_message");
}

echo '<form action="'.$_SERVER['PHP_SELF'].'?tn=moduls&sub=t" method="POST" class="form-horizontal">';
echo '<fieldset>';
echo '<legend>'.$lang['f_prefs_layout'].'</legend>';


$arr_Styles = get_all_templates();

$select_prefs_template = '<select name="select_template" class="form-control">';


/* templates list */
foreach($arr_Styles as $template) {

	$arr_layout_tpl = glob("../styles/$template/templates/layout*.tpl");
	
	$select_prefs_template .= "<optgroup label='$template'>";
	
	foreach($arr_layout_tpl as $layout_tpl) {
		$layout_tpl = basename($layout_tpl);
		
		$selected = "";
		if($template == "$prefs_template" && $layout_tpl == "$prefs_template_layout") {
			$selected = "selected";
		}
		$select_prefs_template .= "<option $selected value='$template<|-|>$layout_tpl'>$template » $layout_tpl</option>";
	}
	$select_prefs_template .= '</optgroup>';

}

$select_prefs_template .= '</select>';

echo tpl_form_control_group('',$lang['f_prefs_active_template'],$select_prefs_template);


echo '<div class="formfooter">';
echo '<input type="submit" class="btn btn-success" name="saveDesign" value="'.$lang['save'].'">';
echo '</div>';

echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';

echo '</fieldset>';
echo '</form>';


/**
 * theme options
 */

echo '<fieldset>';
echo '<legend>'.$prefs_template.'</legend>';

echo '<div class="row">';
echo '<div class="col-md-9">';

if(is_file("../styles/$prefs_template/php/options.php")) {
	include("../styles/$prefs_template/php/options.php");
}

echo '</div>';
echo '<div class="col-md-3">';

if(is_file("../styles/$prefs_template/images/preview.png")) {
	echo '<img src="../styles/'.$prefs_template.'/images/preview.png" class="img-responsive img-rounded">';
}

echo '<table class="table table-condensed">';
if(is_file('../styles/'.$prefs_template.'/info.xml')) {
	$theme_xml = simplexml_load_file('../styles/'.$prefs_template.'/info.xml');
	//print_r($info_xml);
	
	echo '<tr>';
	echo '<td>Theme:</td>';
	echo '<td>' . $theme_xml->name . '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>Version:</td>';
	echo '<td>' . $theme_xml->version . '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>License:</td>';
	echo '<td>' . $theme_xml->license . '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>Author:</td>';
	echo '<td>' . $theme_xml->author . '</td>';
	echo '</tr>';
	if($theme_xml->author_url != '') {
		echo '<tr>';
		echo '<td>URL:</td>';
		echo '<td><a href="'.$theme_xml->author_url.'">' . $theme_xml->author_url . '</a></td>';
		echo '</tr>';		
	}
	echo '<tr>';
	echo '<td>Requires</td>';
	echo '<td>';
	foreach ($theme_xml->requires -> core as $requires) {
	    switch((string) $requires['type']) {
	    case 'min':
	        echo 'min: ' . $requires . '<br>';
	        break;
	    case 'max':
	        echo 'max: ' . ($requires > 0 ? $requires : 'unknown');
	        break;
	    }
	}
	echo '</td>';
	echo '<tr>';
}
echo '</table>';

echo '</div>';
echo '</div>';


echo '</fieldset>';


?>