<?php


/* PERMISSION DENIED */
if($_SESSION[user_nick] == "") {

	$text = get_textlib("no_access");
	$smarty->assign('page_content', $text);

} else {
/* ACCESS - SHOW THE FORM - FILL WITH DATA */



/* Write Data into the database */
if($_POST[update_profile]) {

// all incoming data -> strip_tags
// limit string to 200 characters
foreach($_POST as $key => $val) {
	$$key = strip_tags(substr($val, 0, 200)); 
}




/* USER SEND NEW PSW */

$update_user_psw = "$_SESSION[user_psw]";

// check psw entries
$set_psw = "false";

if($_POST[s_psw] != "") {
	if($user_psw_new == $user_psw_reconfirmation) {
		//salt n pepper
		$update_user_psw = md5("$s_psw$_SESSION[user_nick]");
	}
}



$dbh = new PDO("sqlite:$fc_db_user");

$sql = "UPDATE fc_user
		SET user_firstname = :s_firstname,
			user_lastname = :s_lastname,
			user_street = :s_street,
			user_street_nbr = :s_nr,
			user_zipcode = :s_zip,
			user_city = :s_city,
			user_public_profile = :about_you,
			user_psw = :update_user_psw
			WHERE user_id = $_SESSION[user_id]";

$sth = $dbh->prepare($sql);

$sth->bindParam(':s_firstname', $s_firstname, PDO::PARAM_STR);
$sth->bindParam(':s_lastname', $s_lastname, PDO::PARAM_STR);
$sth->bindParam(':s_street', $s_street, PDO::PARAM_STR);
$sth->bindParam(':s_nr', $s_nr, PDO::PARAM_STR);
$sth->bindParam(':s_zip', $s_zip, PDO::PARAM_STR);
$sth->bindParam(':s_city', $s_city, PDO::PARAM_STR);
$sth->bindParam(':about_you', $about_you, PDO::PARAM_STR);
$sth->bindParam(':update_user_psw', $update_user_psw, PDO::PARAM_STR);

$count = $sth->execute();
$dbh = null;



if($count == TRUE){
	$smarty->assign("msg_status","success");
	$smarty->assign("register_message","$lang[msg_update_profile]");
} else {
	$smarty->assign("msg_status","error");
	$smarty->assign("register_message","$lang[msg_update_profile_error]");
}




} // eol write data





/* upload avater - rename file - md5(username) */

if($_POST[upload_avatar]) {

$getimagesize = getimagesize($_FILES['avatar']['tmp_name']);

if($getimagesize[2] == 3)   {


$types = array(
        1 => 'gif',
        2 => 'jpg',
        3 => 'png');
        
$filetype = $getimagesize[2];
$extension = $types[$filetype];


 if($_FILES['avatar']['size'] <  102400) {
 
	 $new_img_name = md5($_SESSION[user_nick]);
 
   move_uploaded_file($_FILES['avatar']['tmp_name'], "content/avatars/$new_img_name.$extension");
   $smarty->assign("msg_status","success");
   $smarty->assign("register_message","$lang[msg_upload_avatar_success]");
  } else {
	 $smarty->assign("msg_status","error");
   $smarty->assign("register_message","$lang[msg_upload_avatar_filesize]");
  }

} else {
  $smarty->assign("msg_status","error");
  $smarty->assign("register_message","$lang[msg_upload_avatar_filetype]");
}

}

// eol POST[upload_avatar]



/**
 * DELETE THE ACCOUNT
 * We delete all informations, except the user name
 */

if($_POST[delete_my_account]) {

$dbh = new PDO("sqlite:$fc_db_user");


$sql = "UPDATE fc_user
		SET user_firstname = '',
			user_lastname = '',
			user_street = '',
			user_street_nbr = '',
			user_zipcode = '',
			user_city = '',
			user_public_profile = '',
			user_psw = '',
			user_mail = '',
			user_class = 'deleted'
			WHERE user_id = $_SESSION[user_id]";




$count = $dbh->exec($sql);
$dbh = null;



if($count > 0){
	$smarty->assign("msg_status","success");
	$smarty->assign("register_message","$lang[msg_delete_account_success]");
	session_destroy();
	unset($_SESSION[user_nick]);
} else {
	$smarty->assign("msg_status","error");
	$smarty->assign("register_message","$lang[msg_delete_account_error]");
}


} // eol DELETE THE ACCOUNT




// show data in form


if($fc_mod_rewrite == "auto") {
	$form_url = FC_INC_DIR . "/system/profile/";
} else {
	$form_url = "$_SERVER[PHP_SELF]?p=profile";
}

if(is_file("content/avatars/".md5($_SESSION[user_nick]) . ".png")){

	$avatar_url = FC_INC_DIR . "/content/avatars/".md5($_SESSION[user_nick]) . ".png";
	$smarty->assign("avatar_url","$avatar_url");
	
	$link_avatar_delete = "<a href='$_SERVER[PHP_SELF]?p=profile&delete_avatar=true'>$lang[link_delete_avatar]</a>";
	$link_avatar_delete_url = "$_SERVER[PHP_SELF]?p=profile&delete_avatar=true";
	$link_avatar_delete_text = "$lang[link_delete_avatar]";
	
	$smarty->assign("link_avatar_delete","$link_avatar_delete");
	$smarty->assign("link_avatar_delete_url","$link_avatar_delete_url");
	$smarty->assign("link_avatar_delete_text","$link_avatar_delete_text");
	
	if($delete_avatar == true) {
		unlink("content/avatars/".md5($_SESSION[user_nick]) . ".png");
		$smarty->assign("avatar_url","");
		$smarty->assign("link_avatar_delete","");
	}
}


$smarty->assign('form_url', $form_url);


$get_my_userdata = get_my_userdata();
//example: $get_my_userdata[user_nick]

$smarty->assign("user_nick","$_SESSION[user_nick]");
$smarty->assign("get_firstname","$get_my_userdata[user_firstname]");
$smarty->assign("get_lastname","$get_my_userdata[user_lastname]");
$smarty->assign("get_street","$get_my_userdata[user_street]");
$smarty->assign("get_nr","$get_my_userdata[user_street_nbr]");
$smarty->assign("get_zip","$get_my_userdata[user_zipcode]");
$smarty->assign("get_city","$get_my_userdata[user_city]");
$smarty->assign("send_about","$get_my_userdata[user_public_profile]");

$output = $smarty->fetch("profile_main.tpl");
$smarty->assign('page_content', $output);

}




?>