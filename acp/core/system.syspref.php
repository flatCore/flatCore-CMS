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

echo '<div class="row-fluid"><div class="span12">';

echo"<form action='$_SERVER[PHP_SELF]?tn=system&sub=sys_pref' method='POST' class='form-horizontal'>";

/* page preferences */

echo"<fieldset>";

echo"<legend>$lang[f_prefs_descriptions]</legend>";


$prefs_pagetitle_input = "<input class='span10' type='text' name='prefs_pagetitle' value='$prefs_pagetitle'>";
echo tpl_form_control_group('',$lang[f_prefs_pagetitle],$prefs_pagetitle_input);

$prefs_pagesubtitle_input = "<input class='span10' type='text' name='prefs_pagesubtitle' value='$prefs_pagesubtitle'>";
echo tpl_form_control_group('',$lang[f_prefs_pagesubtitle],$prefs_pagesubtitle_input);

echo"</fieldset>";

echo"<fieldset>";
echo"<legend>E-Mail</legend>";
$prefs_mail_name_input = "<input class='span10' type='text' name='prefs_mailer_name' value='$prefs_mailer_name'>";
$prefs_mail_adr_input = "<input class='span10' type='text' name='prefs_mailer_adr' value='$prefs_mailer_adr'>";
echo tpl_form_control_group('',$lang[prefs_mailer_name],$prefs_mail_name_input);
echo tpl_form_control_group('',$lang[prefs_mailer_adr],$prefs_mail_adr_input);
echo"</fieldset>";

/* User Preferences */

echo"<fieldset>";
echo"<legend>$lang[f_prefs_user]</legend>";

$select_prefs_userregistration  = '<select name="prefs_userregistration" class="span3">';
$select_prefs_userregistration .= '<option value="yes" '.($prefs_userregistration == "yes" ? 'selected="selected"' :'').'>'.$lang[yes].'</option>';	
$select_prefs_userregistration .= '<option value="no" '.($prefs_userregistration == "no" ? 'selected="selected"' :'').'>'.$lang[no].'</option>';	
$select_prefs_userregistration .= '</select>';
echo tpl_form_control_group('',$lang[f_prefs_registration],$select_prefs_userregistration);

$select_prefs_showloginform  = '<select name="prefs_showloginform" class="span3">';
$select_prefs_showloginform .= '<option value="yes" '.($prefs_showloginform == "yes" ? 'selected="selected"' :'').'>'.$lang[yes].'</option>';	
$select_prefs_showloginform .= '<option value="no" '.($prefs_showloginform == "no" ? 'selected="selected"' :'').'>'.$lang[no].'</option>';
$select_prefs_showloginform .= '</select>';
echo tpl_form_control_group('',$lang[f_prefs_showloginform],$select_prefs_showloginform);


echo"</fieldset>";


/* Upload Preferences */

echo"<fieldset>";
echo"<legend>$lang[f_prefs_uploads]</legend>";

echo tpl_form_control_group('',$lang[f_prefs_imagesuffix],"<input class='span6' type='text' name='prefs_imagesuffix' value='$prefs_imagesuffix'>");
echo tpl_form_control_group('',$lang[f_prefs_maximage],"<input class='span2' type='text' name='prefs_maximagewidth' value='$prefs_maximagewidth'> x	<input class='span2' type='text' name='prefs_maximageheight' value='$prefs_maximageheight'>");

echo tpl_form_control_group('',$lang[f_prefs_filesuffix],"<input class='span6' type='text' name='prefs_filesuffix' value='$prefs_filesuffix'>");
echo tpl_form_control_group('',$lang[f_prefs_maxfilesize],"<input class='span6' type='text' name='prefs_maxfilesize' value='$prefs_maxfilesize'>");


