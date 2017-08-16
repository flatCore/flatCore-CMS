<?php

//prohibit unauthorized access
require("core/access.php");



if($_GET['sub'] == "new"){
	$sub = "new";
} else {
	$sub = "edit";
}


echo '<div class="row"><div class="col-md-12">';

echo "<form action='acp.php?tn=user&sub=$sub&edituser=$edituser' class='form-horizontal' method='POST'>";

$custom_fields = get_custom_user_fields();
sort($custom_fields);
$cnt_custom_fields = count($custom_fields);

echo '<ul class="nav nav-tabs" id="bsTabs">';
echo '<li class="active"><a href="#info" data-toggle="tab">'.$lang['tab_user_info'].'</a></li>';
echo '<li><a href="#contact" data-toggle="tab">'.$lang['tab_contact'].'</a></li>';
echo '<li><a href="#psw" data-toggle="tab">'.$lang['tab_psw'].'</a></li>';
if($cnt_custom_fields > 0) {
	echo '<li><a href="#custom" data-toggle="tab">'.$lang['legend_custom_fields'].'</a></li>';
}
echo '</ul>';


echo '<div class="tab-content">';
echo '<div class="tab-pane fade in active" id="info">';

echo '<div class="row">';
echo '<div class="col-md-9">';

echo tpl_form_control_group('',$lang['f_user_nick'],"<input class='form-control' type='text' name='user_nick' value='$user_nick'>");

if($user_registerdate != "") {
	$show_registerdate = @date("d.m.Y H:i:s",$user_registerdate);
}

echo tpl_form_control_group('',$lang['f_user_registerdate'],"<p class='form-control-static'>$show_registerdate</p>");

echo '<input type="hidden" name="user_registerdate" value="'.$user_registerdate.'">';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';


if($user_verified == ""){
	$user_verified = "waiting";
}

$select_user_status  = '<label class="radio">';
$select_user_status .= "<input type='radio' name='user_verified' value='verified'".($user_verified == "verified" ? 'checked' :'').">";
$select_user_status .= "<span class='label label-success'>$lang[f_user_select_verified]</span>";
$select_user_status .= '</label>';

$select_user_status .= '<label class="radio">';
$select_user_status .= "<input type='radio' name='user_verified' value='waiting'".($user_verified == "waiting" ? 'checked' :'').">";
$select_user_status .= "<span class='label label-default'>$lang[f_user_select_waiting]</span>";
$select_user_status .= '</label>';

$select_user_status .= '<label class="radio">';
$select_user_status .= "<input type='radio' name='user_verified' value='paused'".($user_verified == "paused" ? 'checked' :'').">";
$select_user_status .= "<span class='label label-danger'>$lang[f_user_select_paused]</span>";
$select_user_status .= '</label>';

echo tpl_form_control_group('',$lang['f_user_status'],"$select_user_status");


$result = get_all_groups();

$nbr_of_groups = count($result);

echo"<input type='hidden' name='nbr_of_groups' value='$nbr_of_groups'>";

for($i=0;$i<$nbr_of_groups;$i++) {
	$get_group_id = $result[$i]['group_id'];
	$get_group_name = $result[$i]['group_name'];
	$get_group_user = $result[$i]['group_user'];
	
	$array_group_user = explode(" ", $get_group_user);
	
	$checked = "";
	if(in_array("$edituser", $array_group_user)) {
		$checked = "checked";
	}
	
	if($sub == "new") {
		$checked = "";
	}

	$cb_usergroup .= '<label class="checkbox">';
	$cb_usergroup .= "<input type='checkbox' name='user_groups[$i]' value='$get_group_id' $checked>";
	$cb_usergroup .= "$get_group_name";
	$cb_usergroup .= '</label>';
	$cb_usergroup .= "<input type='hidden' name='this_group[$i]' value='$get_group_id'>";
}

echo tpl_form_control_group('',$lang['f_user_groups'],"$cb_usergroup");


