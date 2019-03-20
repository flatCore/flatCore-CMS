<?php

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


$pdo_fields = array(
	'user_mail' => 'STR',
	'user_verified' => 'STR',
	'user_psw_hash' => 'STR',
	'user_drm' => 'STR',
	'user_class' => 'STR',
	'user_firstname' => 'STR',
	'user_lastname' => 'STR',
	'user_company' => 'STR',
	'user_street' => 'STR',
	'user_street_nbr' => 'STR',
	'user_zipcode' => 'STR',
	'user_city' => 'STR',
	'user_newsletter' => 'STR'
);

$pdo_fields_new = array(
	'user_id' => 'NULL',
	'user_nick' => 'STR',
	'user_mail' => 'STR',
	'user_verified' => 'STR',
	'user_registerdate' => 'STR',
	'user_psw_hash' => 'STR',
	'user_drm' => 'STR',
	'user_class' => 'STR',
	'user_firstname' => 'STR',
	'user_lastname' => 'STR',
	'user_company' => 'STR',
	'user_street' => 'STR',
	'user_street_nbr' => 'STR',
	'user_zipcode' => 'STR',
	'user_city' => 'STR',
	'user_newsletter' => 'STR'
);

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
  		$pdo_fields[$cf] = 'STR';
  		$pdo_fields_new[$cf] = 'STR';
  		$pdo_fields_cache[$cf] = 'STR';
  	}
  }      
}


/**
 * delete user
 * remove data from the database
 */