$select_prefs_showfilesize  = '<select name="prefs_showfilesize" class="span3">';
$select_prefs_showfilesize .= '<option value="yes" '.($prefs_showfilesize == "yes" ? 'selected="selected"' :'').'>'.$lang[yes].'</option>';	
$select_prefs_showfilesize .= '<option value="no" '.($prefs_showfilesize == "no" ? 'selected="selected"' :'').'>'.$lang[no].'</option>';
$select_prefs_showfilesize .= '</select>';
echo tpl_form_control_group('',$lang[f_prefs_showfilesize],$select_prefs_showfilesize);


echo"</fieldset>";



/* Layout Preferences */

echo"<fieldset>";
echo"<legend>$lang[f_prefs_layout]</legend>";


echo"<div class='control-group'>
		<label class='control-label'>$lang[f_prefs_active_template]</label>
		<div class='controls'>";

$arr_Styles = get_all_templates();

echo"<select name='select_template'>";


/* templates list */
foreach($arr_Styles as $template) {

	$arr_layout_tpl = glob("../styles/$template/templates/layout*.tpl");
	
	echo"<optgroup label='$template'>";
	
	foreach($arr_layout_tpl as $layout_tpl) {
		$layout_tpl = basename($layout_tpl);
		
		$selected = "";
		if($template == "$prefs_template" && $layout_tpl == "$prefs_template_layout") {
			$selected = "selected";
		}
		echo "<option $selected value='$template<|-|>$layout_tpl'>$template » $layout_tpl</option>";
	}
	echo"</optgroup>";

}

echo"</select>";



echo"</div>
	 </div>";
	 
echo"<div class='control-group'>
		<label class='control-label'>$lang[page_thumbnail]</label>
		<div class='controls'>";
		
		
echo"<select name='prefs_pagethumbnail'>";
echo "<option value=''>$lang[page_thumbnail]</option>";
$arr_Images = get_all_images();
	foreach($arr_Images as $page_thumbnail) {
		$selected = "";
		if($prefs_pagethumbnail == "$page_thumbnail") {
			$selected = "selected";
		}
		echo "<option $selected value='$page_thumbnail'>$page_thumbnail</option>";
}
echo"</select>";
echo"</div>
	 </div>";

echo"</fieldset>";


/* global header enhancement */

echo"<fieldset>";
echo"<legend>$lang[f_prefs_global_header]</legend>";


echo tpl_form_control_group('','&lt;head&gt;<br>...<br>&lt;/head&gt;',"<textarea name='prefs_pagesglobalhead' class='span12' rows='10'>$prefs_pagesglobalhead</textarea>");


echo"</fieldset>";


echo"<fieldset>";
echo"<legend>$lang[system_misc]</legend>";

$select_prefs_logfile  = '<select name="prefs_logfile" class="span3">';
$select_prefs_logfile .= '<option value="on" '.($prefs_logfile == "on" ? 'selected="selected"' :'').'>'.$lang[yes].'</option>';	
$select_prefs_logfile .= '<option value="off" '.($prefs_logfile == "off" ? 'selected="selected"' :'').'>'.$lang[no].'</option>';
$select_prefs_logfile .= '</select>';
echo tpl_form_control_group('',$lang[activate_logfile],$select_prefs_logfile);


$select_prefs_xml_sitemap  = '<select name="prefs_xml_sitemap" class="span3">';
$select_prefs_xml_sitemap .= '<option value="on" '.($prefs_xml_sitemap == "on" ? 'selected="selected"' :'').'>'.$lang[yes].'</option>';	
$select_prefs_xml_sitemap .= '<option value="off" '.($prefs_xml_sitemap == "off" ? 'selected="selected"' :'').'>'.$lang[no].'</option>';	
$select_prefs_xml_sitemap .= '</select>';
echo tpl_form_control_group('',$lang[activate_xml_sitemap],$select_prefs_xml_sitemap);

echo tpl_form_control_group('',$lang[rss_offset],"<input class='span6' type='text' name='prefs_rss_time_offset' value='$prefs_rss_time_offset'>");
	 
echo"</fieldset>";




//submit form to save data
echo"<div class='formfooter'>";
echo"<input type='submit' class='btn btn-success' name='saveprefs' value='$lang[save]'>";
echo"</div>";

echo"</form>";

echo '</div></div>';

?>