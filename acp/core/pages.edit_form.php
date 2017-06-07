<?php
//prohibit unauthorized access
require 'core/access.php';


echo '<form id="editpage" action="acp.php?tn=pages&sub=edit&editpage='.$editpage.'" class="form-horizontal" method="POST">';

$custom_fields = get_custom_fields();
sort($custom_fields);
$cnt_custom_fields = count($custom_fields);
echo '<div class="row" style="margin-right:0;">';
echo '<div class="col-lg-9 col-md-8 col-sm-12">';

echo '<ul class="nav nav-tabs" id="bsTabs">';
echo '<li class="active"><a href="#info" data-toggle="tab">'.$lang['tab_info'].'</a></li>';
echo '<li><a href="#content" data-toggle="tab">'.$lang['tab_content'].'</a></li>';
echo '<li><a href="#extracontent" data-toggle="tab">'.$lang['tab_extracontent'].'</a></li>';
echo '<li><a href="#meta" data-toggle="tab">'.$lang['tab_meta'].'</a></li>';
echo '<li><a href="#head" data-toggle="tab">'.$lang['tab_head'].'</a></li>';
if($cnt_custom_fields > 0) {
	echo '<li><a href="#custom" data-toggle="tab">'.$lang['legend_custom_fields'].'</a></li>';
}
echo '</ul>';


echo '<div class="tab-content">';

/* tab_info */
echo'<div class="tab-pane fade in active" id="info">';

$dbh = new PDO("sqlite:".CONTENT_DB);
$sql = "SELECT page_linkname, page_sort, page_title, page_language FROM fc_pages
		    WHERE page_sort != 'portal'
		    ORDER BY page_language ASC, page_sort ASC	";
$all_pages = $dbh->query($sql)->fetchAll();

$dbh = null;

$all_pages = fc_array_multisort($all_pages, 'page_language', SORT_ASC, 'page_sort', SORT_ASC, SORT_NATURAL);


$select_page_position  = '<select name="page_position" class="form-control">';
$select_page_position .= '<option value="null">' . $lang['legend_unstructured_pages'] . '</option>';


if($page_sort == "portal") {
	$select_page_position .= '<option value="portal" selected>' . $lang['f_homepage'] . '</option>';
} else {
	$select_page_position .= '<option value="portal">' . $lang['f_homepage'] . '</option>';
}

if(ctype_digit($page_sort)) {
	$select_page_position .= '<option value="mainpage" selected>'.$lang['f_mainpage'].'</option>';
} else {
	$select_page_position .= '<option value="mainpage">'.$lang['f_mainpage'].'</option>';
}

$select_page_position .= '<optgroup label="'.$lang['f_subpage'].'">';
for($i=0;$i<count($all_pages);$i++) {

	if($all_pages[$i]['page_sort'] == $page_sort) {
		continue;
	}
	
	if($all_pages[$i]['page_sort'] == "") {
		continue;
	}
	
	if($pos = strripos($page_sort,".")) {
		$string = substr($page_sort,0,$pos);
	}
		 
		 $parent_string = $all_pages[$i]['page_sort'];
		 
		 unset($selected);
		 if($parent_string != "" && $parent_string == "$string") {
		 	$selected = "selected";
		 }
		 
	$short_title = first_words($all_pages[$i]['page_title'], 6);
	$indent = str_repeat("-",substr_count($parent_string,'.'));
	$select_page_position .= "<option value='$parent_string' $selected> $indent " . $all_pages[$i]['page_sort'] . ' - ' .$all_pages[$i]['page_linkname'] . ' - ' . $short_title ."</option>";
	
}
$select_page_position .= '</optgroup>';
$select_page_position .= '</select>';

echo tpl_form_control_group('',$lang['f_page_position'],$select_page_position);

if($page_sort != "portal") {

	$page_order = substr (strrchr ($page_sort, "."), 1);
	if(ctype_digit($page_sort)) {
		$page_order = $page_sort;
	}
	
	echo tpl_form_control_group('',$lang['f_page_order'],"<input class='form-control' type='text' name='page_order' value='$page_order'>");
	

}

