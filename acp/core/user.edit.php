<?php
//error_reporting(E_ALL ^E_NOTICE);
//prohibit unauthorized access
require 'core/access.php';

// defaults
$show_form = "true";
$db_status = "unlocked";

if($_REQUEST['edituser'] != "") {
	$edituser = (int) $_REQUEST['edituser'];
} else {
	unset($edituser);
}


/**
 * if we have custom fields
 * expand the array ($pdo_fields...)
 */
 
if(preg_match("/custom_/i", implode(",", array_keys($_POST))) ){
  $custom_fields = get_custom_user_fields();
  $cnt_result = count($custom_fields);
  
  for($i=0;$i<$cnt_result;$i++) {
  	if(substr($custom_fields[$i],0,7) == "custom_") {
  		$cf = $custom_fields[$i];
  		$custom_fields[] = $cf;
  	}
  }      
}


/**
 * delete user
 * remove data from the database
 */

if($_POST['delete_the_user']) {
		
		$columns_update = [
			"user_psw_hash" => "",
			"user_mail" => "",
			"user_verified" => "",
			"user_registerdate" => "",
			"user_drm" => "",
			"user_class" => "deleted",
			"user_firstname" => "",
			"user_lastname" => "",
			"user_company" => "",
			"user_street" => "",
			"user_street_nbr" => "",
			"user_zipcode" => "",
			"user_city" => "",
			"user_newsletter" => ""
		];
		
		/* add the custom fields */
		foreach($custom_fields as $f) {
			$columns_update[$f] = "";
		}
										
		$cnt_changes = $db_user->update("fc_user",$columns_update, [
			"user_id" => $edituser
		]);
	
	if($cnt_changes->rowCount() > 0) {
		$success_message = "$lang[msg_user_deleted]<br />";
		$show_form = "false";
		record_log($_SESSION['user_nick'],"deleted user id: $edituser","0");
	}
	
	unset($edituser);

} // EOL delete user



/**
 * new user or update user
 */

