<?php

//prohibit unauthorized access
require 'core/access.php';


foreach($_POST as $key => $val) {
	$$key = @htmlspecialchars($val, ENT_QUOTES); 
}


/* save thumbnail */

if(isset($_POST['save_prefs_thumbnail'])) {
	
	$data = $db_content->update("fc_preferences", [
		"prefs_pagethumbnail" =>  $prefs_pagethumbnail,
		"prefs_pagethumbnail_prefix" => $prefs_pagethumbnail_prefix,
		"prefs_pagefavicon" => $prefs_pagefavicon
	], [
	"prefs_id" => 1
	]);

}


/* save upload preferences */
if(isset($_POST['save_prefs_upload'])) {
	
	if(isset($_POST['prefs_showfilesize'])) {
		$prefs_showfilesize = 'yes';
	} else {
		$prefs_showfilesize = 'no';
	}
	
	if(isset($_POST['prefs_uploads_remain_unchanged'])) {
		$prefs_upload_unchanged = 'yes';
	} else {
		$prefs_upload_unchanged = 'no';
	}
	
	$data = $db_content->update("fc_preferences", [
		"prefs_imagesuffix" =>  $prefs_imagesuffix,
		"prefs_maximagewidth" => $prefs_maximagewidth,
		"prefs_maximageheight" => $prefs_maximageheight,
		"prefs_maxtmbwidth" => $prefs_maxtmbwidth,
		"prefs_maxtmbheight" => $prefs_maxtmbheight,
		"prefs_filesuffix" => $prefs_filesuffix,
		"prefs_maxfilesize" => $prefs_maxfilesize,
		"prefs_showfilesize" => $prefs_showfilesize,
		"prefs_uploads_remain_unchanged" => $prefs_upload_unchanged
	], [
	"prefs_id" => 1
	]);	
	

}


if(isset($_POST)) {
	/* read the preferences again */
	$fc_preferences = get_preferences();
	
	foreach($fc_preferences as $k => $v) {
	   $$k = stripslashes($v);
	}
}



/* default Thumbnail */
echo '<div id="thumbnail" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>'.$lang['page_thumbnail_default'].'</legend>';
echo '<form action="acp.php?tn=system&sub=images" method="POST" class="form-horizontal">';

$select_prefs_thumbnail  = '<select name="prefs_pagethumbnail" class="form-control custom-select">';
$select_prefs_thumbnail .= '<option value="">'.$lang['page_thumbnail'].'</option>';
$arr_Images = fc_get_all_images_rec();
	foreach($arr_Images as $page_thumbnail) {
		$selected = "";
		if($prefs_pagethumbnail == "$page_thumbnail") {
			$selected = "selected";
		}
		$show_page_thumbnail_filename = str_replace('../content/','/',$page_thumbnail);
		$select_prefs_thumbnail .= '<option '.$selected.' value="'.$page_thumbnail.'">'.$show_page_thumbnail_filename.'</option>';
}
$select_prefs_thumbnail .= "</select>";

echo tpl_form_control_group('',$lang['page_thumbnail'],$select_prefs_thumbnail);

/* Thumbnail Prefix */
$prefs_tmb_prefix_input = "<input class='form-control' type='text' name='prefs_pagethumbnail_prefix' value='$prefs_pagethumbnail_prefix'>";
echo tpl_form_control_group('',$lang['page_thumbnail_prefix'],$prefs_tmb_prefix_input);

/* Favicon */
$select_prefs_favicon  = '<select name="prefs_pagefavicon" class="form-control custom-select">';
$select_prefs_favicon .= '<option value="">'.$lang['page_favicon'].'</option>';
$arr_Images = fc_get_all_images_rec();
	foreach($arr_Images as $page_favicon) {
		
		if(substr($page_favicon, -4) != '.png') {
			continue;
		}
		
		$selected = "";
		if($prefs_pagefavicon == "$page_favicon") {
			$selected = "selected";
		}
		$show_page_favicon_filename = str_replace('../content/','/',$page_favicon);
		$select_prefs_favicon .= '<option '.$selected.' value="'.$page_favicon.'">'.$show_page_favicon_filename.'</option>';
}
$select_prefs_favicon .= "</select>";

