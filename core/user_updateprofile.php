<?php

/* PERMISSION DENIED */
if($_SESSION['user_nick'] == "") {

	$text = get_textlib("no_access",$languagePack);
	$smarty->assign('page_content', $text, true);

} else {

	$get_my_userdata = get_my_userdata();

	/* Write Data into the database */
	if(isset($_POST['update_profile'])) {

		// all incoming data -> strip_tags
		// limit string to 200 characters
		foreach($_POST as $key => $val) {
			$$key = strip_tags(substr($val, 0, 200)); 
		}
		
		
		
		$user_psw_hash = $get_my_userdata['user_psw_hash'];
		
		// check psw entries
		$set_psw = "false";
		
		if(isset($_POST['s_psw']) AND trim($_POST['s_psw']) != '') {
			/* USER SEND NEW PSW */
			if($_POST['s_psw'] == $_POST['s_psw_repeat']) {
				$user_psw_hash = password_hash($_POST['s_psw'], PASSWORD_DEFAULT);
			}
		}
		
		$count = $db_user->update("fc_user", [
				"user_firstname" => "$s_firstname",
				"user_lastname" => "$s_lastname",
				"user_street" => "$s_street",
				"user_street_nbr" => "$s_nr",
				"user_zipcode" => "$s_zip",
				"user_city" => "$s_city",
				"user_public_profile" => "$about_you",
				"user_psw_hash" => "$user_psw_hash"
					], [
				"user_id" => $_SESSION['user_id']
			]);
		
		
		if($count->rowCount() == 1){
			$smarty->assign("msg_status","alert alert-success",true);
			$smarty->assign("register_message",$lang['msg_update_profile'],true);
		} else {
			$smarty->assign("msg_status","alert alert-danger",true);
			$smarty->assign("register_message",$lang['msg_update_profile_error'],true);
		}

	}
	

	/**
	 * upload avatar
	 * convert to png and square format
	 * rename file to md5(username)
	 */

	if(isset($_POST['upload_avatar'])) {
	
		$uploads_dir = "content/avatars";
		$max_width = 100;
		
		$tmp_name = $_FILES['avatar']['tmp_name'];
		$org_name = $_FILES['avatar']['name'];
		$new_name = md5($_SESSION['user_nick']);
		$new_avatar_src = $uploads_dir.'/'.$new_name.'.png';
		
		list($width_upl, $height_upl, $type_upl) = getimagesize($tmp_name);
    
		if ($width_upl > $height_upl) {
		  $y = 0;
		  $x = ($width_upl - $height_upl) / 2;
		  $smallestSide = $height_upl;
		} else {
		  $x = 0;
		  $y = ($height_upl - $width_upl) / 2;
		  $smallestSide = $width_upl;
		}
    
		$imgt = '';
		if($type_upl==1) { $imgt = imagecreatefromgif($tmp_name);  }
		if($type_upl==2) { $imgt = imagecreatefromjpeg($tmp_name);  }
		if($type_upl==3) { $imgt = imagecreatefrompng($tmp_name);  }
		
		
		if($imgt != '') {

			$new_image = imagecreatetruecolor($max_width, $max_width);
			imagecopyresampled($new_image, $imgt, 0, 0, $x, $y, $max_width, $max_width, $smallestSide, $smallestSide);
			
					
			if(imagepng($new_image, $new_avatar_src,9) === true) {
				$smarty->assign("msg_status","alert alert-success",true);
				$smarty->assign("register_message",$lang['msg_upload_avatar_success'],true);			
			}
			imagedestroy($new_image);
		
		} else {
			$smarty->assign("msg_status","alert alert-danger",true);
			$smarty->assign("register_message",$lang['msg_upload_avatar_filetype'],true);
		}
		
	}
	

	/**
	 * DELETE THE ACCOUNT
	 * We delete all informations, except the user name and user id
	 */
	
	if(isset($_POST['delete_my_account'])) {

		$delete_id = (int) $_SESSION['user_id'];
			
		$count = $db_user->update("fc_user", [
				"user_firstname" => "",
				"user_lastname" => "",
				"user_street" => "",
				"user_street_nbr" => "",
				"user_zipcode" => "",
				"user_city" => "",
				"user_public_profile" => "",
				"user_psw_hash" => "",
				"user_psw" => "",
				"user_mail" => "",
				"user_registerdate" => "",
				"user_drm" => "",
				"user_verified" => "",
				"user_class" => "deleted"
					], [
				"user_id" => $delete_id
			]);
		
		if($count->rowCount() == 1) {
			$smarty->assign("msg_status","alert alert-success",true);
			$smarty->assign("register_message",$lang['msg_delete_account_success'],true);
			session_destroy();
			unset($_SESSION['user_nick']);
		} else {
			$smarty->assign("msg_status","alert alert-warning",true);
			$smarty->assign("register_message",$lang['msg_delete_account_error'],true);
		}
	}
	
	
	
	
	// show data in form	
	if(is_file("content/avatars/".md5($_SESSION['user_nick']) . ".png")) {

		$avatar_url = FC_INC_DIR . "/content/avatars/".md5($_SESSION['user_nick']) . ".png";
		$smarty->assign("avatar_url","$avatar_url");
		
		$link_avatar_delete_url = $fc_base_url.'profile/';
		$link_avatar_delete = '<a href="'.$link_avatar_delete_url.'">'.$lang['link_delete_avatar'].'</a>';
		$link_avatar_delete_text = $lang['link_delete_avatar'];
		
		$smarty->assign("link_avatar_delete","$link_avatar_delete",true);
		$smarty->assign("link_avatar_delete_url","$link_avatar_delete_url",true);
		$smarty->assign("link_avatar_delete_text","$link_avatar_delete_text",true);
	}
	
	/* delete avatar */
	if(isset($_POST['delete_avatar'])) {
		unlink("content/avatars/".md5($_SESSION['user_nick']) . ".png");
		$smarty->assign("avatar_url","",true);
		$smarty->assign("link_avatar_delete","",true);
	}
	
	
	if($page_contents['page_permalink'] != '') {
		$smarty->assign("form_url", '/'.$page_contents['page_permalink']);
	} else {
		$form_url = FC_INC_DIR . "/profile/";
		$smarty->assign('form_url', $form_url);
	}
	
	
	$get_my_userdata = get_my_userdata();
	//example: $get_my_userdata['user_nick']
	
	$smarty->assign("user_nick",$_SESSION['user_nick'],true);
	$smarty->assign("get_firstname",$get_my_userdata['user_firstname'],true);
	$smarty->assign("get_lastname",$get_my_userdata['user_lastname'],true);
	$smarty->assign("get_street",$get_my_userdata['user_street'],true);
	$smarty->assign("get_nr",$get_my_userdata['user_street_nbr'],true);
	$smarty->assign("get_zip",$get_my_userdata['user_zipcode'],true);
	$smarty->assign("get_city",$get_my_userdata['user_city'],true);
	$smarty->assign("send_about",$get_my_userdata['user_public_profile'],true);
	
	$output = $smarty->fetch("profile_main.tpl",$cache_id);
	$smarty->assign('page_content', $output, true);

}

?>