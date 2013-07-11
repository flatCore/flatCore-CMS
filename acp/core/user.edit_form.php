<?php

//prohibit unauthorized access
require("core/access.php");



if($_GET[sub] == "new"){
	$sub = "new";
} else {
	$sub = "edit";
}


echo '<div class="row-fluid"><div class="span12">';

echo"<form action='$_SEVER[PHP_SELF]?tn=user&sub=$sub&edituser=$edituser' class='form-horizontal' method='POST'>";

// fancytabs
echo"<div id='tabsBlock'>";

/* tab_info */
echo"<h4 title='$lang[tab_user_info_description]'>$lang[tab_user_info]</h4>";



echo"<div class='tab-content'>";



echo"	<div class='control-group'>
		<label class='control-label'>$lang[f_user_nick]</label>
		<div class='controls'><input class='span6' type='text' name='user_nick' value='$user_nick'></div>
		</div>";

if($user_registerdate != "") {
$show_registerdate = @date("d.m.Y H:i:s",$user_registerdate);
}

echo"	<div class='control-group'>
		<label class='control-label'>$lang[f_user_registerdate]</label>
		<div class='controls'>$show_registerdate</div>
		</div>";
echo"<input type='hidden' name='user_registerdate' value='$user_registerdate'>";
		
		


// $user_verified

if($user_verified == "waiting"){
	$checked_w = "checked";
} elseif($user_verified == "verified"){
	$checked_v = "checked";
} elseif($user_verified == "paused"){
	$checked_p = "checked";
} else {
	$checked_w = "checked";
}

echo"<div class='control-group'>
		<label class='control-label'>$lang[f_user_status]</label>
		<div class='controls'>
		<label class='radio'>
			<input type='radio' value='verified' name='user_verified' $checked_v>
			<span class='label label-success'><i class='icon-ok-sign icon-white'></i></span> $lang[f_user_select_verified]
		</label>
		<label class='radio'>
			<input type='radio' value='waiting' name='user_verified' $checked_w>
			<span class='label label-info'><i class='icon-ok-circle icon-white'></i></span> $lang[f_user_select_waiting]
		</label>
		<label class='radio'>
			<input type='radio' value='paused' name='user_verified' $checked_p>
			<span class='label label-important'><i class='icon-ban-circle icon-white'></i></span> $lang[f_user_select_paused]
		</label>
		</div>
	</div>";


echo"<div class='control-group'>
		<label class='control-label'>$lang[f_user_groups]</label>
		<div class='controls'>";

$result = get_all_groups();

$nbr_of_groups = count($result);
echo"<input type='hidden' name='nbr_of_groups' value='$nbr_of_groups'>";

for($i=0;$i<$nbr_of_groups;$i++) {
$get_group_id = $result[$i][group_id];
$get_group_name = $result[$i][group_name];
$get_group_user = $result[$i][group_user];

$array_group_user = explode(" ", $get_group_user);

$checked = "";
if(in_array("$edituser", $array_group_user)) {
    $checked = "checked";
}


if($sub == "new") {
$checked = "";
}



echo"<input type='hidden' name='this_group[$i]' value='$get_group_id'>";
echo"<label class='checkbox'><input type='checkbox' name='user_groups[$i]' value='$get_group_id' $checked>";
echo" $get_group_name (ID: $get_group_id) </label>";

} //eol $i

echo"</div></div>";


if($user_newsletter == "none" OR $user_newsletter == ""){
	$checked1 = "checked";
}

if($user_newsletter == "html"){
	$checked2 = "checked";
}

if($user_newsletter == "text"){
	$checked3 = "checked";
}


echo"<div class='control-group'>
		<label class='control-label'>$lang[f_user_newsletter]</label>
		<div class='controls'>
			<label class='radio'>
				<input type='radio' name='user_newsletter' value='none' $checked1> $lang[f_user_newsletter_none]
			</label>
			<label class='radio'>
				<input type='radio' name='user_newsletter' value='html' $checked2> $lang[f_user_newsletter_html]
			</label>
			<label class='radio'>
				<input type='radio' name='user_newsletter' value='text' $checked3> $lang[f_user_newsletter_text]
			</label>
		</div>
	</div>";


echo"</div>";


/* EOL tab_info ### ### ### */



/* tab_contact */
echo"<h4 title='$lang[tab_contact_description]'>$lang[tab_contact]</h4>";


echo"<div class='tab-content'>";

echo"<div class='control-group'>
			<label class='control-label'>$lang[f_user_firstname]</label>
			<div class='controls'>
				<input class='span8' type='text' name='user_firstname' value='$user_firstname'>
			</div>
		</div>";

echo"<div class='control-group'>
	<label class='control-label'>$lang[f_user_lastname]</label>
	<div class='controls'>
	<input class='span8' type='text' name='user_lastname' value='$user_lastname'>
	</div>
	</div>";

