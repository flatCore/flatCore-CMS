<?php

//prohibit unauthorized access
require 'core/access.php';

/**
 * save the preferences
 */

foreach($_POST as $key => $val) {
	$$key = @htmlspecialchars($val, ENT_QUOTES); 
}




/* save descriptions */
if(isset($_POST['save_prefs_descriptions'])) {
	
	if(isset($_POST['prefs_publisher_mode'])) {
		$prefs_publisher_mode = 'overwrite';
	} else {
		$prefs_publisher_mode = 'no';
	}
	
	$data = $db_content->update("fc_preferences", [
		"prefs_pagename" =>  $prefs_pagename,
		"prefs_pagedescription" => $prefs_pagedescription,
		"prefs_pagetitle" => $prefs_pagetitle,
		"prefs_pagesubtitle" => $prefs_pagesubtitle,
		"prefs_default_publisher" => $prefs_default_publisher,
		"prefs_publisher_mode" => $prefs_publisher_mode
	], [
	"prefs_id" => 1
	]);
	
}

/* save system settings */

if(isset($_POST['save_system'])) {

	$data = $db_content->update("fc_preferences", [
		"prefs_cms_domain" =>  $prefs_cms_domain,
		"prefs_cms_ssl_domain" => $prefs_cms_ssl_domain,
		"prefs_cms_base" => $prefs_cms_base
	], [
	"prefs_id" => 1
	]);

}

/* save date/time settings */

if(isset($_POST['save_datetime'])) {

	$data = $db_content->update("fc_preferences", [
		"prefs_timezone" =>  $prefs_timezone,
		"prefs_dateformat" => $prefs_dateformat,
		"prefs_timeformat" => $prefs_timeformat
	], [
	"prefs_id" => 1
	]);

}




/* save user preferences */
if(isset($_POST['save_prefs_user'])) {
	
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

	$data = $db_content->update("fc_preferences", [
		"prefs_userregistration" =>  $prefs_userregistration,
		"prefs_showloginform" => $prefs_showloginform
	], [
	"prefs_id" => 1
	]);
	
}


/* save head preferences */
if(isset($_POST['save_prefs_head'])) {
		
	$data = $db_content->update("fc_preferences", [
		"prefs_pagesglobalhead" =>  $_POST['prefs_pagesglobalhead']
	], [
	"prefs_id" => 1
	]);	
	
}

/* save deleted resources */
if(isset($_POST['save_deleted_resources'])) {
		
	$data = $db_content->update("fc_preferences", [
			"prefs_deleted_resources" =>  $_POST['prefs_deleted_resources']
		], [
		"prefs_id" => 1
		]);
}


/* save misc preferences */
if(isset($_POST['save_prefs_misc'])) {
	
	if(isset($_POST['prefs_logfile'])) {
		$prefs_logfile = 'on';
	} else {
		$prefs_logfile = 'off';
	}
	
	if(isset($_POST['prefs_anonymize_ip'])) {
		$prefs_anonymize_ip = 'on';
	} else {
		$prefs_anonymize_ip = 'off';
	}
	
	if(isset($_POST['prefs_xml_sitemap'])) {
		$prefs_xml_sitemap = 'on';
	} else {
		$prefs_xml_sitemap = 'off';
	}
	
	if(isset($_POST['prefs_smarty_cache'])) {
		$prefs_smarty_cache = 1;
	} else {
		$prefs_smarty_cache = 0;
	}
	
	if(isset($_POST['prefs_smarty_compile_check'])) {
		$prefs_smarty_compile_check = 1;
	} else {
		$prefs_smarty_compile_check = 0;
	}
	
	$data = $db_content->update("fc_preferences", [
			"prefs_rss_time_offset" =>  $_POST['prefs_rss_time_offset'],
			"prefs_acp_session_lifetime" =>  $_POST['prefs_acp_session_lifetime'],
			"prefs_logfile" =>  $prefs_logfile,
			"prefs_anonymize_ip" =>  $prefs_anonymize_ip,
			"prefs_xml_sitemap" =>  $prefs_xml_sitemap,
			"prefs_nbr_page_versions" =>  $prefs_nbr_page_versions,
			"prefs_pagesort_minlength" => $prefs_pagesort_minlength,
			"prefs_smarty_cache" =>  $prefs_smarty_cache,
			"prefs_smarty_cache_lifetime" =>  $_POST['prefs_smarty_cache_lifetime'],
			"prefs_smarty_compile_check" =>  $prefs_smarty_compile_check
		], [
		"prefs_id" => 1
		]);
	
}