echo '<hr>';
echo tpl_form_control_group('',$lang['f_page_linkname'],'<input class="form-control" type="text" name="page_linkname" value="'.$page_linkname.'">');
echo tpl_form_control_group('',$lang['f_page_permalink'],"<input class='form-control' type='text' name='page_permalink' value='$page_permalink'>");

if(empty($page_permalink_short_cnt)) {
	$page_permalink_short_cnt = 0;
}

echo '<div class="form-group">';
echo '<label class="control-label control-label-normal col-md-2">'.$lang['f_page_permalink_short'].'</label>';
echo '<div class="col-sm-10">';
echo '<div class="input-group">';
echo '<input class="form-control" type="text" name="page_permalink_short" value="'.$page_permalink_short.'">';
echo '<span class="input-group-addon">'.$page_permalink_short_cnt.'</span>';
echo '</div>';
echo '</div>';
echo '</div>';

echo tpl_form_control_group('',$lang['f_page_hash'],"<input class='form-control' type='text' name='page_hash' value='$page_hash'>");

$select_page_redirect_code  = '<select name="page_redirect_code" class="form-control">';
if($page_redirect_code == '') {
	$page_redirect_code = 301;
}
for($i=0;$i<10;$i++) {
	$redirect_code = 300+$i;
	unset($sel_page_redirect_code);
	if($page_redirect_code == $redirect_code) {
		$sel_page_redirect_code = 'selected';
	}
	$select_page_redirect_code .= '<option value="'.$redirect_code.'" '.$sel_page_redirect_code.'>'.$redirect_code.'</option>';
}
$select_page_redirect_code .= '</select>';

echo tpl_form_control_group('',$lang['f_page_redirect'],'<div class="row"><div class="col-md-3">'.$select_page_redirect_code.'</div><div class="col-md-9"><input class="form-control" type="text" name="page_redirect" value="'.$page_redirect.'"></div></div>');

echo '</div>'; /* EOL tab_info */


/* tab_content */
echo '<div class="tab-pane fade" id="content">';

echo '<textarea name="page_content" class="form-control mceEditor textEditor switchEditor" id="textEditor">'.$page_content.'</textarea>';

echo"</div>";
/* EOL tab_content */


/* tab_extracontent */

echo '<div class="tab-pane fade" id="extracontent">';

echo '<textarea name="page_extracontent" class="form-control mceEditor textEditor switchEditor" id="textEditor2">'.$page_extracontent.'</textarea>';

echo '</div>'; /* EOL tab_extracontent */



/* tab_meta */
echo '<div class="tab-pane fade" id="meta">';

echo tpl_form_control_group('',$lang['f_page_title'],'<input class="form-control" type="text" name="page_title" value="'.$page_title.'">');

if($page_meta_author == "") {
	$page_meta_author = $_SESSION['user_firstname'] .' '. $_SESSION['user_lastname'];
}

echo tpl_form_control_group('',$lang['f_meta_author'],'<input class="form-control" type="text" name="page_meta_author" value="'.$page_meta_author.'">');
echo tpl_form_control_group('',$lang['f_meta_keywords'],'<input class="form-control" type="text" name="page_meta_keywords" value="'.$page_meta_keywords.'" data-role="tagsinput">');
echo tpl_form_control_group('',$lang['f_meta_description'],"<textarea name='page_meta_description' class='form-control cntValues' rows='5'>$page_meta_description</textarea>");


echo '<div class="form-group">';
echo '<label class="control-label control-label-normal col-sm-2">'.$lang['page_thumbnail'].'</label>';
echo '<div class="col-sm-10">';
		
echo '<div class="scroll-container">';
echo '<select name="page_thumbnail" class="form-control image-picker">';
echo '<option value="">'.$lang['page_thumbnail'].'</option>';
$arr_Images = get_all_images();
	foreach($arr_Images as $page_thumbnails) {
		$selected = "";
		if($page_thumbnail == "$page_thumbnails") {
			$selected = "selected";
		}
		echo '<option '.$selected.' data-img-src="/content/images/'.$page_thumbnails.'" class="masonry-item" value="'.$page_thumbnails.'">'.$page_thumbnails.'</option>';
}
echo '</select>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</fieldset>';


