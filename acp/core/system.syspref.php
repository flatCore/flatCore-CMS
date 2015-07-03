<?php

//prohibit unauthorized access
require("core/access.php");

/**
 * save the preferences
 */

foreach($_POST as $key => $val) {
	$$key = @strip_tags($val); 
}


/* save contacts */
if(isset($_POST['save_prefs_contacts'])) {
	
		$pdo_fields = array(
		'prefs_mailer_adr' => 'STR',
		'prefs_mailer_name' => 'STR'
	);
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_update_str($pdo_fields,"fc_preferences","WHERE prefs_id = 1");
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':prefs_mailer_adr', $_POST['prefs_mailer_adr'], PDO::PARAM_STR);
	$sth->bindParam(':prefs_mailer_name', $_POST['prefs_mailer_name'], PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	$dbh = null;
}

/* save descriptions */
if(isset($_POST['save_prefs_descriptions'])) {
	
		$pdo_fields = array(
		'prefs_pagetitle' => 'STR',
		'prefs_pagesubtitle' => 'STR'
	);
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_update_str($pdo_fields,"fc_preferences","WHERE prefs_id = 1");
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':prefs_pagetitle', $_POST['prefs_pagetitle'], PDO::PARAM_STR);
	$sth->bindParam(':prefs_pagesubtitle', $_POST['prefs_pagesubtitle'], PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	$dbh = null;
}


/* save user preferences */
if(isset($_POST['save_prefs_user'])) {
	
	$pdo_fields = array(
		'prefs_userregistration' => 'STR',
		'prefs_showloginform' => 'STR'
	);
	
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
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_update_str($pdo_fields,"fc_preferences","WHERE prefs_id = 1");
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':prefs_userregistration', $prefs_userregistration, PDO::PARAM_STR);
	$sth->bindParam(':prefs_showloginform', $prefs_showloginform, PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	$dbh = null;
}


/* save upload preferences */
if(isset($_POST['save_prefs_upload'])) {
	
		$pdo_fields = array(
		'prefs_imagesuffix' => 'STR',
		'prefs_maximagewidth' => 'INT',
		'prefs_maximageheight' => 'INT',
		'prefs_filesuffix' => 'STR',
		'prefs_maxfilesize' => 'INT',
		'prefs_showfilesize' => 'STR'
	);

	if(isset($_POST['prefs_showfilesize'])) {
		$prefs_showfilesize = 'yes';
	} else {
		$prefs_showfilesize = 'no';
	}	
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_update_str($pdo_fields,"fc_preferences","WHERE prefs_id = 1");
	$sth = $dbh->prepare($sql);
	generate_bindParam_str($pdo_fields,$sth);
	$sth->bindParam(':prefs_showfilesize', $prefs_showfilesize, PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	$dbh = null;
}

/* save head preferences */
if(isset($_POST['save_prefs_head'])) {
	
		$pdo_fields = array(
			'prefs_pagesglobalhead' => 'STR'
	);

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_update_str($pdo_fields,"fc_preferences","WHERE prefs_id = 1");
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':prefs_pagesglobalhead', $_POST['prefs_pagesglobalhead'], PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	$dbh = null;
}


/* save misc preferences */
if(isset($_POST['save_prefs_misc'])) {
	
	$pdo_fields = array(
		'prefs_logfile' => 'STR',
		'prefs_xml_sitemap' => 'STR',
		'prefs_rss_time_offset' => 'STR'
	);
	
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

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_update_str($pdo_fields,"fc_preferences","WHERE prefs_id = 1");
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':prefs_rss_time_offset', $_POST['prefs_rss_time_offset'], PDO::PARAM_STR);
	$sth->bindParam(':prefs_logfile', $prefs_logfile, PDO::PARAM_STR);
	$sth->bindParam(':prefs_xml_sitemap', $prefs_xml_sitemap, PDO::PARAM_STR);	
	$cnt_changes = $sth->execute();
	$dbh = null;
}


/* update labels */

if(isset($_POST['update_label'])) {
	
	$pdo_fields = array(
		'label_color' => 'STR',
		'label_title' => 'STR',
		'label_description' => 'STR'
	);
	
	$label_id = (int) $_POST['label_id'];

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_update_str($pdo_fields,"fc_labels","WHERE label_id = $label_id");
	$sth = $dbh->prepare($sql);
	generate_bindParam_str($pdo_fields,$sth);
	$cnt_changes = $sth->execute();
	$dbh = null;
	
}


/* new label */

if(isset($_POST['new_label'])) {
	
	$pdo_fields = array(
		'label_color' => 'STR',
		'label_title' => 'STR',
		'label_description' => 'STR'
	);

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_insert_str($pdo_fields,"fc_labels");
	$sth = $dbh->prepare($sql);
	generate_bindParam_str($pdo_fields,$sth);
	$cnt_changes = $sth->execute();
	$dbh = null;
	
}

/* delete label */