if($user_newsletter == "none" OR $user_newsletter == ""){
	$user_newsletter = "none";
}

if($user_newsletter == "html"){
	$checked2 = "checked";
}

if($user_newsletter == "text"){
	$checked3 = "checked";
}

$select_nwsl .= '<label class="radio">';
$select_nwsl .= "<input type='radio' name='user_newsletter' value='none'".($user_newsletter == "none" ? 'checked' :'').">";
$select_nwsl .= "$lang[f_user_newsletter_none]";
$select_nwsl .= '</label>';

$select_nwsl .= '<label class="radio">';
$select_nwsl .= "<input type='radio' name='user_newsletter' value='html'".($user_newsletter == "html" ? 'checked' :'').">";
$select_nwsl .= "$lang[f_user_newsletter_html]";
$select_nwsl .= '</label>';

$select_nwsl .= '<label class="radio">';
$select_nwsl .= "<input type='radio' name='user_newsletter' value='text'".($user_newsletter == "text" ? 'checked' :'').">";
$select_nwsl .= "$lang[f_user_newsletter_text]";
$select_nwsl .= '</label>';

echo tpl_form_control_group('',$lang['f_user_newsletter'],"$select_nwsl");

echo '</div>';
echo '<div class="col-md-3">';

echo '<fieldset>';
echo '<legend>Avatar</legend>';
if(is_file("$user_avatar_path")) {
	echo '<p class="text-center"><img src="'.$user_avatar_path.'" class="img-circle avatar"></p>';
	echo '<label class="checkbox">';
	echo '<input type="checkbox" name="deleteAvatar"> ' . $lang['delete'];
	echo '</label>';
} else {
	echo '<p class="text-center"><img src="images/avatar.png" class="img-circle avatar"></p>';
}
echo '</fieldset>';

echo '</div>';
echo '</div>';


echo '</div>';

/* tab_contact */

echo '<div class="tab-pane fade" id="contact">';


echo tpl_form_control_group('',$lang['f_user_firstname'],"<input type='text' class='form-control' name='user_firstname' value='$user_firstname'>");
echo tpl_form_control_group('',$lang['f_user_lastname'],"<input type='text' class='form-control' name='user_lastname' value='$user_lastname'>");
echo tpl_form_control_group('',$lang['f_user_mail'],"<input type='text' class='form-control' name='user_mail' value='$user_mail'>");
echo tpl_form_control_group('',$lang['f_user_company'],"<input type='text' class='form-control' name='user_company' value='$user_company'>");
echo tpl_form_control_group('',"$lang[f_user_street]/$lang[f_user_street_nbr]","<div class='row'><div class='col-md-9'><input type='text' class='form-control' name='user_street' value='$user_street'></div><div class='col-md-3'><input class='form-control' type='text' name='user_street_nbr' value='$user_street_nbr'></div></div>");
echo tpl_form_control_group('',"$lang[f_user_zipcode]/$lang[f_user_city]","<div class='row'><div class='col-md-3'><input type='text' class='form-control' name='user_zipcode' value='$user_zipcode'></div><div class='col-md-9'><input class='form-control' type='text' name='user_city' value='$user_city'></div></div>");



echo '</div>';

/* EOL tab_contact ### ### ### */


echo'<div class="tab-pane fade" id="psw">';

echo tpl_form_control_group('',$lang['f_user_psw'],"<div class='alert alert-danger'>$lang[f_user_psw_description]</div>");

echo tpl_form_control_group('',$lang['f_user_psw_new'],"<input class='form-control' type='password' name='user_psw_new' value=''>");
echo tpl_form_control_group('',$lang['f_user_psw_reconfirmation'],"<input class='form-control' type='password' name='user_psw_reconfirmation' value=''>");
echo"\n<input type='hidden' name='user_psw' value='$user_psw'>\n";



/* DRM */

//String = $user_drm

if($user_class == "administrator")	{  $checked_class = "checked";  }