if($_POST['save_the_user']) {

	foreach($_POST as $key => $val) {
		$$key = @strip_tags($val); 
	}
	
	// drm -string- to save in database
	$drm_string = "$drm_acp_pages|$drm_acp_files|$drm_acp_user|$drm_acp_system|$drm_acp_editpages|$drm_acp_editownpages|$drm_moderator|$drm_can_publish";
	
	$user_psw_new	= $_POST['user_psw_new'];
	$user_psw_reconfirmation = $_POST['user_psw_reconfirmation'];
	
	// check psw entries
	$set_psw = 'false';
	
	if($_POST['user_psw_new'] != "") {

		if($_POST['user_psw_new'] != $_POST['user_psw_reconfirmation']) {
			$db_status = "locked";
			$error_message .= $lang['msg_psw_error'].'<br>';
		} else {
			//generate password hash
			$user_psw = password_hash($_POST['user_psw_new'], PASSWORD_DEFAULT);
			$success_message .= $lang['msg_psw_changed'].'<br>';
			$set_psw = 'true';
		}

	}
	
	// modus update
	if(is_numeric($edituser)) {
		
		$columns_update = [
			"user_nick" => "$user_nick",
			"user_mail" => "$user_mail",
			"user_verified" => "$user_verified",
			"user_registerdate" => "$user_registerdate",
			"user_drm" => "$drm_string",
			"user_class" => "$drm_acp_class",
			"user_firstname" => "$user_firstname",
			"user_lastname" => "$user_lastname",
			"user_company" => "$user_company",
			"user_street" => "$user_street",
			"user_street_nbr" => "$user_street_nbr",
			"user_zipcode" => "$user_zipcode",
			"user_city" => "$user_city",
			"user_newsletter" => "$user_newsletter"
		];
		
		if($set_psw == "true") {
			$columns_update['user_psw_hash'] = "$user_psw";
		}
		
		/* add the custom fields */
		foreach($custom_fields as $f) {
			$columns_update[$f] = "${$f}";
		}
		
		
		$cnt_changes = $db_user->update("fc_user",$columns_update, [
			"user_id" => $edituser
		]);
		
		if($_POST['deleteAvatar'] == 'on') {
			$user_avatar_path = '../'. FC_CONTENT_DIR . '/avatars/' . md5($user_nick) . '.png';
			if(is_file($user_avatar_path)) {
				unlink($user_avatar_path);
			}
		}
									
		if($cnt_changes->rowCount() > 0) {
			$success_message .= $lang['msg_user_updated'].'<br>';
			record_log($_SESSION['user_nick'],"update user id: $edituser via acp","5");
		}
	}
	
	
	//modus new user
	if(!is_numeric($edituser)) {

		$user_registerdate = time();
		
		/* unique check for user_nick and e-mail */
				
		$check_user = $db_user->get("fc_user", "user_nick", [
			"user_nick" => "$user_nick"
		]);
		
		if(count($check_user) > 0) {
			$error_message .= $lang['msg_user_exists'].'<br>';
			$db_status = "locked";
		}
		
		$check_mail = $db_user->get("fc_user", "user_mail", [
			"user_mail" => "$user_mail"
		]);
		
		if(count($check_mail) > 0) {
			$error_message .= $lang['msg_usermail_exists'].'<br>';
			$db_status = "locked";
		}
		
		if($user_nick == '') {
			$error_message .= $lang['msg_user_mandatory'].'<br>';
			$db_status = "locked";			
		}
		
		if($db_status == "unlocked") {
			
			$columns_new = [
				"user_nick" => "$user_nick",
				"user_mail" => "$user_mail",
				"user_verified" => "$user_verified",
				"user_registerdate" => "$user_registerdate",
				"user_psw_hash" => "$user_psw",
				"user_drm" => "$drm_string",
				"user_class" => "$drm_acp_class",
				"user_firstname" => "$user_firstname",
				"user_lastname" => "$user_lastname",
				"user_company" => "$user_company",
				"user_street" => "$user_street",
				"user_street_nbr" => "$user_street_nbr",
				"user_zipcode" => "$user_zipcode",
				"user_city" => "$user_city",
				"user_newsletter" => "$user_newsletter"
			];
			
			/* add the custom fields */
			foreach($custom_fields as $f) {
				$columns_new[$f] = "${$f}";
			}
			
			$cnt_changes = $db_user->insert("fc_user",$columns_new);
		
			$edituser = $db_user->id();
		
		
			if($cnt_changes->rowCount() > 0) {
				$success_message .= $lang['msg_new_user_saved'].'<br>';
				record_log($_SESSION['user_nick'],"new user <i>$user_nick</i>","5");
			} else {
				print_r($dbh->errorInfo());
			}
													
			// don't show the form after saving
			$show_form = "false";
		}
	}
	
	
	
	/**
	 * update table fc_groups
	 */
	
	
	if($db_status == "unlocked") {

		if($edituser != "") {
			$enter_user_id = $edituser;
		} else {
			$enter_user_id = $db_user->id();
		}
		
		$user_groups = $_POST['user_groups'];
		$this_group = $_POST['this_group']; // not checked checkbox (hidden field)
		$nbr_of_groups = $_POST['nbr_of_groups'];
		
		
		for($i=0;$i<$nbr_of_groups;$i++) {
		
			if($user_groups[$i] == "") {
				$user_groups[$i] = $this_group[$i];
				$sign_out = "true"; // delete user from this list
			} else {
				$sign_out = "false";
			}

		}
	}
	
}

/* EOL write data */


if($db_status == "locked") {
	unset($success_message);
}


//print message(s)

if($success_message != ""){
	echo"<div class='alert alert-success'><p>$success_message</p></div>";
}

if($error_message != ""){
	echo"<div class='alert alert-danger'><p>$error_message</p></div>";
}


if(is_numeric($edituser)){
	// modus update user
		
	$get_user = $db_user->get("fc_user", "*", [
		"user_id" => "$edituser"
	]);
	
	foreach($get_user as $k => $v) {
	   $$k = stripslashes($v);
	}
	
	$user_avatar_path = '../'. FC_CONTENT_DIR . '/avatars/' . md5($user_nick) . '.png';
	
	echo '<h3>'.$lang['h_modus_edituser'].' - '.$user_nick.' <small>ID: '.$user_id.'</small></h3>';
	$submit_button = "<input class='btn btn-save w-100' type='submit' name='save_the_user' value='$lang[update_user]'>";
		
	//no delete_button for myself
	if($user_nick != $_SESSION['user_nick']){
		$delete_button = '<input class="btn btn-danger btn-sm w-100" type="submit" name="delete_the_user" value="'.$lang['delete_user'].'" onclick="return confirm(\''.$lang['confirm_delete_user'].'\')">';
	}

} else {
	// modus new user
	echo"<h3>$lang[h_modus_newuser]</h3>";
	$submit_button = "<input class='btn btn-save' type='submit' name='save_the_user' value='$lang[save_new_user]'>";
	$delete_button = "";
}

if($show_form == "true") {
	include 'core/user.edit_form.php';
}

?>