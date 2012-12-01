<?php

//prohibit unauthorized access
require("core/access.php");

// defaults
$show_form = "true";
$db_status = "unlocked";

if($_REQUEST[edituser] != "") {
	$edituser = (int) $_REQUEST[edituser];
} else {
	unset($edituser);
}




/**
 * delete user
 * remove data from the database
 */

if($_POST[delete_the_user]) {

	// connect to database
	$dbh = new PDO("sqlite:".USER_DB);
	
	$sql = "UPDATE fc_user
			SET user_mail = '',
				user_verified = '',
				user_psw = '',
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

if($_POST[save_the_user]) {

foreach($_POST as $key => $val) {
	$$key = @strip_tags($val); 
}

// drm -string- to save in database
$drm_string = "$drm_acp_pages|$drm_acp_files|$drm_acp_user|$drm_acp_system|$drm_acp_editpages|$drm_acp_editownpages|$drm_moderator|$drm_can_publish";

$user_psw_new	= "$_POST[user_psw_new]";
$user_psw_reconfirmation = "$_POST[user_psw_reconfirmation]";

// check psw entries
$set_psw = "false";

if($user_psw_new != "") {

	if($user_psw_new != $user_psw_reconfirmation) {
		$db_status = "locked";
		$error_message .= "$lang[msg_psw_error]<br />";
	} else {
		//generate password hash
		$user_psw = md5("$user_psw_new$user_nick");
		$success_message .= "$lang[msg_psw_changed]<br />";
	}

}


// modus update
if(is_numeric($edituser)) {

	$dbh = new PDO("sqlite:".USER_DB);
	
	$sql = "UPDATE fc_user
			SET user_mail = '$user_mail',
				user_registerdate = '$user_registerdate',
				user_verified = '$user_verified',
				user_psw = '$user_psw',
				user_drm = '$drm_string',
				user_class = '$drm_acp_class',
				user_firstname = '$user_firstname',
				user_lastname = '$user_lastname',
				user_company = '$user_company',
				user_street = '$user_street',
				user_street_nbr = '$user_street_nbr',
				user_zipcode = '$user_zipcode',
				user_city = '$user_city',
				user_newsletter = '$user_newsletter'
			WHERE user_id = $edituser";
										
	$cnt_changes = $dbh->exec($sql);
	
	if($cnt_changes > 0){
		$success_message .= "$lang[msg_user_updated]<br />";
		record_log("$_SESSION[user_nick]","update user id: $edituser via acp","0");
	}

}


//modus new user
if(!is_numeric($edituser)) {

$user_registerdate = time();

/* unique check for user_nick and e-mail */

$dbh = new PDO("sqlite:".USER_DB);

$result = $dbh->query("SELECT user_nick FROM fc_user WHERE user_nick = '$user_nick' ")->fetchAll();

if(count($result) > 0) {
	$error_message .= "$lang[msg_user_exists]<br />";
	$db_status = "locked";
}

$result = $dbh->query("SELECT user_mail FROM fc_user WHERE user_mail = '$user_mail' ")->fetchAll();

if(count($result) > 0) {
	$error_message .= "$lang[msg_usermail_exists]<br />";
	$db_status = "locked";
}


if($db_status == "unlocked") {

$sql = "INSERT INTO fc_user (
					user_id, user_class, user_nick ,
					user_registerdate, user_verified, user_drm ,
					user_firstname , user_lastname , user_company ,
					user_street ,user_street_nbr ,user_zipcode ,
					user_city ,user_mail,user_newsletter,user_psw 
					) VALUES (
					NULL, '$drm_acp_class', '$user_nick',
					'$user_registerdate','$user_verified','$drm_string',
					'$user_firstname','$user_lastname','$user_company',
					'$user_street','$user_street_nbr','$user_zipcode',
					'$user_city','$user_mail','$user_newsletter','$user_psw' 
					) ";
										
$cnt_changes = $dbh->exec($sql);


if($cnt_changes > 0){
	$success_message .= "$lang[msg_new_user_saved]<br />";
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

$user_groups = $_POST[user_groups];
$this_group = $_POST[this_group]; // not checked checkbox
$nbr_of_groups = $_POST[nbr_of_groups];


for($i=0;$i<$nbr_of_groups;$i++) {

if($user_groups[$i] == "") {
	$user_groups[$i] = "$this_group[$i]";
	$sign_out = "true"; // delete user from this list
} else {
	$sign_out = "false";
}


$result = $dbh->query("SELECT * FROM fc_groups WHERE group_id = $user_groups[$i] ");
$result= $result->fetch(PDO::FETCH_ASSOC);

$array_existing_users = explode(" ", $result[group_user]);        // userlist - to array
array_push($array_existing_users, "$enter_user_id");              // add the user
$array_existing_users = array_unique($array_existing_users);      // delete doubles
$existing_users = implode(" ", $array_existing_users);            // generate the new userlist - back to a string

if($sign_out == "true") {
	$existing_users = str_replace("$enter_user_id","",$existing_users);
}

$existing_users = preg_replace("/ +/", ' ', $existing_users);     // delete multiple spaces

$result = $dbh->query("UPDATE fc_groups SET group_user = '$existing_users' WHERE group_id = $user_groups[$i]");

} // eol $i

}


// eol update table ff_groups


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
	echo"<div id='alert alert-error'><p>$error_message</p></div>";
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
	
	
	echo"<h3>$lang[h_modus_edituser] - $user_nick [$user_id]</h3>";
	$submit_button = "<input class='btn btn-success' type='submit' name='save_the_user' value='$lang[update_user]'>";
		
	//no delete_button for myself
	if($user_nick != "$_SESSION[user_nick]"){
		$delete_button = "<input class='btn btn-danger' type='submit' name='delete_the_user' value='$lang[delete_user]' onclick=\"return confirm('$lang[confirm_delete_user]')\">";
		}

} else {
	// modus new user
	
	echo"<h3>$lang[h_modus_newuser]</h3>";
	
	$submit_button = "<input class='btn btn-success' type='submit' name='save_the_user' value='$lang[save_new_user]'>";
	$delete_button = "";
}

if($show_form == "true") {
	include("core/user.edit_form.php");
}

?>