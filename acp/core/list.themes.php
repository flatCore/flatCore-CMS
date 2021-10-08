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
		"prefs_template_stylesheet" =>  $select_template_sytlesheet
	], [
		"prefs_id" => 1
	]);	

	
	if($data->rowCount() > 0) {
		$sys_message = "{OKAY} $lang[db_changed]";
		record_log($_SESSION['user_nick'],"edit system design <b>$prefs_template</b>","6");
		
		/* read the preferences again */
		$fc_preferences = get_preferences();
		foreach($fc_preferences as $k => $v) {
			$$k = stripslashes($v);
		}
		
		fc_delete_smarty_cache('all');
		
	} else {
		$sys_message = "{ERROR} $lang[db_not_changed]";
		record_log($_SESSION['user_nick'],"error on saving system design","11");
	}

}

if(isset($_POST['save_theme_options'])) {
	
	$save_theme_options = fc_write_theme_options($_POST);	
	echo $save_theme_options;
	
}



if($sys_message != ""){
	print_sysmsg("$sys_message");
}


echo '<h3>'.$lang['f_prefs_layout'].'</h3>';



$arr_Styles = get_all_templates();

/* templates list */

foreach($arr_Styles as $template) {
	
	$arr_layout_tpls = glob("../styles/".$template."/templates/layout*.tpl");
	
	$border = '';
	$active = '';
	if($template == $prefs_template) {
		$border = 'border-success';
		$active = '(active)';
	}
		
	echo '<div class="card mb-3 '.$border.'">';
	echo '<div class="card-header">'.$template.' '.$active.'</div>';
	echo '<div class="card-body">';
	
	echo '<div class="row">';

	echo '<div class="col-md-9">';
	
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
	
	$stylesheets = glob('../styles/'.$template.'/css/theme_*.css');
	
	echo '<select name="select_template_sytlesheet" class="form-control">';
	
	echo '<option value=""></option>'; //blank
	foreach($stylesheets as $css) {
		
		$active = '';
		if($css == $fc_preferences['prefs_template_stylesheet']) {
			$active = 'selected';
		}
		
		echo '<option value="'.$css.'" '.$active.'>'.$css.'</option>';
	}
	
	echo '</select><hr>';
	
	echo '<input type="submit" class="btn btn-save btn-md" name="saveDesign" value="Layout '.$lang['save'].'">';
	echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
	
	echo '</form><hr>';
	
	
	unset($theme_options);
	if(is_file("../styles/$template/php/options.php")) {
		include '../styles/'.$template.'/php/options.php';
	}
	

	
	/* $theme_options (array) -> if set in /theme/options.php */
	
	if(is_array($theme_options)) {
		
		$theme_data = fc_get_theme_options($template);
		
		echo '<div class="card mb-3">';
		echo '<div class="card-header">';
		echo '<a class="" data-bs-toggle="collapse" href="#collapse'.$template.'" role="button" aria-expanded="false" aria-controls="collapse'.$template.'">+ Theme Options</a>';
		echo '</div>';
		echo '<div class="collapse" id="collapse'.$template.'">';
		echo '<form action="?tn=moduls&sub=t" method="POST">';
		echo '<div class="card-body">';
		
		foreach($theme_options as $key => $value) {
			
			$this_value = '';
			$get_key = '';
			$get_key = array_search("theme_$key", array_column($theme_data, 'theme_label'));
			if(is_numeric($get_key)) {
				$this_value = $theme_data[$get_key]['theme_value'];
			}
			

			
			echo '<div class="mb-3">';
			echo '<label class="form-label">'.$value.'</label>';
			echo '<input type="text" name="theme_'.$key.'" value="'.$this_value.'" class="form-control">';
			echo '</div>';
			
		}
		echo '</div>';
		
		
		echo '<div class="card-footer">';
		echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
		echo '<input type="hidden" name="theme" value="'.$template.'">';
		echo '<input type="submit" name="save_theme_options" value="'.$lang['save'].'" class="btn btn-success">';
		echo '</div>';
	
		
		echo '</form>';
		
		echo '</div>';
		echo '</div>';

	}
		
	
	
	echo '</div>';
	echo '<div class="col-md-3">';
	
	if(is_file("../styles/$template/images/preview.png")) {
		echo '<p class="text-center"><img src="../styles/'.$template.'/images/preview.png" class="img-fluid rounded"></p>';
	}
	
	$modal = '';
	
	$readme = file_get_contents('../styles/'.$template.'/readme.html');
	if($readme != '') {
		$modal = file_get_contents('templates/bs-modal.tpl');
		$modal = str_replace('{modalID}', "ID$template", $modal);
		$modal = str_replace('{modalBody}', "$readme", $modal);
		$modal = str_replace('{modalTitle}', "$template <small>readme.html</small>", $modal);
		echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ID'.$template.'">readme.html</button>';
	}
	
	echo $modal;
	
	
	$btn_delete_addon = '<form class="d-inline ps-2" action="?tn=addons&sub=t" method="POST" onsubmit="return confirm(\''.$lang['confirm_delete_file'].'\');">';
	$btn_delete_addon .= '<button type="submit" name="delete_addon" class="btn btn-sm btn-fc text-danger">'.$icon['trash_alt'].'</button>';
	$btn_delete_addon .= '<input type="hidden" name="type" value="t">';
	$btn_delete_addon .= '<input type="hidden" name="addon" value="'.$template.'">';
	$btn_delete_addon .= '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
	$btn_delete_addon .= '</form>';
	echo $btn_delete_addon;
	
	echo '</div>';
	echo '</div>';
	echo '</div>'; // card-body
	echo '</div>'; // card
}

?>