<?php



/**
 * Return all registered user -> user_nick
 * $db_host = path to the database file
 */


function get_all_usernames($db_host) {

    $dbh = dbconnect($db_type, $db_host, $db_user, $db_pass, $db_name);
	$sql = "SELECT user_nick FROM ".DB_PREFIX."user";
  foreach (dbquery($sql) as $row) {
		$result[] = $row;
  }
  $dbh = null;
	return($result);
}




/**
 * Return all registered user -> user_mail
 * $db_host = path to the database file
 */

function get_all_usermail($db_host) {

    global $db_type, $db_host, $db_user, $db_pass, $db_name;

	$dbh = dbconnect($db_type, $db_host, $db_user, $db_pass, $db_name);
	$sql = "SELECT user_mail FROM ".DB_PREFIX."user";
  foreach (dbquery($sql) as $row) {
		$result[] = $row;
  }
  $dbh = null;
	return($result);
}





/**
 * Return all user informations
 * from the database
 */

function get_my_userdata() {

	global $fc_db_user, $db_type, $db_host, $db_user, $db_pass, $db_name;

    if($db_type == 'sqlite') $db_host = $fc_db_user;
	$dbh = dbconnect($db_type, $db_host, $db_user, $db_pass, $db_name);
	$sql = "SELECT * FROM ".DB_PREFIX."user WHERE user_id = '$_SESSION[user_id]' AND user_verified = 'verified' ";
    $result = dbarray(dbquery($sql));

    $dbh = null;

	return($result);

}




/**
 * Return user information
 * input = E-Mail Adress
 */


function get_userdata_by_mail($mail) {

	global $fc_db_user, $db_type, $db_host, $db_user, $db_pass, $db_name;

    if($db_type == 'sqlite') $db_host = $fc_db_user;
	$dbh = dbconnect($db_type, $db_host, $db_user, $db_pass, $db_name);
	$mail = $dbh -> quote($mail);

	$sql = "SELECT * FROM ".DB_PREFIX."user WHERE user_mail = $mail AND user_verified = 'verified' ";
	
	$result = dbarray(dbquery($sql));

    $dbh = null;

	return($result);
}






/**
 * Return user information
 * input = password-reset-token
 */

function get_userdata_by_token($token) {

	global $fc_db_user, $db_type, $db_host, $db_user, $db_pass, $db_name;

    if($db_type == 'sqlite') $db_host = $fc_db_user;
	$dbh = dbconnect($db_type, $db_host, $db_user, $db_pass, $db_name);
	$token = $dbh -> quote($token);
	$sql = "SELECT user_id, user_nick, user_mail FROM ".DB_PREFIX."user WHERE user_reset_psw = $token ";
	
	$result = dbarray(dbquery($sql));

    $dbh = null;

	return($result);

}








/**
 * Generate random Password
 */


function randpsw($length=8) {


	$chars = '123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';

	$random_s = '';
	$cnt_chars = strlen($chars);
	for($i=0;$i<$length;$i++) {
		$random_s .= $chars[mt_rand(0, $cnt_chars - 1)];
	}
	return $random_s;
}



/**
 * user login
 */
 
function fc_user_login($user,$psw,$acp=NULL,$remember=NULL) {

	global $fc_db_user, $db_type, $db_host, $db_user, $db_pass, $db_name;
	unset($result);
	
	if($acp == TRUE) {
		$fc_db_user = '../'.$fc_db_user;
	}
		
	$login_hash  = md5($psw.$user);

    if($db_type == 'sqlite') $db_host = $fc_db_user;
	$dbh = dbconnect($db_type, $db_host, $db_user, $db_pass, $db_name);

	$sql_get_hash = "SELECT user_psw_hash FROM ".DB_PREFIX."user WHERE user_nick = :login_name AND user_verified = 'verified'";
    $hash = dbarray(dbquery($sql_get_hash, array(':login_name'=>$user)));
	
	
	if(password_verify($psw, $hash['user_psw_hash'])) {
		/* valid psw */
		$sql = "SELECT * FROM ".DB_PREFIX."user	WHERE user_nick = :user_nick AND user_verified = 'verified'";
        $result = dbarray(dbquery($sql, array(':user_nick'=>$user)));
	} else {
		$sql = "SELECT * FROM ".DB_PREFIX."user	WHERE user_nick = :login_name AND user_psw = :login_hash AND user_verified = 'verified'";
        $result = dbarray(dbquery($sql, array(':login_name'=>$user, ':login_hash'=>$login_hash)));
	}
	
	$cnt_result = count($result);

	if($cnt_result>1) {
		

		fc_start_user_session($result);

		/* store new password_hash */
		if(empty($result['user_psw_hash'])) {
			$user_psw_hash = password_hash($psw, PASSWORD_DEFAULT);
			$sql = 'UPDATE '.DB_PREFIX.'user SET user_psw_hash = :user_psw_hash WHERE user_psw = :login_hash';
            dbquery($sql, array(':user_psw_hash'=>$user_psw_hash, ':login_hash'=>$login_hash));
		}
		
		/* if user_psw_hash is not empty, delete user_psw */
		if(!empty($result['user_psw_hash'])) {
			$sql = 'UPDATE '.DB_PREFIX.'user SET user_psw = NULL WHERE user_nick = :user_nick';
            dbquery($sql, array(':user_nick'=>$user));
		}

		/* set cookie to remember user */
		if($remember == TRUE) {
			$identifier = randpsw($length=24);
			$securitytoken = randpsw($length=24);
			
			$sql = 'INSERT INTO '.DB_PREFIX.'tokens (user_id, identifier, securitytoken, time) VALUES (:user_id, :identifier, :securitytoken, :time)';
            dbquery($sql, array(':user_id'=>$result['user_id'], ':identifier'=>$identifier, ':securitytoken'=>sha1($securitytoken), ':time'=>time()));
			setcookie("identifier",$identifier,time()+(3600*24*365)); //1 Jahr Gültigkeit
			setcookie("securitytoken",$securitytoken,time()+(3600*24*365)); //1 Jahr Gültigkeit			
		}
		
		
		if(($acp == TRUE) AND ($_SESSION['user_class'] == "administrator")) {
			header("location:acp.php");
		}
		

	} else {
		session_destroy();
	}
	
	
	$dbh = null;

}


/**
 * start new session with user data
 */
 
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





?>
