<?php

//prohibit unauthorized access
require("core/access.php");

/**
 * save the preferences
 */

if($_POST['saveprefs']) {

	// all incoming data -> strip_tags
	foreach($_POST as $key => $val) {
		$$key = @strip_tags($val); 
	}
	
	// template
	$select_template = explode("<|-|>", $_POST['select_template']);
	$prefs_template = $select_template[0];
	$prefs_template_layout = $select_template[1];
	
	if(isset($_POST['prefs_userregistration'])) {
		$prefs_userregistration = 'yes';
	} else {
		$prefs_userregistration = 'no';
	}
	
	if(isset($_POST['prefs_showloginform'])) {
		$prefs_showloginform = 'yes';
	} else {
		$prefs_showloginform = 'no';
	}

	if(isset($_POST['prefs_showfilesize'])) {
		$prefs_showfilesize = 'yes';
	} else {
		$prefs_showfilesize = 'no';
	}

	if(isset($_POST['prefs_logfile'])) {
		$prefs_logfile = 'on';
	} else {
		$prefs_logfile = 'off';
	}

	if(isset($_POST['prefs_xml_sitemap'])) {
		$prefs_xml_sitemap = 'on';
	} else {
		$prefs_xml_sitemap = 'off';
	}	

	
	$pdo_fields = array(
		'prefs_pagetitle' => 'STR',
		'prefs_pagesubtitle' => 'STR',
		'prefs_pagesglobalhead' => 'STR',
		'prefs_imagesuffix' => 'STR',
		'prefs_maximagewidth' => 'INT',
		'prefs_maximageheight' => 'INT',
		'prefs_template' => 'STR',
		'prefs_template_layout' => 'STR',
		'prefs_usertemplate' => 'STR',
		'prefs_filesuffix' => 'STR',
		'prefs_maxfilesize' => 'INT',
		'prefs_showfilesize' => 'STR',
		'prefs_userregistration' => 'STR',
		'prefs_showloginform' => 'STR',
		'prefs_logfile' => 'STR',
		'prefs_xml_sitemap' => 'STR',
		'prefs_pagethumbnail' => 'STR',
		'prefs_mailer_name' => 'STR',
		'prefs_mailer_adr' => 'STR',
		'prefs_rss_time_offset' => 'STR'
	);
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	
	$sql = generate_sql_update_str($pdo_fields,"fc_preferences","WHERE prefs_id = 1");
	$sth = $dbh->prepare($sql);
	
	generate_bindParam_str($pdo_fields,$sth);
	$sth->bindParam(':prefs_template', $prefs_template, PDO::PARAM_STR);
	$sth->bindParam(':prefs_template_layout', $prefs_template_layout, PDO::PARAM_STR);
	$sth->bindParam(':prefs_userregistration', $prefs_userregistration, PDO::PARAM_STR);
	$sth->bindParam(':prefs_showloginform', $prefs_showloginform, PDO::PARAM_STR);
	$sth->bindParam(':prefs_showfilesize', $prefs_showfilesize, PDO::PARAM_STR);
	$sth->bindParam(':prefs_logfile', $prefs_logfile, PDO::PARAM_STR);
	$sth->bindParam(':prefs_xml_sitemap', $prefs_xml_sitemap, PDO::PARAM_STR);	
	$cnt_changes = $sth->execute();
	
	$dbh = null;
	
	if($cnt_changes == TRUE) {
		$sys_message = "{OKAY} $lang[db_changed]";
		record_log("$_SESSION[user_nick]","edit system preferences","6");
	} else {
		$sys_message = "{ERROR} $lang[db_not_changed]";
		record_log("$_SESSION[user_nick]","error on saving system preferences","11");
	}

} // eo $_POST[saveprefs]



if($sys_message != ""){
	print_sysmsg("$sys_message");
}



/* READ THE PREFS */
$result = get_preferences();

foreach($result as $k => $v) {
   $$k = stripslashes($v);
}


/* print the form */

echo '<div class="row"><div class="col-md-12">';

echo"<form action='$_SERVER[PHP_SELF]?tn=system&sub=sys_pref' method='POST' class='form-horizontal'>";

/* page preferences */

echo"<fieldset>";

echo"<legend>$lang[f_prefs_descriptions]</legend>";


$prefs_pagetitle_input = "<input class='form-control' type='text' name='prefs_pagetitle' value='$prefs_pagetitle'>";
echo tpl_form_control_group('',$lang[f_prefs_pagetitle],$prefs_pagetitle_input);

$prefs_pagesubtitle_input = "<input class='form-control' type='text' name='prefs_pagesubtitle' value='$prefs_pagesubtitle'>";
echo tpl_form_control_group('',$lang[f_prefs_pagesubtitle],$prefs_pagesubtitle_input);

echo"</fieldset>";

echo"<fieldset>";
echo"<legend>E-Mail</legend>";
$prefs_mail_name_input = "<input class='form-control' type='text' name='prefs_mailer_name' value='$prefs_mailer_name'>";
$prefs_mail_adr_input = "<input class='form-control' type='text' name='prefs_mailer_adr' value='$prefs_mailer_adr'>";
echo tpl_form_control_group('',$lang[prefs_mailer_name],$prefs_mail_name_input);
echo tpl_form_control_group('',$lang[prefs_mailer_adr],$prefs_mail_adr_input);
echo"</fieldset>";

/* User Preferences */

echo"<fieldset>";
echo"<legend>$lang[f_prefs_user]</legend>";


$toggle_btn_userregistration  = '<div class="make-switch" data-on="success" data-on-label="'.$lang['yes'].'" data-off-label="'.$lang['no'].'">';
$toggle_btn_userregistration .= '<input type="checkbox" name="prefs_userregistration" '.($prefs_userregistration == "yes" ? 'checked' :'').'>';
$toggle_btn_userregistration .= '</div>';

