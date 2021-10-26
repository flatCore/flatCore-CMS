<?php

/*
 * include this file in all acp scripts
 * to check the session login var 'user_class'
 *
*/

if(!function_exists('randpsw')) {
	function randpsw($length=8) {
		$chars = '123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
		$random_s = '';
		$cnt_chars = strlen($chars);
		for($i=0;$i<$length;$i++) {
			$random_s .= $chars[mt_rand(0, $cnt_chars - 1)];
		}
		return $random_s;
	}
}

if(!function_exists('fc_start_user_session')) {
	function fc_start_user_session($ud) {
	
		$_SESSION['user_id'] = $ud['user_id'];
		$_SESSION['user_nick'] = $ud['user_nick'];
		$_SESSION['user_mail'] = $ud['user_mail'];
		$_SESSION['user_class'] = $ud['user_class'];
		$_SESSION['user_psw'] = $ud['user_psw'];
		$_SESSION['user_firstname'] = $ud['user_firstname'];
		$_SESSION['user_lastname'] = $ud['user_lastname'];
		$_SESSION['user_hash'] = md5($ud['user_nick']);
		
		$arr_drm = explode("|", $ud['user_drm']);
		
		if($arr_drm[0] == "drm_acp_pages")	{  $_SESSION['acp_pages'] = "allowed";  }
		if($arr_drm[1] == "drm_acp_files")	{  $_SESSION['acp_files'] = "allowed";  }
		if($arr_drm[2] == "drm_acp_user")	{  $_SESSION['acp_user'] = "allowed";  }
		if($arr_drm[3] == "drm_acp_system")	{  $_SESSION['acp_system'] = "allowed";  }
		if($arr_drm[4] == "drm_acp_editpages")	{  $_SESSION['acp_editpages'] = "allowed";  }
		if($arr_drm[5] == "drm_acp_editownpages")	{  $_SESSION['acp_editownpages'] = "allowed";  }
		if($arr_drm[6] == "drm_moderator")	{  $_SESSION['drm_moderator'] = "allowed";  }
		if($arr_drm[7] == "drm_can_publish")	{  $_SESSION['drm_can_publish'] = "true";  }
		
	}
}

if(($_SESSION['user_class'] != 'administrator') && isset($_COOKIE['identifier']) && isset($_COOKIE['securitytoken'])) {
	$identifier = $_COOKIE['identifier'];
	$securitytoken = $_COOKIE['securitytoken'];
		
	$token_row = $db_user->get("fc_tokens",["securitytoken","identifier","user_id"],["identifier" => $identifier]);
	
	//Token is correct
	if(sha1($securitytoken) == $token_row['securitytoken']) {
		// update Token
		$new_securitytoken = randpsw($length=24);
		$new_securitytoken_hashed = sha1($new_securitytoken);
		
		$db_user->update("fc_tokens",[
			"securitytoken" => "$new_securitytoken_hashed"
			],[
			"identifier" => $identifier
		]);
		
		setcookie("identifier",$identifier,time()+(3600*24*365)); //1 Jahr Gültigkeit
		setcookie("securitytoken",$new_securitytoken,time()+(3600*24*365)); //1 Jahr Gültigkeit

		$user_data = $db_user->select("fc_user","*",[
			"user_id" => $token_row['user_id']
		]);
		fc_start_user_session($user_data);
				
		$_SESSION['user_class'] = 'administrator';
	} else {
		header("location:../index.php");
		die("PERMISSION DENIED");
	}
}




if($_SESSION['user_class'] != "administrator"){
	//move back to site
	header("location:../index.php");
	//or die
	die("PERMISSION DENIED!");
}


/* check token */

if(!isset($_SESSION['token'])) {
	die('Error: CSRF Token is invalid');
}

/* stop all $_POST actions if csrf token is empty or invalid */

if(!empty($_POST)) {
	if(empty($_POST['csrf_token'])) {
		die('Error: CSRF Token is empty');
	}
	if($_POST['csrf_token'] !== $_SESSION['token']) {
		die('Error: CSRF Token is invalid');	
	}
}

$hidden_csrf_token = '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';

?>