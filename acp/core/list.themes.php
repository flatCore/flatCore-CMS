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

	
	$data = $db_content->update("fc_preferences", [
		"prefs_template" =>  $prefs_template,
		"prefs_template_layout" =>  $prefs_template_layout,
	], [
		"prefs_id" => 1
	]);	

	
	if($data->rowCount() > 0) {
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




echo '<h3>'.$lang['f_prefs_layout'].'</h3>';

echo '<div class="card">';
echo '<div class="card-header">';
echo '<ul class="nav nav-tabs card-header-tabs" id="bsTabs" role="tablist">';
echo '<li class="nav-item"><a class="nav-link active" href="#activetheme" data-toggle="tab">'.$lang['label_active_theme'].'</a></li>';
echo '<li class="nav-item"><a class="nav-link" href="#installedthemes" data-toggle="tab">'.$lang['label_installed_themes'].'</a></li>';
echo '</ul>';
echo '</div>';
echo '<div class="card-body">';
echo '<div class="tab-content">';
echo '<div class="tab-pane fade active show" id="activetheme">';

/**
 * active theme options
 */

echo '<fieldset>';
echo '<legend>'.$prefs_template.' ('.$prefs_template_layout.')</legend>';

echo '<div class="row">';
echo '<div class="col-lg-12 col-xl-8">';

if(is_file("../styles/$prefs_template/php/options.php")) {
	include '../styles/'.$prefs_template.'/php/options.php';
}

echo '</div>';
echo '<div class="col-lg-12 col-xl-4">';

if(is_file("../styles/$prefs_template/images/preview.png")) {
	echo '<img src="../styles/'.$prefs_template.'/images/preview.png" class="img-fluid rounded">';
}

echo '<div class="table-responsive">';
echo '<table class="table table-sm">';
if(is_file('../styles/'.$prefs_template.'/info.xml')) {
	$theme_xml = simplexml_load_file('../styles/'.$prefs_template.'/info.xml');
	
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
echo '</div>';
 
echo '</fieldset>';


echo '</div>';
echo '<div class="tab-pane fade" id="installedthemes">';


$arr_Styles = get_all_templates();

/* templates list */

foreach($arr_Styles as $template) {
	
	$arr_layout_tpls = glob("../styles/".$template."/templates/layout*.tpl");
	
	echo '<fieldset>';
	echo '<legend>'.$template.'</legend>';
	
	echo '<div class="row">';
	echo '<div class="col-md-3">';
	
	if(is_file("../styles/$template/images/preview.png")) {
		echo '<p class="text-center"><img src="../styles/'.$template.'/images/preview.png" class="img-fluid rounded"></p>';
	}
	
	echo '</div>';
	echo '<div class="col-md-6">';
	
	echo '<form action="acp.php?tn=moduls&sub=t" method="POST">';
	echo '<select name="select_template" class="form-control image-picker">';
	echo '<option value=""></option>'; // blank
	foreach($arr_layout_tpls as $layout_tpl) {
		
		$active = '';
		if($prefs_template_layout == basename($layout_tpl) && ($template == $prefs_template)) {
			$active = 'selected';
		}
		
		$tpl_name = basename($layout_tpl,'.tpl');		
		$preview = '../styles/'.$template.'/images/'.$tpl_name.'.png';
		$value = "$template<|-|>$tpl_name".'.tpl';
		if(!is_file($preview)) {			
			$preview = './images/tpl-preview.png';
		}
		
		echo '<option data-img-src="'.$preview.'" value="'.$value.'" '.$active.'>'.$tpl_name.'</option>';
		
		
		echo basename($layout_tpl,'.tpl').'<br>';
	}
	echo '</select><hr>';
	
	echo '<input type="submit" class="btn btn-save btn-md" name="saveDesign" value="'.$lang['save'].'">';
	echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
	
	echo '</form>';
	
	echo '</div>';
	echo '<div class="col-md-3">';
	
	echo '<div class="table-responsive">';
	echo '<table class="table table-sm">';
	if(is_file('../styles/'.$template.'/info.xml')) {
		$theme_xml = simplexml_load_file('../styles/'.$template.'/info.xml');
		
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
	echo '</div>';
	echo '</fieldset>';
}


echo '</div>'; // tab-pane
echo '</div>'; // tab-content
echo '</div>'; // card-body
echo '</div>'; // card


?>