if($_POST['delete_the_user']) {

	// connect to database
	$dbh = new PDO("sqlite:".USER_DB);
	
	$sql = "UPDATE fc_user
			SET user_mail = '',
				user_verified = '',
				user_psw = '',
				user_psw_hash = '',
				user_drm = '',
				user_class = 'deleted',
				user_mail = '',
				user_firstname = '',
				user_lastname = '',
				user_company = '',
				user_street = '',
				user_street_nbr = '',
				user_zipcode = '',
				user_city = '',
				user_newsletter = ''
			WHERE user_id = $edituser";
										
	$cnt_changes = $dbh->exec($sql);
	
	if($cnt_changes > 0){
		$success_message = "$lang[msg_user_deleted]<br />";
		$show_form = "false";
		record_log("$_SESSION[user_nick]","deleted user id: $edituser","0");
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
	$set_psw = "false";
	
	if($_POST['user_psw_new'] != "") {

		if($_POST['user_psw_new'] != $_POST['user_psw_reconfirmation']) {
			$db_status = "locked";
			$error_message .= $lang['msg_psw_error'].'<br>';
		} else {
			//generate password hash
			$user_psw = password_hash($_POST['user_psw_new'], PASSWORD_DEFAULT);
			$success_message .= $lang['msg_psw_changed'].'<br>';
		}

	}
	
	// modus update
	if(is_numeric($edituser)) {

		$dbh = new PDO("sqlite:".USER_DB);
		$sql_u = generate_sql_update_str($pdo_fields,"fc_user","WHERE user_id = $edituser");							
		$sth = $dbh->prepare($sql_u);
		generate_bindParam_str($pdo_fields,$sth);
		
		if($db_status == "unlocked") {
			$sth->bindParam(':user_psw_hash', $user_psw, PDO::PARAM_STR);
		}
		
		$sth->bindParam(':user_drm', $drm_string, PDO::PARAM_STR);
		$sth->bindParam(':user_class', $drm_acp_class, PDO::PARAM_STR);
		$cnt_changes = $sth->execute();
		
		if($_POST['deleteAvatar'] == 'on') {
			$user_avatar_path = '../'. FC_CONTENT_DIR . '/avatars/' . md5($user_nick) . '.png';
			if(is_file($user_avatar_path)) {
				unlink($user_avatar_path);
			}
		}
									
		if($cnt_changes == TRUE) {
			$success_message .= $lang['msg_user_updated'].'<br>';
			record_log($_SESSION['user_nick'],"update user id: $edituser via acp","5");
		}
	}
	
	
	//modus new user
	if(!is_numeric($edituser)) {

		$user_registerdate = time();
		
		/* unique check for user_nick and e-mail */
		
		$dbh = new PDO("sqlite:".USER_DB);
		$result = $dbh->query("SELECT user_nick FROM fc_user WHERE user_nick = '$user_nick' ")->fetchAll();
		
		if(count($result) > 0) {
			$error_message .= $lang['msg_user_exists'].'<br>';
			$db_status = "locked";
		}
		
		$result = $dbh->query("SELECT user_mail FROM fc_user WHERE user_mail = '$user_mail' ")->fetchAll();
		
		if(count($result) > 0) {
			$error_message .= $lang['msg_usermail_exists'].'<br>';
			$db_status = "locked";
		}
		
		if($db_status == "unlocked") {
		
			$user_id = null;
			$sql = generate_sql_insert_str($pdo_fields_new,"fc_user");
			$sth = $dbh->prepare($sql);
			generate_bindParam_str($pdo_fields_new,$sth);
			
			$sth->bindParam(':user_psw_hash', $user_psw, PDO::PARAM_STR);
			$sth->bindParam(':user_drm', $drm_string, PDO::PARAM_STR);
			$sth->bindParam(':user_registerdate', $user_registerdate, PDO::PARAM_STR);
			$sth->bindParam(':user_class', $drm_acp_class, PDO::PARAM_STR);
											
			$cnt_changes = $sth->execute();
			
			if($cnt_changes == TRUE) {
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
	 * update table ff_groups
	 */
	
	
	if($db_status == "unlocked") {

		if($edituser != "") {
			$enter_user_id = $edituser;
		} else {
			$enter_user_id = $dbh->lastInsertId();
		}
		
		$user_groups = $_POST['user_groups'];
		$this_group = $_POST['this_group']; // not checked checkbox
		$nbr_of_groups = $_POST['nbr_of_groups'];
		
		
		for($i=0;$i<$nbr_of_groups;$i++) {
		
			if($user_groups[$i] == "") {
				$user_groups[$i] = "$this_group[$i]";
				$sign_out = "true"; // delete user from this list
			} else {
				$sign_out = "false";
			}
			
			$result = $dbh->query("SELECT * FROM fc_groups WHERE group_id = $user_groups[$i] ");
			$result= $result->fetch(PDO::FETCH_ASSOC);
			
			$array_existing_users = explode(" ", $result['group_user']);        // userlist - to array
			array_push($array_existing_users, "$enter_user_id");              // add the user
			$array_existing_users = array_unique($array_existing_users);      // delete doubles
			$existing_users = implode(" ", $array_existing_users);            // generate the new userlist - back to a string
			
			if($sign_out == "true") {
				$existing_users = str_replace("$enter_user_id","",$existing_users);
			}
			
			$existing_users = preg_replace("/ +/", ' ', $existing_users);     // delete multiple spaces	
			$result = $dbh->query("UPDATE fc_groups SET group_user = '$existing_users' WHERE group_id = $user_groups[$i]");
		
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
	echo"<div class='alert alert-error'><p>$error_message</p></div>";
}


if(is_numeric($edituser)){
	// modus update user
	
	$dbh = new PDO("sqlite:".USER_DB);
	$sql = "SELECT * FROM fc_user WHERE user_id = $edituser";
	$result = $dbh->query($sql);
	$result= $result->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	
	foreach($result as $k => $v) {
	   $$k = stripslashes($v);
	}
	
	$user_avatar_path = '../'. FC_CONTENT_DIR . '/avatars/' . md5($user_nick) . '.png';
	
	echo '<h3>'.$lang['h_modus_edituser'].' - '.$user_nick.' <small>ID: '.$user_id.'</small></h3>';
	$submit_button = "<input class='btn btn-save btn-block' type='submit' name='save_the_user' value='$lang[update_user]'>";
		
	//no delete_button for myself
	if($user_nick != $_SESSION['user_nick']){
		$delete_button = '<input class="btn btn-danger btn-sm btn-block" type="submit" name="delete_the_user" value="'.$lang['delete_user'].'" onclick="return confirm(\''.$lang['confirm_delete_user'].'\')">';
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