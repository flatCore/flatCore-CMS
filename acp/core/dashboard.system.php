<?php

//prohibit unauthorized access
require("core/access.php");

echo '<h3>System</h3>';

echo '<div class="row-fluid">';

echo '<div class="span4">';
echo '<h5>Config</h5>';
echo 'Server: <span class="label label-info">' . $_SERVER['SERVER_NAME'] . '</span><br>';
echo 'System E-Mails: <span class="label label-info">' . $fc_mailer_adr . '</span><br>';
echo 'E-Mail Name: <span class="label label-info">' . $fc_mailer_name . '</span>';

echo '<hr>';

echo '<h4>' . $lang['f_user_drm'] . '</h4>';


echo"<p><img src='images/user_blue_16.png' border='0'> $_SESSION[user_firstname] $_SESSION[user_lastname] ($_SESSION[user_nick])</p>";

$list_str = "<ul class='unstyled'>";

if($_SESSION['acp_pages'] == "allowed") {
	$list_str .= "<li><i class='icon-ok-sign'></i> $lang[drm_pages]</li>";
} else {
	$list_str .= "<li><i class='icon-minus-sign'></i> $lang[drm_pages]</li>";
}

if($_SESSION['acp_editpages'] == "allowed") {
	$list_str .= "<li><i class='icon-ok-sign'></i> $lang[drm_editpages]</li>";
} else {
	$list_str .= "<li><i class='icon-minus-sign'></i> $lang[drm_editpages]</li>";
}

if($_SESSION['acp_editownpages'] == "allowed") {
	$list_str .= "<li><i class='icon-ok-sign'></i> $lang[drm_editownpages]</li>";
} else {
	$list_str .= "<li><i class='icon-minus-sign'></i> $lang[drm_editownpages]</li>";
}

if($_SESSION['acp_files'] == "allowed") {
	$list_str .= "<li><i class='icon-ok-sign'></i> $lang[drm_files]</li>";
} else {
	$list_str .= "<li><i class='icon-minus-sign'></i> $lang[drm_files]</li>";
}

if($_SESSION['acp_user'] == "allowed") {
	$list_str .= "<li><i class='icon-ok-sign'></i> $lang[drm_user]</li>";
} else {
	$list_str .= "<li><i class='icon-minus-sign'></i> $lang[drm_files]</li>";
}

if($_SESSION['acp_system'] == "allowed") {
	$list_str .= "<li><i class='icon-ok-sign'></i> $lang[drm_system]</li>";
} else {
	$list_str .= "<li><i class='icon-minus-sign'></i> $lang[drm_files]</li>";
}

if($_SESSION['drm_can_publish'] == "true") {
	$list_str .= "<li><i class='icon-ok-sign'></i> $lang[drm_user_can_publish]</li>";
} else {
	$list_str .= "<li><i class='icon-minus-sign'></i> $lang[drm_user_can_publish]</li>";
}


$list_str .= "</ul>";

echo"$list_str";


echo"</div>";


echo"<div class='span8'>";

echo"<h4>Protokoll</h4>";

echo"<div style='height:280px;overflow:auto;padding:5px;background-color:#eee;border-radius:5px;'>";
show_log(10);
echo"</div>";

echo"</div>";


echo'</div>';







?>