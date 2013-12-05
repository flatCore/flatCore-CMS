<?php

//prohibit unauthorized access
require("core/access.php");

echo '<h3>System</h3>';

echo '<div class="row">';

echo '<div class="col-md-4">';
echo '<h5>Config</h5>';
echo '<dl class="dl-horizontal">';
echo '<dt>Server:</dt><dd>' . $_SERVER['SERVER_NAME'] . '</dd>';
echo '<dt>System E-Mails:</dt><dd>' . $fc_mailer_adr . '</dd>';
echo '<dt>E-Mail Name:</dt><dd>' . $fc_mailer_name . '</dd>';
echo '</dl>';


echo '<hr>';

echo '<h4>' . $lang['f_user_drm'] . '</h4>';


echo"<p><span class='glyphicon glyphicon-user'></span> $_SESSION[user_firstname] $_SESSION[user_lastname] ($_SESSION[user_nick])</p>";

$list_str = "<ul class='list-unstyled'>";

if($_SESSION['acp_pages'] == "allowed") {
	$list_str .= "<li><span class='glyphicon glyphicon-ok-circle'></span> $lang[drm_pages]</li>";
} else {
	$list_str .= "<li><span class='glyphicon glyphicon-remove-circle'></span> $lang[drm_pages]</li>";
}

if($_SESSION['acp_editpages'] == "allowed") {
	$list_str .= "<li><span class='glyphicon glyphicon-ok-circle'></span> $lang[drm_editpages]</li>";
} else {
	$list_str .= "<li><span class='glyphicon glyphicon-remove-circle'></span> $lang[drm_editpages]</li>";
}

if($_SESSION['acp_editownpages'] == "allowed") {
	$list_str .= "<li><span class='glyphicon glyphicon-ok-circle'></span> $lang[drm_editownpages]</li>";
} else {
	$list_str .= "<li><span class='glyphicon glyphicon-remove-circle'></span> $lang[drm_editownpages]</li>";
}

if($_SESSION['acp_files'] == "allowed") {
	$list_str .= "<li><span class='glyphicon glyphicon-ok-circle'></span> $lang[drm_files]</li>";
} else {
	$list_str .= "<li><span class='glyphicon glyphicon-remove-circle'></span> $lang[drm_files]</li>";
}

if($_SESSION['acp_user'] == "allowed") {
	$list_str .= "<li><span class='glyphicon glyphicon-ok-circle'></span> $lang[drm_user]</li>";
} else {
	$list_str .= "<li><span class='glyphicon glyphicon-remove-circle'></span> $lang[drm_files]</li>";
}

if($_SESSION['acp_system'] == "allowed") {
	$list_str .= "<li><span class='glyphicon glyphicon-ok-circle'></span> $lang[drm_system]</li>";
} else {
	$list_str .= "<li><span class='glyphicon glyphicon-remove-circle'></span> $lang[drm_files]</li>";
}

if($_SESSION['drm_can_publish'] == "true") {
	$list_str .= "<li><span class='glyphicon glyphicon-ok-circle'></span> $lang[drm_user_can_publish]</li>";
} else {
	$list_str .= "<li><span class='glyphicon glyphicon-remove-circle'></span> $lang[drm_user_can_publish]</li>";
}


$list_str .= "</ul>";

echo"$list_str";


echo"</div>";


echo '<div class="col-md-8">';

echo '<h4>Protokoll</h4>';

echo '<div id="logfile-container">';
show_log(10);
echo'</div>';

echo'</div>';


echo'</div>';







?>