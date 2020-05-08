<?php

//prohibit unauthorized access
require 'core/access.php';

/**
 * save the preferences
 */

foreach($_POST as $key => $val) {
	$$key = @htmlspecialchars($val, ENT_QUOTES); 
}


/* save contacts */
if(isset($_POST['save_prefs_contacts'])) {
	
	
	$data = $db_content->update("fc_preferences", [
		"prefs_mailer_adr" =>  $prefs_mailer_adr,
		"prefs_mailer_name" => $prefs_mailer_name,
		"prefs_mailer_type" => $_POST['prefs_mailer_type'],
		"prefs_smtp_host" => $_POST['prefs_smtp_host'],
		"prefs_smtp_port" => $_POST['prefs_smtp_port'],
		"prefs_smtp_encryption" => $_POST['prefs_smtp_encryption'],
		"prefs_smtp_username" => $_POST['prefs_smtp_username'],
		"prefs_smtp_psw" => $_POST['prefs_smtp_psw']
	], [
	"prefs_id" => 1
	]);

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
		"prefs_filesuffix" => $prefs_filesuffix,
		"prefs_maxfilesize" => $prefs_maxfilesize,
		"prefs_showfilesize" => $prefs_showfilesize,
		"prefs_uploads_remain_unchanged" => $prefs_upload_unchanged
	], [
	"prefs_id" => 1
	]);	
	

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

/* save deleted resources */
if(isset($_POST['save_deleted_resources'])) {
	
		$pdo_fields = array(
			'prefs_deleted_resources' => 'STR'
	);

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_update_str($pdo_fields,"fc_preferences","WHERE prefs_id = 1");
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':prefs_deleted_resources', $_POST['prefs_deleted_resources'], PDO::PARAM_STR);
	$cnt_changes = $sth->execute();
	$dbh = null;
}


/* save misc preferences */
if(isset($_POST['save_prefs_misc'])) {
	
	$pdo_fields = array(
		'prefs_logfile' => 'STR',
		'prefs_anonymize_ip' => 'STR',
		'prefs_xml_sitemap' => 'STR',
		'prefs_rss_time_offset' => 'STR',
		'prefs_nbr_page_versions' => 'INT',
		'prefs_smarty_cache' => 'INT',
		'prefs_smarty_cache_lifetime' => 'INT',
		'prefs_smarty_compile_check' => 'INT'
	);
	
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

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_update_str($pdo_fields,"fc_preferences","WHERE prefs_id = 1");
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':prefs_rss_time_offset', $_POST['prefs_rss_time_offset'], PDO::PARAM_STR);
	$sth->bindParam(':prefs_logfile', $prefs_logfile, PDO::PARAM_STR);
	$sth->bindParam(':prefs_anonymize_ip', $prefs_anonymize_ip, PDO::PARAM_STR);
	$sth->bindParam(':prefs_xml_sitemap', $prefs_xml_sitemap, PDO::PARAM_STR);
	$sth->bindParam(':prefs_nbr_page_versions', $prefs_nbr_page_versions, PDO::PARAM_INT);
	$sth->bindParam(':prefs_smarty_cache', $prefs_smarty_cache, PDO::PARAM_INT);
	$sth->bindParam(':prefs_smarty_cache_lifetime', $_POST['prefs_smarty_cache_lifetime'], PDO::PARAM_INT);
	$sth->bindParam(':prefs_smarty_compile_check', $prefs_smarty_compile_check, PDO::PARAM_INT);
	$cnt_changes = $sth->execute();
	$dbh = null;
}

/* delete smarty cache files */

