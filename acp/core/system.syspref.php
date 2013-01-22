<?php

//prohibit unauthorized access
require("core/access.php");



/**
 * save the preferences
 */

if($_POST[saveprefs]) {

	// all incoming data -> strip_tags
	foreach($_POST as $key => $val) {
		$$key = @strip_tags($val); 
		}
	
	// template
	$select_template = explode("<|-|>", $_POST[select_template]);
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
		'prefs_xml_sitemap' => 'STR'
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

echo"<form action='$_SERVER[PHP_SELF]?tn=system&sub=sys_pref' method='POST' class='form-horizontal'>";

/* page preferences */

echo"<fieldset>";

echo"<legend>$lang[f_prefs_descriptions]</legend>";

echo '<div class="form-line">';
echo '<label>' . $lang[f_prefs_pagetitle] .'</label>';
echo '<div class="form-controls">';
echo "<input class='input-block-level' type='text' name='prefs_pagetitle' value='$prefs_pagetitle'>";
echo '</div>';
echo '</div>';

echo '<div class="form-line">';
echo '<label>' . $lang[f_prefs_pagesubtitle] .'</label>';
echo '<div class="form-controls">';
echo "<input class='span6' type='text' name='prefs_pagesubtitle' value='$prefs_pagesubtitle'>";
echo '</div>';
echo '</div>';


echo"</fieldset>";





/* User Preferences */

echo"<fieldset>";
echo"<legend>$lang[f_prefs_user]</legend>";


echo '<div class="form-line">';
echo '<label>' . $lang[f_prefs_registration] .'</label>';
echo '<div class="form-controls">';
		
if($prefs_userregistration == "yes") {
	$checked_ur_yes = "selected";
} else {
	$checked_ur_no = "selected";
}

echo '<select name="prefs_userregistration" class="span2">';
	echo"<option $checked_ur_yes value='yes'>$lang[yes]</option>";
	echo"<option $checked_ur_no value='no'>$lang[no]</option>";
echo '</select>';

echo '</div>';
echo '</div>';

	 
	 

echo"<div class='form-line'>
		<label>$lang[f_prefs_showloginform]</label>
		<div class='form-controls'>";
		
if($prefs_showloginform == "yes") {
	$checked_lf_yes = "selected";
} else {
	$checked_lf_no = "selected";
}

echo '<select name="prefs_showloginform" class="span2">';
	echo"<option $checked_lf_yes value='yes'>$lang[yes]</option>";
	echo"<option $checked_lf_no value='no'>$lang[no]</option>";
echo '</select>';

echo '</div>';
echo '</div>';

echo"</fieldset>";






/* Upload Preferences */


echo"<fieldset>";
echo"<legend>$lang[f_prefs_uploads]</legend>";


echo"<div class='form-line'>
		<label>$lang[f_prefs_imagesuffix]</label>
		<div class='form-controls'><input class='span6' type='text' name='prefs_imagesuffix' value='$prefs_imagesuffix'></div>
	 </div>";
	 
echo"<div class='form-line'>
		<label>$lang[f_prefs_maximage]</label>
		<div class='form-controls'>
		<input class='span2' type='text' name='prefs_maximagewidth' value='$prefs_maximagewidth'> x	<input class='span2' type='text' name='prefs_maximageheight' value='$prefs_maximageheight'>
		</div>
	 </div>";
	 
echo"<div class='form-line'>
		<label>$lang[f_prefs_filesuffix]</label>
		<div class='form-controls'><input class='span6' type='text' name='prefs_filesuffix' value='$prefs_filesuffix'></div>
	 </div>";
	 
echo"<div class='form-line'>
		<label>$lang[f_prefs_maxfilesize]</label>
		<div class='form-controls'><input class='span6' type='text' name='prefs_maxfilesize' value='$prefs_maxfilesize'></div>
	 </div>";


echo"<div class='form-line'>
		<label>$lang[f_prefs_showfilesize]</label>
		<div class='form-controls'>";
			 
if($prefs_showfilesize == "yes") {
	$checked_sfs_yes = "selected";
} else {
	$checked_sfs_no = "selected";
}

echo"<select name='prefs_showfilesize'>";
	echo"<option $checked_sfs_yes value='yes'>$lang[yes]</option>";
	echo"<option $checked_sfs_no value='no'>$lang[no]</option>";
echo"</select>";

echo"</div>
	 </div>";
	 



echo"</fieldset>";





/* Layout Preferences */


echo"<fieldset>";
echo"<legend>$lang[f_prefs_layout]</legend>";


echo"<div class='form-line'>
		<label>$lang[f_prefs_active_template]</label>
		<div class='form-controls'>";

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
    



} // eo foreach template list

echo"</select>";



echo"</div>
	 </div>";


echo"</fieldset>";




/* global header enhancement */

echo"<fieldset>";
echo"<legend>$lang[f_prefs_global_header]</legend>";

echo"<div class='form-line'>
			<label>&lt;head&gt;<br>...<br>&lt;/head&gt;</label>";

echo"<div class='form-controls'>
		<textarea name='prefs_pagesglobalhead' class='span6' rows='10'>$prefs_pagesglobalhead</textarea>
		</div>";
		
echo"</div>";


echo"</fieldset>";




/* prefs_logfile and prefs_xml_sitemap */

echo"<fieldset>";
echo"<legend>$lang[system_statistics]</legend>";


echo"<div class='form-line'>
		<label>$lang[activate_logfile]</label>
		<div class='form-controls'>";
		
if($prefs_logfile == "on") {
	$selected_on = "selected";
} else {
	$selected_off = "selected";
}

echo"<select name='prefs_logfile'>";
	echo"<option $selected_on value='on'>$lang[yes]</option>";
	echo"<option $selected_off value='off'>$lang[no]</option>";
echo"</select>";

echo"</div>
	 </div>";
	 
	 
	 
	 
	 
	 
echo"<div class='form-line'>
		<label>$lang[activate_xml_sitemap]</label>
		<div class='form-controls'>";
		
if($prefs_xml_sitemap == "on") {
	$sel_xml_on = "selected";
} else {
	$sel_xml_off = "selected";
}

echo"<select name='prefs_xml_sitemap'>";
	echo"<option $sel_xml_on value='on'>$lang[yes]</option>";
	echo"<option $sel_xml_off value='off'>$lang[no]</option>";
echo"</select>";

echo"</div>
	 </div>";
	 
echo"</fieldset>";





	


//submit form to save data
echo"<div class='formfooter'>";
echo"<input type='submit' class='btn btn-success' name='saveprefs' value='$lang[save]'>";
echo"</div>";

echo"</form>";
?>