$arr_drm = explode("|", $user_drm);

if($arr_drm[0] == "drm_acp_pages")	{  $checked_pages = "checked";  }
if($arr_drm[1] == "drm_acp_files")	{  $checked_files = "checked";  }
if($arr_drm[2] == "drm_acp_user")	{  $checked_user = "checked";  }
if($arr_drm[3] == "drm_acp_system")	{  $checked_system = "checked";  }
if($arr_drm[4] == "drm_acp_editpages")	{  $checked_editpages = "checked";  }
if($arr_drm[5] == "drm_acp_editownpages")	{  $checked_editownpages = "checked";  }
if($arr_drm[6] == "drm_moderator")	{  $checked_moderator = "checked";  }
if($arr_drm[7] == "drm_can_publish")	{  $checked_can_publish = "checked";  }


$cb_user_drm .= "<label class='checkbox'><input type='checkbox' value='administrator' name='drm_acp_class' $checked_class> <b>$lang[drm_administrator]</b></label>";
$cb_user_drm .= "<label class='checkbox'><input type='checkbox' value='drm_acp_pages' name='drm_acp_pages' $checked_pages> $lang[drm_pages]</label>";
$cb_user_drm .= "<label class='checkbox'><input type='checkbox' value='drm_acp_editpages' name='drm_acp_editpages' $checked_editpages> $lang[drm_editpages]</label>";
$cb_user_drm .= "<label class='checkbox'><input type='checkbox' value='drm_acp_editownpages' name='drm_acp_editownpages' $checked_editownpages> $lang[drm_editownpages]</label>";
$cb_user_drm .= "<label class='checkbox'><input type='checkbox' value='drm_acp_files' name='drm_acp_files' $checked_files> $lang[drm_files]</label>";
$cb_user_drm .= "<label class='checkbox'><input type='checkbox' value='drm_acp_user' name='drm_acp_user' $checked_user> $lang[drm_user]</label>";
$cb_user_drm .= "<label class='checkbox'><input type='checkbox' value='drm_acp_system' name='drm_acp_system' $checked_system> $lang[drm_system]</label>";
$cb_user_drm .= "<label class='checkbox'><input type='checkbox' value='drm_moderator' name='drm_moderator' $checked_moderator> $lang[drm_moderator]</label>";
$cb_user_drm .= "<label class='checkbox'><input type='checkbox' value='drm_can_publish' name='drm_can_publish' $checked_can_publish> $lang[drm_user_can_publish]</label>";

echo tpl_form_control_group('',$lang['f_user_drm'],"$cb_user_drm");

echo '</div>';


if($cnt_custom_fields > 0) {

/* tab custom fields */
echo'<div class="tab-pane fade" id="custom">';

	for($i=0;$i<$cnt_custom_fields;$i++) {	
		if(substr($custom_fields[$i],0,10) == "custom_one") {
			$label = substr($custom_fields[$i],11);
			echo tpl_form_control_group('',$label,"<input type='text' class='form-control' name='$custom_fields[$i]' value='" . ${$custom_fields[$i]} . "'>");
		}	elseif(substr($custom_fields[$i],0,11) == "custom_text") {
			$label = substr($custom_fields[$i],12);
			echo tpl_form_control_group('',$label,"<textarea class='form-control' rows='6' name='$custom_fields[$i]'>" . ${$custom_fields[$i]} . "</textarea>");
		}	elseif(substr($custom_fields[$i],0,14) == "custom_wysiwyg") {
			$label = substr($custom_fields[$i],15);
			echo tpl_form_control_group('',$label,"<textarea class='mceEditor_small' name='$custom_fields[$i]'>" . ${$custom_fields[$i]} . "</textarea>");
		}		
	}

echo '</div>'; /* EOL tab custom fields */

}




echo '</div>';

//submit form to save data
echo '<div class="formfooter">';
echo "$delete_button $submit_button";
echo '</div>';

echo '</form>';

echo '</div></div>';


?>