echo"<div class='control-group'>
		<label class='control-label'>$lang[f_user_mail]</label>
		<div class='controls'>
		<input class='span8' type='text' name='user_mail' value='$user_mail'>
		</div>
	</div>";

echo"<div class='control-group'>
		<label class='control-label'>$lang[f_user_company]</label>
		<div class='controls'>
		<input class='span8' type='text' name='user_company' value='$user_company'>
		</div>
	</div>";

echo"<div class='control-group'>
		<label class='control-label'>$lang[f_user_street]/$lang[f_user_street_nbr]</label>
		<div class='controls controls-row'>
		<input class='span6' type='text' name='user_street' value='$user_street'> 
		<input class='span2' type='text' name='user_street_nbr' value='$user_street_nbr'>
		</div>
	</div>";

echo"<div class='control-group'>
		<label class='control-label controls-row'>$lang[f_user_zipcode]/$lang[f_user_city]</label>
		<div class='controls controls-row'>
		<input class='span2' type='text' name='user_zipcode' value='$user_zipcode'> 
		<input class='span6' type='text' name='user_city' value='$user_city'>
		</div>
	</div>";

echo"</div>"; // eol .formbox

/* EOL tab_contact ### ### ### */

/* tab_password */
echo"<h4 title='$lang[tab_psw_description]'>$lang[tab_psw]</h4>";


echo"<div class='tab-content'>";

echo"<div class='control-group'>
		<label class='control-label'>$lang[f_user_psw]</label>
		<div class='controls'>
		<div class='alert'>$lang[f_user_psw_description]</div>
		$lang[f_user_psw_new]<br />
		<input class='span6' type='password' name='user_psw_new' value=''><br />
		$lang[f_user_psw_reconfirmation]<br />
		<input class='span6' type='password' name='user_psw_reconfirmation' value=''>
		</div>
	</div>";

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

echo"<div class='control-group'>
		<label class='control-label'>$lang[f_user_drm]</label>
		<div class='controls'>
		<label class='checkbox'><input type='checkbox' value='administrator' name='drm_acp_class' $checked_class> <b>$lang[drm_administrator]</b></label>
		<label class='checkbox'><input type='checkbox' value='drm_acp_pages' name='drm_acp_pages' $checked_pages> $lang[drm_pages]</label>
		<label class='checkbox'><input type='checkbox' value='drm_acp_editpages' name='drm_acp_editpages' $checked_editpages> $lang[drm_editpages]</label>
		<label class='checkbox'><input type='checkbox' value='drm_acp_editownpages' name='drm_acp_editownpages' $checked_editownpages> $lang[drm_editownpages]</label>
		<label class='checkbox'><input type='checkbox' value='drm_acp_files' name='drm_acp_files' $checked_files> $lang[drm_files]</label>
		<label class='checkbox'><input type='checkbox' value='drm_acp_user' name='drm_acp_user' $checked_user> $lang[drm_user]</label>
		<label class='checkbox'><input type='checkbox' value='drm_acp_system' name='drm_acp_system' $checked_system> $lang[drm_system]</label>
		<label class='checkbox'><input type='checkbox' value='drm_moderator' name='drm_moderator' $checked_moderator> $lang[drm_moderator]</label>
		<label class='checkbox'><input type='checkbox' value='drm_can_publish' name='drm_can_publish' $checked_can_publish> $lang[drm_user_can_publish]</label>
		</div>
	</div>
		";

echo"</div>";


$custom_fields = get_custom_user_fields();
sort($custom_fields);
$cnt_result = count($custom_fields);

if($cnt_result > 0) {

/* tab custom fields */
echo"<h4 title='$lang[legend_custom_fields]'>$lang[legend_custom_fields]</h4>";
echo'<div class="tab-content">'; // tabs content

	for($i=0;$i<$cnt_result;$i++) {	
		if(substr($custom_fields[$i],0,10) == "custom_one") {
			$label = substr($custom_fields[$i],11);
			echo tpl_form_control_group('',$label,"<input type='text' class='input-block-level' name='$custom_fields[$i]' value='" . $$custom_fields[$i] . "'>");
		}	elseif(substr($custom_fields[$i],0,11) == "custom_text") {
			$label = substr($custom_fields[$i],12);
			echo tpl_form_control_group('',$label,"<textarea class='input-block-level' rows='6' name='$custom_fields[$i]'>" . $$custom_fields[$i] . "</textarea>");
		}	elseif(substr($custom_fields[$i],0,14) == "custom_wysiwyg") {
			$label = substr($custom_fields[$i],15);
			echo tpl_form_control_group('',$label,"<textarea class='mceEditor_small' name='$custom_fields[$i]'>" . $$custom_fields[$i] . "</textarea>");
		}		
	}

echo'</div>'; // eo tabs content
/* EOL tab custom fields */

}




echo"</div>";
// EOL fancytabs

//submit form to save data
echo"<div class='formfooter'>";
echo"$delete_button $submit_button";
echo"</div>";

echo"</form>";

echo '</div></div>';


?>