$select_page_meta_robots  = '<select name="page_meta_robots" class="form-control">';
$select_page_meta_robots .= '<option value="all" '.($page_meta_robots == "all" ? 'selected="selected"' :'').'>all</option>';
$select_page_meta_robots .= '<option value="noindex" '.($page_meta_robots == "noindex" ? 'selected="selected"' :'').'>noindex</option>';
$select_page_meta_robots .= '<option value="nofollow" '.($page_meta_robots == "nofollow" ? 'selected="selected"' :'').'>nofollow</option>';
$select_page_meta_robots .= '<option value="noodp" '.($page_meta_robots == "noodp" ? 'selected="selected"' :'').'>noodp</option>';
$select_page_meta_robots .= '<option value="noydir" '.($page_meta_robots == "noydir" ? 'selected="selected"' :'').'>noydir</option>';
$select_page_meta_robots .= '</select>';
echo tpl_form_control_group('',$lang['f_meta_robots'],$select_page_meta_robots);



echo '</div>'; /* EOL tab_meta */



/* tab_head */
echo '<div class="tab-pane fade" id="head">';

echo $lang['f_head_styles'];
echo '<span class="silent"> &lt;style type=&quot;text/css&quot;&gt;</span> ... <span class="silent">&lt;/styles&gt;</span>';
echo '<textarea name="page_head_styles" class="form-control aceEditor_css" rows="12">'.$page_head_styles.'</textarea>';
echo '<div id="CSSeditor"></div>';

echo '<hr>';

echo $lang['f_head_enhanced'];
echo '<span class="silent"> &lt;head&gt;</span> ... <span class="silent">&lt;/head&gt;</span>';
echo '<textarea name="page_head_enhanced" class="form-control aceEditor_html" rows="12">'.$page_head_enhanced.'</textarea>';
echo '<div id="HTMLeditor"></div>';

echo '</div>'; /* EOL tab_head */



if($cnt_custom_fields > 0) {

/* tab custom fields */
echo '<div class="tab-pane fade" id="custom">';

	for($i=0;$i<$cnt_custom_fields;$i++) {
		
		$custom_field_value = '';
		$custom_field_value = ${$custom_fields[$i]};
		if(substr($custom_fields[$i],0,10) == "custom_one") {
			$label = substr($custom_fields[$i],11);
			echo tpl_form_control_group('',$label,'<input type="text" class="form-control" name="'.$custom_fields[$i].'" value="'.$custom_field_value.'">');
		}	elseif(substr($custom_fields[$i],0,11) == "custom_text") {
			$label = substr($custom_fields[$i],12);
			echo tpl_form_control_group('',$label,"<textarea class='form-control' rows='6' name='$custom_fields[$i]'>" .$custom_field_value. "</textarea>");
		}	elseif(substr($custom_fields[$i],0,14) == "custom_wysiwyg") {
			$label = substr($custom_fields[$i],15);
			echo tpl_form_control_group('',$label,"<textarea class='mceEditor_small' name='$custom_fields[$i]'>" .$custom_field_value. "</textarea>");
		}		
	}

echo '</div>'; /* EOL tab custom fields */

}

echo '</div>'; // EOL fancytabs

echo '</div>';
echo '<div class="col-lg-3 col-md-4 col-sm-12">';


echo '<div class="panel panel-default">';
echo '<div class="panel-heading">'.$lang['tab_page_preferences'].'</div>';
echo '<div class="panel-body" style="padding-left:30px;padding-right:30px;">';


echo '<div class="form-group">';
echo '<div class="btn-group btn-group-justified" data-toggle="buttons">';
echo '<label class="btn btn-sm btn-default"><input type="radio" name="optEditor" value="optE1"> WYSIWYG</label>';
echo '<label class="btn btn-sm btn-default"><input type="radio" name="optEditor" value="optE2"> Text</label>';
echo '<label class="btn btn-sm btn-default"><input type="radio" name="optEditor" value="optE3"> Code</label>';
echo '</div>';
echo '</div>';


/* Select Language */
$arr_lang = get_all_languages();