echo tpl_form_control_group('',$lang[f_prefs_registration],$toggle_btn_userregistration);



$toggle_btn_loginform  = '<div class="make-switch" data-on="success" data-on-label="'.$lang['yes'].'" data-off-label="'.$lang['no'].'">';
$toggle_btn_loginform .= '<input type="checkbox" name="prefs_showloginform" '.($prefs_showloginform == "yes" ? 'checked' :'').'>';
$toggle_btn_loginform .= '</div>';

echo tpl_form_control_group('',$lang[f_prefs_showloginform],$toggle_btn_loginform);


echo"</fieldset>";


/* Upload Preferences */

echo"<fieldset>";
echo"<legend>$lang[f_prefs_uploads]</legend>";

echo tpl_form_control_group('',$lang[f_prefs_imagesuffix],"<input class='form-control' type='text' name='prefs_imagesuffix' value='$prefs_imagesuffix'>");

$prefs_maximage_input  = '<div class="row"><div class="col-md-3">';
$prefs_maximage_input .= '<div class="input-group">';
$prefs_maximage_input .= '<input class="form-control" type="text" name="prefs_maximagewidth" value="'.$prefs_maximagewidth.'">';
$prefs_maximage_input .= '<span class="input-group-addon"><span class="glyphicon glyphicon-resize-horizontal"></span></span>';
$prefs_maximage_input .= '</div>';
$prefs_maximage_input .= '</div><div class="col-md-3">';
$prefs_maximage_input .= '<div class="input-group">';
$prefs_maximage_input .= '<input class="form-control" type="text" name="prefs_maximageheight" value="'.$prefs_maximageheight.'">';
$prefs_maximage_input .= '<span class="input-group-addon"><span class="glyphicon glyphicon-resize-vertical"></span></span>';
$prefs_maximage_input .= '</div>';
$prefs_maximage_input .= '</div></div>';

echo tpl_form_control_group('',$lang[f_prefs_maximage],"$prefs_maximage_input");

echo tpl_form_control_group('',$lang[f_prefs_filesuffix],"<input class='form-control' type='text' name='prefs_filesuffix' value='$prefs_filesuffix'>");
echo tpl_form_control_group('',$lang[f_prefs_maxfilesize],"<input class='form-control' type='text' name='prefs_maxfilesize' value='$prefs_maxfilesize'>");



$toggle_btn_showfilesize  = '<div class="make-switch" data-on="success" data-on-label="'.$lang['yes'].'" data-off-label="'.$lang['no'].'">';
$toggle_btn_showfilesize .= '<input type="checkbox" name="prefs_showfilesize" '.($prefs_showfilesize == "yes" ? 'checked' :'').'>';
$toggle_btn_showfilesize .= '</div>';

echo tpl_form_control_group('',$lang[f_prefs_showfilesize],$toggle_btn_showfilesize);



echo"</fieldset>";



/* Layout Preferences */

echo"<fieldset>";
echo"<legend>$lang[f_prefs_layout]</legend>";




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

echo tpl_form_control_group('',$lang[f_prefs_active_template],$select_prefs_template);


		
$select_prefs_thumbnail  = '<select name="prefs_pagethumbnail" class="form-control">';
$select_prefs_thumbnail .= '<option value="">'.$lang['page_thumbnail'].'</option>';
$arr_Images = get_all_images();
	foreach($arr_Images as $page_thumbnail) {
		$selected = "";
		if($prefs_pagethumbnail == "$page_thumbnail") {
			$selected = "selected";
		}
		$select_prefs_thumbnail .= "<option $selected value='$page_thumbnail'>$page_thumbnail</option>";
}
$select_prefs_thumbnail .= "</select>";

echo tpl_form_control_group('',$lang[page_thumbnail],$select_prefs_thumbnail);

echo"</fieldset>";


/* global header enhancement */

echo"<fieldset>";
echo"<legend>$lang[f_prefs_global_header]</legend>";


echo tpl_form_control_group('','&lt;head&gt;<br>...<br>&lt;/head&gt;',"<textarea name='prefs_pagesglobalhead' class='form-control' rows='10'>$prefs_pagesglobalhead</textarea>");


echo"</fieldset>";


echo"<fieldset>";
echo"<legend>$lang[system_misc]</legend>";


$toggle_btn_logfile  = '<div class="make-switch" data-on="success" data-on-label="'.$lang['yes'].'" data-off-label="'.$lang['no'].'">';
$toggle_btn_logfile .= '<input type="checkbox" name="prefs_logfile" '.($prefs_logfile == "on" ? 'checked' :'').'>';
$toggle_btn_logfile .= '</div>';

echo tpl_form_control_group('',$lang[activate_logfile],$toggle_btn_logfile);


$toggle_btn_sitemap  = '<div class="make-switch" data-on="success" data-on-label="'.$lang['yes'].'" data-off-label="'.$lang['no'].'">';
$toggle_btn_sitemap .= '<input type="checkbox" name="prefs_xml_sitemap" '.($prefs_xml_sitemap == "on" ? 'checked' :'').'>';
$toggle_btn_sitemap .= '</div>';

echo tpl_form_control_group('',$lang[activate_xml_sitemap],$toggle_btn_sitemap);




echo tpl_form_control_group('',$lang[rss_offset],"<input class='form-control' type='text' name='prefs_rss_time_offset' value='$prefs_rss_time_offset'>");
	 
echo"</fieldset>";




//submit form to save data
echo"<div class='formfooter'>";
echo"<input type='submit' class='btn btn-success' name='saveprefs' value='$lang[save]'>";
echo"</div>";

echo"</form>";

echo '</div></div>';

?>