<?php

//prohibit unauthorized access
require("core/access.php");



if($_GET[sub] == "new"){
$sub = "new";
} else {
$sub = "edit";
}


/*
print form
*/

echo"<form action='$_SEVER[PHP_SELF]?tn=user&sub=$sub&edituser=$edituser' method='POST'>";

// fancytabs
echo"<div id='tabsBlock'>";

/* tab_info */
echo"<h4 title='$lang[tab_user_info_description]'>$lang[tab_user_info]</h4>";



echo"<div class='tab-content'>";



echo"	<div class='form-line'>
		<label>$lang[f_user_nick]</label>
		<div class='form-controls'><input class='span6' type='text' name='user_nick' value='$user_nick'></div>
		</div>";

if($user_registerdate != "") {
$show_registerdate = @date("d.m.Y H:i:s",$user_registerdate);
}

echo"	<div class='form-line'>
		<label>$lang[f_user_registerdate]</label>
		<div class='form-controls'>$show_registerdate</div>
		</div>";
echo"<input type='hidden' name='user_registerdate' value='$user_registerdate'>";
		
		


// $user_verified

if($user_verified == "waiting"){
	$checked_w = "checked";
}
elseif($user_verified == "verified"){
	$checked_v = "checked";
}
elseif($user_verified == "paused"){
	$checked_p = "checked";
}
else {
	$checked_w = "checked";
}

echo"<div class='form-line'>
		<label>$lang[f_user_status]</label>
		<div class='form-controls'>
		<ul class='unstyled'>
		<li><input type='radio' value='waiting' name='user_verified' $checked_w> <span class='label label-info'>$lang[f_user_select_waiting]</span></li>
		<li><input type='radio' value='verified' name='user_verified' $checked_v> <span class='label label-success'>$lang[f_user_select_verified]</span></li>
		<li><input type='radio' value='paused' name='user_verified' $checked_p> <span class='label label-important'>$lang[f_user_select_paused]</span></li>
		</ul>
		</div>
	</div>";


echo"<div class='form-line'>
		<label>$lang[f_user_groups]</label>
		<div class='form-controls'>";

$result = get_all_groups(); //@ core/functions.php

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
echo"<input type='checkbox' name='user_groups[$i]' value='$get_group_id' $checked>";
echo" $get_group_name (ID: $get_group_id)<br />";

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


echo"<div class='form-line'>
		<label>$lang[f_user_newsletter]</label>
		<div class='form-controls'>
			<input type='radio' name='user_newsletter' value='none' $checked1> $lang[f_user_newsletter_none]<br />
			<input type='radio' name='user_newsletter' value='html' $checked2> $lang[f_user_newsletter_html]<br />
			<input type='radio' name='user_newsletter' value='text' $checked3> $lang[f_user_newsletter_text]<br />
		</div>
	</div>";


echo"</div>"; // eol .formbox


/* EOL tab_info ### ### ### */



/* tab_contact */
echo"<h4 title='$lang[tab_contact_description]'>$lang[tab_contact]</h4>";


echo"<div class='formbox'>";

echo"<div class='form-line'>
		<label>$lang[f_user_firstname]</label>
		<div class='form-controls'><input class='span6' type='text' name='user_firstname' value='$user_firstname'></div>
	</div>";

echo"<div class='form-line'>
	<label>$lang[f_user_lastname]</label>
	<div class='form-controls'><input class='span6' type='text' name='user_lastname' value='$user_lastname'></div>
	</div>";

echo"<div class='form-line'>
		<label>$lang[f_user_mail]</label>
		<div class='form-controls'><input class='span6' type='text' name='user_mail' value='$user_mail'></div>
	</div>";

echo"<div class='form-line'>
		<label>$lang[f_user_company]</label>
		<div class='form-controls'><input class='span6' type='text' name='user_company' value='$user_company'></div>
	</div>";

echo"<div class='form-line'>
		<label>$lang[f_user_street]/$lang[f_user_street_nbr]</label>
		<div class='form-controls'><input class='span4' type='text' name='user_street' value='$user_street'> / 
		<input class='span2' type='text' name='user_street_nbr' value='$user_street_nbr'></div>
	</div>";

echo"<div class='form-line'>
		<label>$lang[f_user_zipcode]/$lang[f_user_city]</label>
		<div class='form-controls'><input class='span2' type='text' name='user_zipcode' value='$user_zipcode'> / 
		<input class='span4' type='text' name='user_city' value='$user_city'></div>
	</div>";

echo"</div>"; // eol .formbox

/* EOL tab_contact ### ### ### */

/* tab_password */
echo"<h4 title='$lang[tab_psw_description]'>$lang[tab_psw]</h4>";


echo"<div class='formbox'>";

echo"<div class='form-line'>
		<label>$lang[f_user_psw]</label>
		<div class='form-controls'>
		<p>$lang[f_user_psw_description]</p>
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

echo"<div class='form-line'>
		<label>$lang[f_user_drm]</label>
		<div class='form-controls'>
		<input type='checkbox' value='administrator' name='drm_acp_class' $checked_class> <b>$lang[drm_administrator]</b><br />
		<input type='checkbox' value='drm_acp_pages' name='drm_acp_pages' $checked_pages> $lang[drm_pages]<br />
		<input type='checkbox' value='drm_acp_editpages' name='drm_acp_editpages' $checked_editpages> $lang[drm_editpages]<br />
		<input type='checkbox' value='drm_acp_editownpages' name='drm_acp_editownpages' $checked_editownpages> $lang[drm_editownpages]<br />
		<input type='checkbox' value='drm_acp_files' name='drm_acp_files' $checked_files> $lang[drm_files]<br />
		<input type='checkbox' value='drm_acp_user' name='drm_acp_user' $checked_user> $lang[drm_user]<br />
		<input type='checkbox' value='drm_acp_system' name='drm_acp_system' $checked_system> $lang[drm_system]<br />
		<input type='checkbox' value='drm_moderator' name='drm_moderator' $checked_moderator> $lang[drm_moderator]<br />
		<input type='checkbox' value='drm_can_publish' name='drm_can_publish' $checked_can_publish> $lang[drm_user_can_publish]<br />
		</div>
	</div>
		";

echo"</div>"; // eol .formbox







echo"</div>";
// EOL fancytabs

//submit form to save data
echo"<div class='formfooter'>";
echo"$delete_button $submit_button";
echo"</div>";

echo"</form>";




?>