/* delete smarty cache files */

if(isset($_POST['delete_smarty_cache'])) {
	fc_delete_smarty_cache('all');
}





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


/* forms */

echo '<div class="row">';
echo '<div class="col-md-9">';

/* descriptions */
echo '<div id="descriptions" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>'.$lang['f_prefs_descriptions'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref" method="POST" class="form-horizontal">';

$prefs_pagename_input = "<input class='form-control' type='text' name='prefs_pagename' value='$prefs_pagename'>";
echo tpl_form_control_group('',$lang['f_prefs_pagename'],$prefs_pagename_input);

$prefs_pagetitle_input = "<input class='form-control' type='text' name='prefs_pagetitle' value='$prefs_pagetitle'>";
echo tpl_form_control_group('',$lang['f_prefs_pagetitle'],$prefs_pagetitle_input);

$prefs_pagesubtitle_input = "<input class='form-control' type='text' name='prefs_pagesubtitle' value='$prefs_pagesubtitle'>";
echo tpl_form_control_group('',$lang['f_prefs_pagesubtitle'],$prefs_pagesubtitle_input);

$prefs_pagedescription_input = "<textarea class='form-control' name='prefs_pagedescription'>$prefs_pagedescription</textarea>";
echo tpl_form_control_group('',$lang['f_prefs_pagedescription'],$prefs_pagedescription_input);

echo '<hr>';


$toggle_btn_publisher .= '<span class="input-group-text">'.$lang['f_prefs_publisher_mode'].'</span>';
$toggle_btn_publisher .= '<span class="input-group-text">';
$toggle_btn_publisher .= '<input type="checkbox" name="prefs_publisher_mode" '.($prefs_publisher_mode == "overwrite" ? 'checked' :'').'>';
$toggle_btn_publisher .= '</span>';


$prefs_publisher_input  = '<div class="input-group">';
$prefs_publisher_input .= '<input class="form-control" type="text" name="prefs_default_publisher" value="'.$prefs_default_publisher.'">';
$prefs_publisher_input .= $toggle_btn_publisher;
$prefs_publisher_input .= '</div>';

echo tpl_form_control_group('',$lang['f_prefs_default_publisher'],$prefs_publisher_input);


echo tpl_form_control_group('','',"<input type='submit' class='btn btn-save' name='save_prefs_descriptions' value='$lang[save]'>");

echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';
echo '</fieldset>';



/* system */
echo '<div id="system" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>System</legend>';

$prefs_cms_domain_input = "<input class='form-control' type='text' name='prefs_cms_domain' value='$prefs_cms_domain'>";
$prefs_cms_ssl_domain_input = "<input class='form-control' type='text' name='prefs_cms_ssl_domain' value='$prefs_cms_ssl_domain'>";
$prefs_cms_base_input = "<input class='form-control' type='text' name='prefs_cms_base' value='$prefs_cms_base'>";

echo '<form action="acp.php?tn=system&sub=sys_pref#system" method="POST" class="form-horizontal">';
echo tpl_form_control_group('',$lang['prefs_cms_domain'],$prefs_cms_domain_input);
echo tpl_form_control_group('',$lang['prefs_cms_ssl_domain'],$prefs_cms_ssl_domain_input);
echo tpl_form_control_group('',$lang['prefs_cms_base'],$prefs_cms_base_input);
echo tpl_form_control_group('','',"<input type='submit' class='btn btn-save' name='save_system' value='$lang[save]'>");
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';
echo '</fieldset>';

$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

/* date and time settings */
echo '<div id="date_time" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>'.$lang['label_datetime_settings'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref#date_time" method="POST">';

echo '<div class="form-group">';
echo '<label>'.$lang['label_datetime_timezone'].'</label>';
echo '<select class="form-control" name="prefs_timezone">';
$x=0;
foreach($timezones as $key => $value) {
	
	
	if(strpos($value,'/') !== false) {
		$region[$x] = substr($value,0,strpos($value,'/'));
		$location = substr($value, strpos($value,'/')+1);
	} else {
		$region[$x] = 'Other';
		$location = $value;
	}

	
	$s_optgroup = '';
	$e_optgroup = '';
	
	if(($region[$x] != $region[$x-1]) OR ($x==0)) {
		$s_optgroup = '<optgroup label="'.$region[$x].'">';
		$cnt_opt = $x;
	}
	
	if(($region[$x] != $region[$x-1]) AND ($x != $cnt_opt)) {
		$e_optgroup = '</optgroup>';
	}
	
	$selected = '';
	if($prefs_timezone == $value) {
		$selected = 'selected';
	}
	
	echo $s_optgroup;
	echo '<option value="'.$value.'" '.$selected.'>'.$location.'</option>';
	echo $e_optgroup;
	
	$x++;
	
}
echo '</select>';
echo '</div>';


$date_formats = array("Y-m-d","d.m.Y","d/m/Y","m/d/Y");

echo '<div class="form-group">';
echo '<label>'.$lang['label_datetime_dateformat'].'</label>';
echo '<select class="form-control" name="prefs_dateformat">';

foreach($date_formats as $dates) {
	
	$selected = '';
	if($prefs_dateformat == $dates) {
		$selected = 'selected';
	}
	
	echo '<option value="'.$dates.'" '.$selected.'>'.date("$dates",time()).' ('.$dates.')</option>';
}


echo '</select>';
echo '</div>';

$time_formats = array("H:i","g:i a","g:i A");

echo '<div class="form-group">';
echo '<label>'.$lang['label_datetime_timeformat'].'</label>';
echo '<select class="form-control" name="prefs_timeformat">';

foreach($time_formats as $times) {

	$selected = '';
	if($prefs_timeformat == $times) {
		$selected = 'selected';
	}

	echo '<option value="'.$times.'" '.$selected.'>'.date("$times",time()).' ('.$times.')</option>';
}


echo '</select>';
echo '</div>';



echo '<input type="submit" class="btn btn-save" name="save_datetime" value="'.$lang['save'].'">';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';
echo '</fieldset>';

/* User Preferences */
echo '<div id="user" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>'.$lang['f_prefs_user'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref#user" method="POST" class="form-horizontal">';


echo '<div class="form-group form-check mt-3">';
echo '<input type="checkbox" class="form-check-input" id="userregister" name="prefs_userregistration" '.($prefs_userregistration == "yes" ? 'checked' :'').'>';
echo '<label class="form-check-label" for="userregister">'.$lang['f_prefs_registration'].'</label>';
echo '</div>';


echo '<div class="form-group form-check mt-3">';
echo '<input type="checkbox" class="form-check-input" id="loginform" name="prefs_showloginform" '.($prefs_showloginform == "yes" ? 'checked' :'').'>';
echo '<label class="form-check-label" for="loginform">'.$lang['f_prefs_showloginform'].'</label>';
echo '</div>';

echo '<input type="submit" class="btn btn-save" name="save_prefs_user" value="'.$lang['save'].'">';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';
echo '</fieldset>';



/* global header enhancement */
echo '<div id="globalheader" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>'.$lang['f_prefs_global_header'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref#globalheader" method="POST" class="form-horizontal">';

$ta_pagesglobalhead = '<textarea name="prefs_pagesglobalhead" class="aceEditor_html form-control">'.$prefs_pagesglobalhead.'</textarea>';
$ta_pagesglobalhead .= '<div id="HTMLeditor"></div>';
echo tpl_form_control_group('','&lt;head&gt;<br>...<br>&lt;/head&gt;',"$ta_pagesglobalhead");
echo tpl_form_control_group('','',"<input type='submit' class='btn btn-save' name='save_prefs_head' value='$lang[save]'>");
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';
echo '</fieldset>';

/* deleted resources */
echo '<div id="deletedresources" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>'.$lang['label_deleted_resources'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref#deletedresources" method="POST" class="form-horizontal">';


echo tpl_form_control_group('','410 GONE','<textarea name="prefs_deleted_resources" rows="10" class="form-control">'.$prefs_deleted_resources.'</textarea>');
echo tpl_form_control_group('','','<input type="submit" class="btn btn-save" name="save_deleted_resources" value="'.$lang['save'].'">');
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';
echo '</fieldset>';


/* misc */
echo '<div id="misc" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>'.$lang['system_misc'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref#misc" method="POST" class="form-horizontal">';

echo '<div class="form-group form-check mt-3">';
echo '<input type="checkbox" class="form-check-input" id="logfile" name="prefs_logfile" '.($prefs_logfile == "on" ? 'checked' :'').'>';
echo '<label class="form-check-label" for="logfile">'.$lang['activate_logfile'].'</label>';
echo '</div>';

echo '<div class="form-group form-check mt-3">';
echo '<input type="checkbox" class="form-check-input" id="ips" name="prefs_anonymize_ip" '.($prefs_anonymize_ip == "on" ? 'checked' :'').'>';
echo '<label class="form-check-label" for="ips">'.$lang['anonymize_ip'].'</label>';
echo '</div>';




echo '<div class="form-group form-check mt-3">';
echo '<input type="checkbox" class="form-check-input" id="sitemap" name="prefs_xml_sitemap" '.($prefs_xml_sitemap == "on" ? 'checked' :'').'>';
echo '<label class="form-check-label" for="sitemap">'.$lang['activate_xml_sitemap'].'</label>';
echo '</div>';



echo tpl_form_control_group('',$lang['rss_offset'],"<input class='form-control' type='text' name='prefs_rss_time_offset' value='$prefs_rss_time_offset'>");
echo tpl_form_control_group('',$lang['acp_session_lifetime'],"<input class='form-control' type='text' name='prefs_acp_session_lifetime' value='$prefs_acp_session_lifetime'>");


echo tpl_form_control_group('',$lang['prefs_nbr_page_versions'],"<input class='form-control' type='text' name='prefs_nbr_page_versions' value='$prefs_nbr_page_versions'>");

echo tpl_form_control_group('',$lang['prefs_pagesort_minlength'],"<input class='form-control' type='text' name='prefs_pagesort_minlength' value='$prefs_pagesort_minlength'>");

echo '<hr>';


echo '<div class="form-group form-check mt-3">';
echo '<input type="checkbox" class="form-check-input" id="cache" name="prefs_smarty_cache" '.($prefs_smarty_cache == "1" ? 'checked' :'').'>';
echo '<label class="form-check-label" for="cache">'.$lang['cache'].'</label>';
echo '</div>';


echo '<div class="form-group form-check mt-3">';
echo '<input type="checkbox" class="form-check-input" id="compile" name="prefs_smarty_compile_check" '.($prefs_smarty_compile_check == "1" ? 'checked' :'').'>';
echo '<label class="form-check-label" for="compile">'.$lang['compile_check'].'</label>';
echo '</div>';


$cache_size = fc_dir_size('../'.FC_CONTENT_DIR.'/cache/cache/');
$compile_size = fc_dir_size('../'.FC_CONTENT_DIR.'/cache/templates_c/');
$complete_size = readable_filesize($cache_size+$compile_size);

echo '<div class="input-group mb-3">';
echo '<input class="form-control" type="text" name="prefs_smarty_cache_lifetime" value="'.$prefs_smarty_cache_lifetime.'">';
echo '<div class="input-group-append">';
echo '<button class="btn btn-fc" type="submit" name="delete_smarty_cache">('.$complete_size.') '.$lang['delete_cache'].'</button>';
echo '</div>';
echo '</div>';

echo '<input type="submit" class="btn btn-save" name="save_prefs_misc" value="'.$lang['save'].'">';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';

echo '</form>';
echo '</fieldset>';



echo '</div>';
echo '<div class="col-md-3">';

echo '<div class="card mt-4">';
echo '<div class="list-group list-group-flush">';

echo '<a href="#descriptions" class="list-group-item list-group-item-ghost">'.$lang['f_prefs_descriptions'].'</a>';
echo '<a href="#system" class="list-group-item list-group-item-ghost">System</a>';
echo '<a href="#user" class="list-group-item list-group-item-ghost">'.$lang['f_prefs_user'].'</a>';
echo '<a href="#globalheader" class="list-group-item list-group-item-ghost">'.$lang['f_prefs_global_header'].'</a>';
echo '<a href="#deletedresources" class="list-group-item list-group-item-ghost">'.$lang['label_deleted_resources'].'</a>';
echo '<a href="#misc" class="list-group-item list-group-item-ghost">'.$lang['system_misc'].'</a>';
echo '</div>';
echo '</div>';


echo '</div>';
echo '</div>';




?>