if(isset($_POST['delete_label'])) {

	$label_id = (int) $_POST['label_id'];

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "DELETE FROM fc_labels WHERE label_id = $label_id";
	$dbh->exec($sql);
	$dbh = null;
	
}

	/*
	if($cnt_changes == TRUE) {
		$sys_message = "{OKAY} $lang[db_changed]";
		record_log("$_SESSION[user_nick]","edit system preferences","6");
	} else {
		$sys_message = "{ERROR} $lang[db_not_changed]";
		record_log("$_SESSION[user_nick]","error on saving system preferences","11");
	}
	*/


if($sys_message != ""){
	print_sysmsg("$sys_message");
}

if(isset($_POST)) {
	/* read the preferences again */
	$fc_preferences = get_preferences();
	
	foreach($fc_preferences as $k => $v) {
	   $$k = stripslashes($v);
	}
}

/* descriptions */
echo '<fieldset>';
echo '<legend>'.$lang['f_prefs_descriptions'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref" method="POST" class="form-horizontal">';
$prefs_pagetitle_input = "<input class='form-control' type='text' name='prefs_pagetitle' value='$prefs_pagetitle'>";
echo tpl_form_control_group('',$lang['f_prefs_pagetitle'],$prefs_pagetitle_input);
$prefs_pagesubtitle_input = "<input class='form-control' type='text' name='prefs_pagesubtitle' value='$prefs_pagesubtitle'>";
echo tpl_form_control_group('',$lang['f_prefs_pagesubtitle'],$prefs_pagesubtitle_input);
echo tpl_form_control_group('','',"<input type='submit' class='btn btn-success' name='save_prefs_descriptions' value='$lang[save]'>");
echo '</form>';
echo '</fieldset>';

/* contacts */

echo"<fieldset>";
echo"<legend>System E-Mail</legend>";
echo '<form action="acp.php?tn=system&sub=sys_pref" method="POST" class="form-horizontal">';
$prefs_mail_name_input = "<input class='form-control' type='text' name='prefs_mailer_name' value='$prefs_mailer_name'>";
$prefs_mail_adr_input = "<input class='form-control' type='text' name='prefs_mailer_adr' value='$prefs_mailer_adr'>";
echo tpl_form_control_group('',$lang['prefs_mailer_name'],$prefs_mail_name_input);
echo tpl_form_control_group('',$lang['prefs_mailer_adr'],$prefs_mail_adr_input);
echo tpl_form_control_group('','',"<input type='submit' class='btn btn-success' name='save_prefs_contacts' value='$lang[save]'>");
echo '</form>';
echo"</fieldset>";


/* User Preferences */
echo '<fieldset>';
echo '<legend>'.$lang['f_prefs_user'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref" method="POST" class="form-horizontal">';

$toggle_btn_userregistration  = '<div class="make-switch" data-on="success" data-on-label="'.$lang['yes'].'" data-off-label="'.$lang['no'].'">';
$toggle_btn_userregistration .= '<input type="checkbox" name="prefs_userregistration" '.($prefs_userregistration == "yes" ? 'checked' :'').'>';
$toggle_btn_userregistration .= '</div>';
echo tpl_form_control_group('',$lang['f_prefs_registration'],$toggle_btn_userregistration);

$toggle_btn_loginform  = '<div class="make-switch" data-on="success" data-on-label="'.$lang['yes'].'" data-off-label="'.$lang['no'].'">';
$toggle_btn_loginform .= '<input type="checkbox" name="prefs_showloginform" '.($prefs_showloginform == "yes" ? 'checked' :'').'>';
$toggle_btn_loginform .= '</div>';
echo tpl_form_control_group('',$lang['f_prefs_showloginform'],$toggle_btn_loginform);

echo tpl_form_control_group('','',"<input type='submit' class='btn btn-success' name='save_prefs_user' value='$lang[save]'>");

echo '</form>';
echo '</fieldset>';



/* Upload Preferences */
echo '<fieldset>';
echo '<legend>'.$lang['f_prefs_uploads'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref" method="POST" class="form-horizontal">';

echo tpl_form_control_group('',$lang['f_prefs_imagesuffix'],"<input class='form-control' type='text' name='prefs_imagesuffix' value='$prefs_imagesuffix'>");

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

echo tpl_form_control_group('',$lang['f_prefs_maximage'],"$prefs_maximage_input");
echo tpl_form_control_group('',$lang['f_prefs_filesuffix'],"<input class='form-control' type='text' name='prefs_filesuffix' value='$prefs_filesuffix'>");
echo tpl_form_control_group('',$lang['f_prefs_maxfilesize'],"<input class='form-control' type='text' name='prefs_maxfilesize' value='$prefs_maxfilesize'>");

$toggle_btn_showfilesize  = '<div class="make-switch" data-on="success" data-on-label="'.$lang['yes'].'" data-off-label="'.$lang['no'].'">';
$toggle_btn_showfilesize .= '<input type="checkbox" name="prefs_showfilesize" '.($prefs_showfilesize == "yes" ? 'checked' :'').'>';
$toggle_btn_showfilesize .= '</div>';

echo tpl_form_control_group('',$lang['f_prefs_showfilesize'],$toggle_btn_showfilesize);
echo tpl_form_control_group('','',"<input type='submit' class='btn btn-success' name='save_prefs_upload' value='$lang[save]'>");

echo '</form>';
echo '</fieldset>';


/* global header enhancement */
echo '<fieldset>';
echo '<legend>'.$lang['f_prefs_global_header'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref" method="POST" class="form-horizontal">';

$ta_pagesglobalhead = '<textarea name="prefs_pagesglobalhead" class="aceEditor_html form-control">'.$prefs_pagesglobalhead.'</textarea>';
$ta_pagesglobalhead .= '<div id="HTMLeditor"></div>';
echo tpl_form_control_group('','&lt;head&gt;<br>...<br>&lt;/head&gt;',"$ta_pagesglobalhead");
echo tpl_form_control_group('','',"<input type='submit' class='btn btn-success' name='save_prefs_head' value='$lang[save]'>");

echo '</form>';
echo '</fieldset>';


/* misc */
echo"<fieldset>";
echo '<legend>'.$lang['system_misc'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref" method="POST" class="form-horizontal">';

$toggle_btn_logfile  = '<div class="make-switch" data-on="success" data-on-label="'.$lang['yes'].'" data-off-label="'.$lang['no'].'">';
$toggle_btn_logfile .= '<input type="checkbox" name="prefs_logfile" '.($prefs_logfile == "on" ? 'checked' :'').'>';
$toggle_btn_logfile .= '</div>';
echo tpl_form_control_group('',$lang['activate_logfile'],$toggle_btn_logfile);

$toggle_btn_sitemap  = '<div class="make-switch" data-on="success" data-on-label="'.$lang['yes'].'" data-off-label="'.$lang['no'].'">';
$toggle_btn_sitemap .= '<input type="checkbox" name="prefs_xml_sitemap" '.($prefs_xml_sitemap == "on" ? 'checked' :'').'>';
$toggle_btn_sitemap .= '</div>';
echo tpl_form_control_group('',$lang['activate_xml_sitemap'],$toggle_btn_sitemap);

echo tpl_form_control_group('',$lang['rss_offset'],"<input class='form-control' type='text' name='prefs_rss_time_offset' value='$prefs_rss_time_offset'>");
echo tpl_form_control_group('','',"<input type='submit' class='btn btn-success' name='save_prefs_misc' value='$lang[save]'>");

echo '</form>';
echo '</fieldset>';


/* Labels */

echo"<fieldset>";
echo"<legend>Labels</legend>";

$fc_labels = fc_get_labels();
$cnt_labels = count($fc_labels);


echo '<div class="row">';
for($i=0;$i<$cnt_labels;$i++) {
	echo '<form action="acp.php?tn=system&sub=sys_pref#labels" method="POST" class="clearfix" id="labels">';
	echo '<div class="col-md-2">';
	echo '<div class="input-group">';
	echo '<span class="input-group-addon" id="basic-addon1" style="background-color:'.$fc_labels[$i]['label_color'].'"></span>';
	echo '<input class="form-control" type="text" name="label_color" value="'.$fc_labels[$i]['label_color'].'">';
	echo '</div>';
	echo '</div>';
	echo '<div class="col-md-3">';
	echo '<input class="form-control" type="text" name="label_title" value="'.$fc_labels[$i]['label_title'].'">';
	echo '</div>';
	echo '<div class="col-md-5">';
	echo '<input class="form-control" type="text" name="label_description" value="'.$fc_labels[$i]['label_description'].'">';
	echo '</div>';
	echo '<div class="col-md-2">';
	echo '<input type="hidden" name="label_id" value="'.$fc_labels[$i]['label_id'].'">';
	echo '<div class="btn-group" role="group">';
	echo '<button type="submit" name="update_label" class="btn btn-default"><span class="glyphicon glyphicon-refresh"></span></button>';
	echo '<button type="submit" name="delete_label" class="btn btn-danger"><span class="glyphicon glyphicon glyphicon-trash"></span></button>';
	echo '</div>';
	echo '</div>';
	echo '</form><br>';
}
echo '</div><br>';

echo '<form action="acp.php?tn=system&sub=sys_pref#labels" method="POST" class="form-horizontal">';
echo '<div class="row">';
echo '<div class="col-md-2">';
echo $lang['label_color'];
echo '<div class="input-group">';
echo '<span class="input-group-addon" id="basic-addon1" style="background-color:'.$fc_labels[$i]['label_color'].'"></span>';
echo '<input class="form-control" type="text" name="label_color" value="" placeholder="#3366cc">';
echo '</div>';
echo '</div>';
echo '<div class="col-md-3">';
echo $lang['label_title'];
echo '<input class="form-control" type="text" name="label_title" value="">';
echo '</div>';
echo '<div class="col-md-5">';
echo $lang['label_description'];
echo '<input class="form-control" type="text" name="label_description" value="">';
echo '</div>';
echo '<div class="col-md-2">';
echo '<br><button type="submit" name="new_label" class="btn btn-success">'.$lang['save'].'</button>';
echo '</div>';
echo '</div>';
echo '</form>';



echo"</fieldset>";




?>