if(isset($_POST['delete_smarty_cache'])) {
	fc_delete_smarty_cache('all');
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

$toggle_btn_publisher  = '<div class="input-group-append">';
$toggle_btn_publisher .= '<span class="input-group-text">'.$lang['f_prefs_publisher_mode'].'</span>';
$toggle_btn_publisher .= '<div class="input-group-text">';
$toggle_btn_publisher .= '<input type="checkbox" name="prefs_publisher_mode" '.($prefs_publisher_mode == "overwrite" ? 'checked' :'').'>';
$toggle_btn_publisher .= '</div>';
$toggle_btn_publisher .= '</div>';

$prefs_publisher_input  = '<div class="input-group">';
$prefs_publisher_input .= '<input class="form-control" type="text" name="prefs_default_publisher" value="'.$prefs_default_publisher.'">';
$prefs_publisher_input .= $toggle_btn_publisher;
$prefs_publisher_input .= '</div>';

echo tpl_form_control_group('',$lang['f_prefs_default_publisher'],$prefs_publisher_input);


echo tpl_form_control_group('','',"<input type='submit' class='btn btn-save' name='save_prefs_descriptions' value='$lang[save]'>");

echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';
echo '</fieldset>';


/* default Thumbnail */
echo '<div id="thumbnail" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>'.$lang['page_thumbnail_default'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref" method="POST" class="form-horizontal">';

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

/* contacts */
echo '<div id="mails" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>System E-Mail</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref#mails" method="POST" class="form-horizontal">';

if($prefs_mailer_type == '') {
	$prefs_mailer_type = 'mail';
}

$prefs_mail_name_input = "<input class='form-control' type='text' name='prefs_mailer_name' value='$prefs_mailer_name'>";
$prefs_mail_adr_input = "<input class='form-control' type='text' name='prefs_mailer_adr' value='$prefs_mailer_adr'>";
$prefs_mail_smtp_host_input = "<input class='form-control' type='text' name='prefs_smtp_host' value='$prefs_smtp_host'>";
$prefs_mail_smtp_port_input = "<input class='form-control' type='text' name='prefs_smtp_port' value='$prefs_smtp_port'>";
$prefs_mail_smtp_encryption_input = "<input class='form-control' type='text' name='prefs_smtp_encryption' value='$prefs_smtp_encryption'>";
$prefs_mail_smtp_username_input = "<input class='form-control' type='text' name='prefs_smtp_username' value='$prefs_smtp_username'>";
$prefs_mail_smtp_psw_input = "<input class='form-control' type='password' name='prefs_smtp_psw' value='$prefs_smtp_psw'>";

$prefs_mail_type_input = '<div class="form-check">';
$prefs_mail_type_input .= '<input type="radio" class="form-check-input" id="mail" name="prefs_mailer_type" value="mail" '.($prefs_mailer_type == "mail" ? 'checked' :'').'>';
$prefs_mail_type_input .= '<label class="form-check-label" for="mail">'.$lang['prefs_mail_type_mail'].'</label>';
$prefs_mail_type_input .= '</div>';
$prefs_mail_type_input .= '<div class="form-check">';
$prefs_mail_type_input .= '<input type="radio" class="form-check-input" id="smtp" name="prefs_mailer_type" value="smtp" '.($prefs_mailer_type == "smtp" ? 'checked' :'').'>';
$prefs_mail_type_input .= '<label class="form-check-label" for="smtp">'.$lang['prefs_mail_type_smtp'].'</label>';
$prefs_mail_type_input .= '</div>';



echo tpl_form_control_group('',$lang['prefs_mailer_name'],$prefs_mail_name_input);
echo tpl_form_control_group('',$lang['prefs_mailer_adr'],$prefs_mail_adr_input);

echo $prefs_mail_type_input;

echo tpl_form_control_group('','','<p>SMTP</p>');

echo '<div class="row">';
echo '<div class="col-md-4">';
echo tpl_form_control_group('',$lang['prefs_mailer_smtp_host'],$prefs_mail_smtp_host_input);
echo '</div>';
echo '<div class="col-md-4">';
echo tpl_form_control_group('',$lang['prefs_mailer_smtp_port'],$prefs_mail_smtp_port_input);
echo '</div>';
echo '<div class="col-md-4">';
echo tpl_form_control_group('',$lang['prefs_mailer_smtp_encryption'],$prefs_mail_smtp_encryption_input);
echo '</div>';
echo '</div>';
echo '<div class="row">';
echo '<div class="col-md-6">';
echo tpl_form_control_group('',$lang['prefs_mailer_smtp_username'],$prefs_mail_smtp_username_input);
echo '</div>';
echo '<div class="col-md-6">';
echo tpl_form_control_group('',$lang['prefs_mailer_smtp_password'],$prefs_mail_smtp_psw_input);
echo '</div>';
echo '</div>';

echo '<input type="submit" class="btn btn-save" name="save_prefs_contacts" value="'.$lang['save'].'">';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';

echo '<div class="mt-3">';
if($prefs_mailer_adr != '') {
	echo '<a href="acp.php?tn=system&sub=sys_pref&sendtest=1#mails" class="btn btn-fc btn-sm">'.$lang['prefs_mailer_send_test'].' ('.$prefs_mailer_adr.')</a>';
}


if($_GET['sendtest'] == 1) {
	require_once'../lib/Swift/lib/swift_required.php';
	
	if($prefs_mailer_type == 'smtp') {
		
		$trans = Swift_SmtpTransport::newInstance()
            ->setUsername("$prefs_smtp_username")
            ->setPassword("$prefs_smtp_psw")
            ->setHost("$prefs_smtp_host")
            ->setPort($prefs_smtp_port);
			
		if($prefs_mail_smtp_encryption_input != '') {
			$trans->setEncryption($prefs_smtp_encryption);
		}
	} else {
		$trans = Swift_MailTransport::newInstance();
	}
	

	$mailer = Swift_Mailer::newInstance($trans);
	$message = Swift_Message::newInstance('flatCore Test')
			->setFrom(array($prefs_mailer_adr => $prefs_mailer_name))
			->setTo(array($prefs_mailer_adr => $prefs_mailer_name))
			->setBody("flatCore Test (via $prefs_mailer_type)");
			
	if(!$mailer->send($message, $failures)) {
		echo '<div class="alert alert-danger mt-3">';
	  echo 'Failures:<br>';
	  print_r($failures);
	  echo '</div>';
	} else {
		echo '<p class="alert alert-success mt-3">'.$icon['check'].' '.$lang['prefs_mailer_send_test_success'].'</p>';
	}
	
}

echo '</div>';


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


/* Upload Preferences */
echo '<div id="uploads" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>'.$lang['f_prefs_uploads'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref#uploads" method="POST" class="form-horizontal">';

echo tpl_form_control_group('',$lang['f_prefs_imagesuffix'],"<input class='form-control' type='text' name='prefs_imagesuffix' value='$prefs_imagesuffix'>");

$prefs_maximage_input  = '<div class="row"><div class="col-md-3">';
$prefs_maximage_input .= '<div class="input-group">';
$prefs_maximage_input .= '<input class="form-control" type="text" name="prefs_maximagewidth" value="'.$prefs_maximagewidth.'">';
$prefs_maximage_input .= '<div class="input-group-append"><span class="input-group-text"><i class="fas fa-arrows-alt-h"></i></span></div>';
$prefs_maximage_input .= '</div>';
$prefs_maximage_input .= '</div><div class="col-md-3">';
$prefs_maximage_input .= '<div class="input-group">';
$prefs_maximage_input .= '<input class="form-control" type="text" name="prefs_maximageheight" value="'.$prefs_maximageheight.'">';
$prefs_maximage_input .= '<div class="input-group-append"><span class="input-group-text"><i class="fas fa-arrows-alt-v"></i></span></div>';
$prefs_maximage_input .= '</div>';
$prefs_maximage_input .= '</div></div>';

echo tpl_form_control_group('',$lang['f_prefs_maximage'],"$prefs_maximage_input");
echo tpl_form_control_group('',$lang['f_prefs_filesuffix'],"<input class='form-control' type='text' name='prefs_filesuffix' value='$prefs_filesuffix'>");
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


/* global header enhancement */
echo '<div id="globalheader" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>'.$lang['f_prefs_global_header'].'</legend>';
echo '<form action="acp.php?tn=system&sub=sys_pref" method="POST" class="form-horizontal">';

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
echo '<form action="acp.php?tn=system&sub=sys_pref" method="POST" class="form-horizontal">';


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

echo tpl_form_control_group('',$lang['prefs_nbr_page_versions'],"<input class='form-control' type='text' name='prefs_nbr_page_versions' value='$prefs_nbr_page_versions'>");

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


/* Labels */
echo '<div id="labels" class="pt-2"></div>';

echo '<fieldset>';
echo '<legend>'.$lang['labels'].'</legend>';

$fc_labels = fc_get_labels();
$cnt_labels = count($fc_labels);



for($i=0;$i<$cnt_labels;$i++) {
	echo '<form action="acp.php?tn=system&sub=sys_pref#labels" method="POST" class="clearfix" id="labels">';
	echo '<div class="row mb-1">';
	echo '<div class="col-md-2">';
	echo '<div class="input-group">';
	echo '<div class="input-group-prepend">';
	echo '<span class="input-group-text" id="basic-addon1" style="background-color:'.$fc_labels[$i]['label_color'].'"></span>';
	echo '</div>';
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
	echo '<button type="submit" name="update_label" class="btn btn-save">'.$icon['sync_alt'].'</button>';
	echo '<button type="submit" name="delete_label" class="btn btn-fc text-danger">'.$icon['trash_alt'].'</button>';
	echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
	echo '</div>';
	echo '</div>';
	
	echo '</div>';
	echo '</form>';
	
	if($i == $cnt_labels-1) {
		echo '<hr>';
	}
	
}


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
echo '<br><button type="submit" name="new_label" class="btn btn-save">'.$lang['save'].'</button>';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</div>';
echo '</div>';
echo '</form>';



echo"</fieldset>";


echo '</div>';
echo '<div class="col-md-3">';

echo '<div class="card mt-4">';
echo '<div class="list-group list-group-flush">';

echo '<a href="#descriptions" class="list-group-item list-group-item-ghost">'.$lang['f_prefs_descriptions'].'</a>';
echo '<a href="#thumbnail" class="list-group-item list-group-item-ghost">'.$lang['page_thumbnail_default'].'</a>';
echo '<a href="#system" class="list-group-item list-group-item-ghost">System</a>';
echo '<a href="#mails" class="list-group-item list-group-item-ghost">System E-Mail</a>';
echo '<a href="#user" class="list-group-item list-group-item-ghost">'.$lang['f_prefs_user'].'</a>';
echo '<a href="#uploads" class="list-group-item list-group-item-ghost">'.$lang['f_prefs_uploads'].'</a>';
echo '<a href="#globalheader" class="list-group-item list-group-item-ghost">'.$lang['f_prefs_global_header'].'</a>';
echo '<a href="#deletedresources" class="list-group-item list-group-item-ghost">'.$lang['label_deleted_resources'].'</a>';
echo '<a href="#misc" class="list-group-item list-group-item-ghost">'.$lang['system_misc'].'</a>';
echo '<a href="#labels" class="list-group-item list-group-item-ghost">'.$lang['labels'].'</a>';
echo '</div>';
echo '</div>';


echo '</div>';
echo '</div>';




?>