echo tpl_form_control_group('',$lang['page_favicon'],$select_prefs_favicon);



echo tpl_form_control_group('','',"<input type='submit' class='btn btn-save' name='save_prefs_thumbnail' value='$lang[save]'>");
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';
echo '</fieldset>';




/* Upload Preferences */
echo '<div id="uploads" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>'.$lang['f_prefs_uploads'].'</legend>';
echo '<form action="acp.php?tn=system&sub=images" method="POST" class="form-horizontal">';

$prefs_maximage_input  = '<div class="row"><div class="col-md-6">';
$prefs_maximage_input .= '<div class="input-group">';
$prefs_maximage_input .= '<input class="form-control" type="text" name="prefs_maximagewidth" value="'.$prefs_maximagewidth.'">';
$prefs_maximage_input .= '<div class="input-group-append"><span class="input-group-text"><i class="fas fa-arrows-alt-h"></i></span></div>';
$prefs_maximage_input .= '</div>';
$prefs_maximage_input .= '</div><div class="col-md-6">';
$prefs_maximage_input .= '<div class="input-group">';
$prefs_maximage_input .= '<input class="form-control" type="text" name="prefs_maximageheight" value="'.$prefs_maximageheight.'">';
$prefs_maximage_input .= '<div class="input-group-append"><span class="input-group-text"><i class="fas fa-arrows-alt-v"></i></span></div>';
$prefs_maximage_input .= '</div>';
$prefs_maximage_input .= '</div></div>';

$prefs_maxtmb_input  = '<div class="row"><div class="col-md-6">';
$prefs_maxtmb_input .= '<div class="input-group">';
$prefs_maxtmb_input .= '<input class="form-control" type="text" name="prefs_maxtmbwidth" value="'.$prefs_maxtmbwidth.'">';
$prefs_maxtmb_input .= '<div class="input-group-append"><span class="input-group-text"><i class="fas fa-arrows-alt-h"></i></span></div>';
$prefs_maxtmb_input .= '</div>';
$prefs_maxtmb_input .= '</div><div class="col-md-6">';
$prefs_maxtmb_input .= '<div class="input-group">';
$prefs_maxtmb_input .= '<input class="form-control" type="text" name="prefs_maxtmbheight" value="'.$prefs_maxtmbheight.'">';
$prefs_maxtmb_input .= '<div class="input-group-append"><span class="input-group-text"><i class="fas fa-arrows-alt-v"></i></span></div>';
$prefs_maxtmb_input .= '</div>';
$prefs_maxtmb_input .= '</div></div>';

echo '<div class="row">';
echo '<div class="col-md-6">';
echo '<p>'.$lang['images'].'</p>';
echo tpl_form_control_group('',$lang['f_prefs_maximage'],"$prefs_maximage_input");
echo '</div>';
echo '<div class="col-md-6">';
echo '<p>'.$lang['thumbnails'].'</p>';
echo tpl_form_control_group('',$lang['f_prefs_maximage'],"$prefs_maxtmb_input");
echo '</div>';
echo '</div>';

echo tpl_form_control_group('',$lang['f_prefs_maxfilesize'],"<input class='form-control' type='text' name='prefs_maxfilesize' value='$prefs_maxfilesize'>");

$toggle_btn_upload_unchanged  = '<div class="form-group form-check">';
$toggle_btn_upload_unchanged .= '<input type="checkbox" class="form-check-input" id="checkUpload" name="prefs_uploads_remain_unchanged" '.($prefs_uploads_remain_unchanged == "yes" ? 'checked' :'').'>';
$toggle_btn_upload_unchanged .= '<label class="form-check-label" for="checkUpload">'.$lang['f_prefs_uploads_remain_unchanged'].'</label>';
$toggle_btn_upload_unchanged .= '</div>';

echo $toggle_btn_upload_unchanged;


echo tpl_form_control_group('','',"<input type='submit' class='btn btn-save' name='save_prefs_upload' value='$lang[save]'>");
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';
echo '</fieldset>';


?>