$select_page_language  = '<select name="page_language" class="form-control">';
for($i=0;$i<count($arr_lang);$i++) {

	$lang_sign = $arr_lang[$i]['lang_sign'];
	$lang_desc = $arr_lang[$i]['lang_desc'];
	$lang_folder = $arr_lang[$i]['lang_folder'];
	$select_page_language .= "<option value='$lang_folder'".($page_language == "$lang_folder" ? 'selected="selected"' :'').">$lang_sign ($lang_desc)</option>";	

} // eo $i

$select_page_language .= '</select>';

echo '<div class="form-group">';
echo '<label>'.$lang['f_page_language'].'</label>';
echo $select_page_language;
echo '</div>';


/* Select Template */

$arr_Styles = get_all_templates();

$select_select_template = '<select id="select_template" name="select_template"  class="form-control">';

if($page_template == '') {
	$selected_standard = 'selected';
}

$select_select_template .= "<option value='use_standard<|-|>use_standard' $selected_standard>$lang[use_standard]</option>";

/* templates list */
foreach($arr_Styles as $template) {

	$arr_layout_tpl = glob("../styles/$template/templates/layout*.tpl");
	
	$select_select_template .= "<optgroup label='$template'>";
	
	foreach($arr_layout_tpl as $layout_tpl) {
		$layout_tpl = basename($layout_tpl);
	
		$selected = '';
		if($template == "$page_template" && $layout_tpl == "$page_template_layout") {
			$selected = 'selected';
		}
		
		$select_select_template .=  "<option $selected value='$template<|-|>$layout_tpl'>$template » $layout_tpl</option>";
	}
	
	$select_select_template .= '</optgroup>';

}

$select_select_template .= '</select>';

echo '<div class="form-group">';
echo '<label>'.$lang['f_page_template'].'</label>';
echo $select_select_template;
echo '</div>';

/* Select Modul */

$arr_iMods = get_all_moduls();

$select_page_modul = '<select name="page_modul"  class="form-control">';

$select_page_modul .= '<option value="">Kein Modul</option>';

for($i=0;$i<count($arr_iMods);$i++) {

	$selected = "";
	$mod_name = $arr_iMods[$i]['name'];
	$mod_folder = $arr_iMods[$i]['folder'];

	if($mod_folder == "$page_modul") {
		$selected = "selected";
	}

	$select_page_modul .= "<option value='$mod_folder' $selected>$mod_name</option>";

}


$select_page_modul .= '</select>';


echo '<div class="form-group">';
echo '<label>'.$lang['f_page_modul'].'</label>';
echo $select_page_modul;
echo '</div>';

echo '<div class="form-group">';
echo '<label>'.$lang['f_page_modul_query'].'</label>';
echo "<input class='form-control' type='text' name='page_modul_query' value='$page_modul_query'>";
echo '</div>';


/* Select  Status */

unset($checked_status);

if($page_status == "") {
	$page_status = "public";
}


$select_page_status = '<div class="text-center">';
$select_page_status .= '<div class="btn-group btn-group-justified" data-toggle="buttons">';

$select_page_status .= '<label class="btn btn-xs btn-default btn-public '.($page_status == "public" ? 'active' :'').' ">';
$select_page_status .= "<input type='radio' name='page_status' value='public'".($page_status == "public" ? 'checked' :'')."> $lang[f_page_status_puplic]";
$select_page_status .= '</label>';

$select_page_status .= '<label class="btn btn-xs btn-default btn-ghost '.($page_status == "ghost" ? 'active' :'').'">';
$select_page_status .= "<input type='radio' name='page_status' value='ghost'".($page_status == "ghost" ? 'checked' :'')."> $lang[f_page_status_ghost]";
$select_page_status .= '</label>';

$select_page_status .= '<label class="btn btn-xs btn-default btn-private '.($page_status == "private" ? 'active' :'').'">';
$select_page_status .= "<input type='radio' name='page_status' value='private'".($page_status == "private" ? 'checked' :'')."> $lang[f_page_status_private]";
$select_page_status .= '</label>';

