<?php

//prohibit unauthorized access
require 'core/access.php';


echo '<hr class="shadow">';

echo '<div class="row">';

echo '<div class="col-md-4">';

echo '<div class="card">';
echo '<div class="card-header">' . $lang['f_user_drm'] . '</div>';
echo '<div class="card-body">';

$user_avatar = '<img src="images/avatar.png" class="rounded-circle avatar" width="50" height="50">';
$user_avatar_path = '../'. FC_CONTENT_DIR . '/avatars/' . md5($_SESSION['user_nick']) . '.png';
if(is_file("$user_avatar_path")) {
	$user_avatar = '<img src="'.$user_avatar_path.'" class="rounded-circle avatar" width="50" height="50">';
}

echo '<p class="lead">'.$user_avatar.' '.$_SESSION['user_firstname'].' '.$_SESSION['user_lastname'].' ('.$_SESSION['user_nick'].')</p>';

$list_str = '<ul class="list-unstyled" style="padding-left:16px;">';

if($_SESSION['acp_pages'] == "allowed") {
	$list_str .= '<li>'.$icon['check'].' '. $lang['drm_pages'].'</li>';
} else {
	$list_str .= '<li>'.$icon['ban'].' '. $lang['drm_pages'].'</li>';
}

if($_SESSION['acp_editpages'] == "allowed") {
	$list_str .= '<li>'.$icon['check'].' '. $lang['drm_editpages'].'</li>';
} else {
	$list_str .= '<li>'.$icon['ban'].' '. $lang['drm_editpages'].'</li>';
}

if($_SESSION['acp_editownpages'] == "allowed") {
	$list_str .= '<li>'.$icon['check'].' '. $lang['drm_editownpages'].'</li>';
} else {
	$list_str .= '<li>'.$icon['ban'].' '. $lang['drm_editownpages'].'</li>';
}

if($_SESSION['acp_files'] == "allowed") {
	$list_str .= '<li>'.$icon['check'].' '. $lang['drm_files'].'</li>';
} else {
	$list_str .= '<li>'.$icon['ban'].' '. $lang['drm_files'].'</li>';
}

if($_SESSION['acp_user'] == "allowed") {
	$list_str .= '<li>'.$icon['check'].' '. $lang['drm_user'].'</li>';
} else {
	$list_str .= '<li>'.$icon['ban'].' '. $lang['drm_user'].'</li>';
}

if($_SESSION['acp_system'] == "allowed") {
	$list_str .= '<li>'.$icon['check'].' '. $lang['drm_system'].'</li>';
} else {
	$list_str .= '<li>'.$icon['ban'].' '. $lang['drm_files'].'</li>';
}

if($_SESSION['drm_can_publish'] == "true") {
	$list_str .= '<li>'.$icon['check'].' '. $lang['drm_user_can_publish'].'</li>';
} else {
	$list_str .= '<li>'.$icon['ban'].' '. $lang['drm_user_can_publish'].'</li>';
}


$list_str .= "</ul>";

echo $list_str;

echo '</div>';

echo '</div>';
echo '</div>';


echo '<div class="col-md-8">';

echo '<div class="card">';
echo '<div class="card-header">';

echo '<ul class="nav nav-tabs card-header-tabs" id="bsTabs" role="tablist">';
echo '<li class="nav-item"><a class="nav-link active" href="#" data-bs-target="#log" data-bs-toggle="tab">'.$icon['file_alt'].' Logfile</a></li>';
echo '<li class="nav-item"><a class="nav-link" href="#" data-bs-target="#sitemap" data-bs-toggle="tab">'.$icon['sitemap'].' sitemap.xml</a></li>';
echo '<li class="nav-item"><a class="nav-link" href="#" data-bs-target="#deleted_resources" data-bs-toggle="tab">'.$icon['trash_alt'].' '.$lang['label_deleted_resources'].'</a></li>';
echo '<li class="nav-item"><a class="nav-link" href="#" data-bs-target="#config" data-bs-toggle="tab">'.$icon['cogs'].' Config</a></li>';
echo '</ul>';

echo '</div>';
echo '<div class="card-body">';

echo '<div class="tab-content">';

echo'<div class="tab-pane fade show active" id="log">';

echo '<div class="scroll-container">';
show_log(10);
echo'</div>';
echo'</div>'; // #log

echo '<div class="tab-pane fade" id="sitemap">';
$sitemap = file_get_contents('../sitemap.xml');
echo '<textarea name="my-xml-editor" data-editor="xml" rows="15">'.htmlentities($sitemap,ENT_QUOTES,"UTF-8").'</textarea>';
echo '</div>'; // #sitemap


echo '<div class="tab-pane fade" id="deleted_resources">';
echo '<div class="scroll-container">';
$deleted_resources = explode(PHP_EOL, $prefs_deleted_resources);
echo '<ul>';
foreach($deleted_resources as $resource) {
	echo '<li>'.$resource.'</li>';
}

echo '<li><a href="acp.php?tn=system">'.$lang['system_preferences'].'</a></li>';

echo '</ul>';
echo '</div>';
echo '</div>'; // #deleted resources

echo '<div class="tab-pane fade" id="config">';
echo '<table class="table table-sm">';
echo '<tr><td>Server:</td><td>' . $_SERVER['SERVER_NAME'] . ' (PHP '.phpversion().')</td></tr>';
echo '<tr><td>Database:</td><td>'.$db_type.'</td></tr>';
echo '<tr><td>'.$lang['prefs_cms_domain'].'</td><td>' . $prefs_cms_domain . '</td></tr>';
echo '<tr><td>'.$lang['prefs_cms_ssl_domain'].'</td><td>' . $prefs_cms_ssl_domain . '</td></tr>';
echo '<tr><td>'.$lang['prefs_cms_base'].'</td><td>' . $prefs_cms_base . '</td></tr>';
if($prefs_mailer_adr != '') {
	echo '<tr><td>System E-Mails:</td><td>' . $prefs_mailer_adr . '</td></tr>';
} else {
	echo '<tr><td>System E-Mails:</td><td><span class="text-danger">'.$lang['missing_value'].'</span></td></tr>';
}
if($prefs_mailer_name != '') {
	echo '<tr><td>E-Mail Name:</td><td>' . $prefs_mailer_name . '</td></tr>';
} else {
	echo '<tr><td>E-Mail Name:</td><td><span class="text-danger">'.$lang['missing_value'].'</span></td></tr>';
}
echo '</table>';

echo '</div>'; // #config

echo '</div>'; // .tab-content
echo '</div>'; // .card-body
echo '</div>'; // .card

echo '</div>';


echo '</div>';







?>