$select_page_status .= '<label class="btn btn-xs btn-default btn-draft '.($page_status == "draft" ? 'active' :'').'">';
$select_page_status .= "<input type='radio' name='page_status' value='draft'".($page_status == "draft" ? 'checked' :'')."> $lang[f_page_status_draft]";	
$select_page_status .= '</label>';

$select_page_status .= '</div>';
$select_page_status .= '</div>';

echo '<div class="form-group">';
echo '<label>'.$lang['f_page_status'].'</label>';
echo '<div>';
echo $select_page_status;
echo '</div>';
echo '</div>';


/* Select Usergroups */

$arr_groups = get_all_groups();
$arr_checked_groups = explode(",",$page_usergroup);

for($i=0;$i<count($arr_groups);$i++) {

	$group_id = $arr_groups[$i]['group_id'];
	$group_name = $arr_groups[$i]['group_name'];

	if(in_array("$group_name", $arr_checked_groups)) {
		$checked = "checked";
	} else {
		$checked = "";
	}
	
	$checkbox_usergroup .= '<div class="checkbox"><label>';
	$checkbox_usergroup .= "<input type='checkbox' $checked name='set_usergroup[]' value='$group_name'> $group_name";
	$checkbox_usergroup .= '</label></div>';
}

echo '<div class="form-group">';
echo '<div class="well well-sm">';
echo '<a href="#" data-toggle="collapse" data-target="#usergroups">'.$lang['choose_usergroup'].'</a>';
echo '<div id="usergroups" class="collapse">';
echo $checkbox_usergroup;
echo '</div>';
echo '</div>';


/* Select Rights Management */

$arr_admins = get_all_admins();
$arr_checked_admins = explode(",", $page_authorized_users);
$cnt_admins = count($arr_admins);

for($i=0;$i<$cnt_admins;$i++) {

	$user_nick = $arr_admins[$i]['user_nick'];

  if(in_array("$user_nick", $arr_checked_admins)) {
		$checked_user = "checked";
	} else {
		$checked_user = "";
	}
		
	$checkbox_set_authorized_admins .= '<div class="checkbox"><label>';
 	$checkbox_set_authorized_admins .= "<input type='checkbox' $checked_user name='set_authorized_admins[]' value='$user_nick'> $user_nick";
 	$checkbox_set_authorized_admins .= '</label></div>';
}


echo '<div class="well well-sm">';
echo '<a href="#" data-toggle="collapse" data-target="#admins">'.$lang['f_page_authorized_admins'].'</a>';
echo '<div id="admins" class="collapse">';
echo $checkbox_set_authorized_admins;
echo '</div>';
echo '</div>';



/* select labels */


$cnt_labels = count($fc_labels);
$arr_checked_labels = explode(",", $page_labels);

for($i=0;$i<$cnt_labels;$i++) {
	$label_title = $fc_labels[$i]['label_title'];
	$label_id = $fc_labels[$i]['label_id'];
	$label_color = $fc_labels[$i]['label_color'];
	
  if(in_array("$label_id", $arr_checked_labels)) {
		$checked_label = "checked";
	} else {
		$checked_label = "";
	}
	
	$checkbox_set_labels .= '<div class="checkbox"><label>';
 	$checkbox_set_labels .= "<input type='checkbox' $checked_label name='set_page_labels[]' value='$label_id'> $label_title";
 	$checkbox_set_labels .= '</label></div>';
	
}


echo '<div class="well well-sm">';
echo '<a href="#" data-toggle="collapse" data-target="#labels">'.$lang['labels'].'</a>';
echo '<div id="labels" class="collapse">';
echo $checkbox_set_labels;
echo '</div>';
echo '</div>';

echo '</div>'; // form-group



echo '<input type="hidden" name="page_version" value="'.$page_version.'">';
echo '<input type="hidden" name="modus" value="'.$modus.'">';

echo '<div class="form-group">';
echo '<div class="btn-group btn-group-justified">';
echo '<div class="btn-group">'.$submit_button.'</div><div class="btn-group">'.$previev_button.'</div>';
echo '</div>';
echo $delete_button;
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</div>';

echo '</div>'; // panel-body
echo '</div>'; // panel

echo '</div>'; // col
echo '</div>'; // row


//submit form to save data



echo '</form>';



?>