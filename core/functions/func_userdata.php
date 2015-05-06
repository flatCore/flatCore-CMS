<?php



/**
 * Return all registered user -> user_nick
 * $db = path to the database file
 */


function get_all_usernames($db) {

// connect to database
$dbh = new PDO("sqlite:$db");

	$sql = "SELECT user_nick FROM fc_user";
	
   foreach ($dbh->query($sql) as $row) {
     $result[] = $row;
   }  

return($result);

}




/**
 * Return all registered user -> user_mail
 * $db = path to the database file
 */

function get_all_usermail($db) {

// connect to database
$dbh = new PDO("sqlite:$db");

	$sql = "SELECT user_mail FROM fc_user";
	
   foreach ($dbh->query($sql) as $row) {
     $result[] = $row;
   }  

return($result);

}





/**
 * Return all user informations
 * from the database
 */

function get_my_userdata() {

global $fc_db_user;

// connect to database
$dbh = new PDO("sqlite:$fc_db_user");

$sql = "SELECT user_class, user_nick, user_psw, user_firstname, user_lastname, user_street,
				user_street_nbr, user_zipcode, user_city, user_public_profile, user_drm
	FROM fc_user
	WHERE user_id = '$_SESSION[user_id]' AND user_verified = 'verified' ";
	
$result = $dbh->query($sql);
$result= $result->fetch(PDO::FETCH_ASSOC);

return($result);

}




/**
 * Return user information
 * input = E-Mail Adress
 */


function get_userdata_by_mail($mail) {

global $fc_db_user;

// connect to database
$dbh = new PDO("sqlite:$fc_db_user");

$mail = $dbh -> quote($mail);

$sql = "SELECT user_class, user_nick, user_psw,	user_firstname,
			   user_lastname, user_street, user_street_nbr, user_zipcode,
			   user_city, user_drm, user_mail
		FROM fc_user
		WHERE user_mail = $mail AND user_verified = 'verified' ";
	
$result = $dbh->query($sql);
$result= $result->fetch(PDO::FETCH_ASSOC);

return($result);

}






/*
Return user information
input = password-reset-token
*/

function get_userdata_by_token($token) {

global $fc_db_user;

// connect to database
$dbh = new PDO("sqlite:$fc_db_user");

$token = $dbh -> quote($token);

$sql = "SELECT user_id, user_nick, user_mail
		FROM fc_user
		WHERE user_reset_psw = $token ";
	
$result = $dbh->query($sql);
$result= $result->fetch(PDO::FETCH_ASSOC);

return($result);

}








/**
 * Generate random Password
 */


function randpsw() {

	$length = 8;
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
 
function fc_user_login($user,$psw,$acp = NULL) {

	global $fc_db_user;
	unset($result);
	
	if($acp == TRUE) {
		$fc_db_user = '../'.$fc_db_user;
	}
	
	$login_hash  = md5($psw.$user);

	
	$dbh = new PDO("sqlite:$fc_db_user");
	$sql = "SELECT * FROM fc_user	WHERE user_nick = :login_name AND user_psw = :login_hash AND user_verified = 'verified'";
	$sth = $dbh->prepare($sql);
	$sth->bindValue(':login_name', "$user", PDO::PARAM_STR);
	$sth->bindValue(':login_hash', "$login_hash", PDO::PARAM_STR);
	$sth->execute();
	$result = $sth->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	
	$cnt_result = count($result);
	
	if($cnt_result>1) {
		$_SESSION['user_id'] = $result['user_id'];
		$_SESSION['user_nick'] = $result['user_nick'];
		$_SESSION['user_mail'] = $result['user_mail'];
		$_SESSION['user_class'] = $result['user_class'];
		$_SESSION['user_psw'] = $result['user_psw'];
		$_SESSION['user_firstname'] = $result['user_firstname'];
		$_SESSION['user_lastname'] = $result['user_lastname'];
		$_SESSION['user_hash'] = md5($result['user_nick']);
		
		$arr_drm = explode("|", $result['user_drm']);

		if($arr_drm[0] == "drm_acp_pages")	{  $_SESSION['acp_pages'] = "allowed";  }
		if($arr_drm[1] == "drm_acp_files")	{  $_SESSION['acp_files'] = "allowed";  }
		if($arr_drm[2] == "drm_acp_user")	{  $_SESSION['acp_user'] = "allowed";  }
		if($arr_drm[3] == "drm_acp_system")	{  $_SESSION['acp_system'] = "allowed";  }
		if($arr_drm[4] == "drm_acp_editpages")	{  $_SESSION['acp_editpages'] = "allowed";  }
		if($arr_drm[5] == "drm_acp_editownpages")	{  $_SESSION['acp_editownpages'] = "allowed";  }
		if($arr_drm[6] == "drm_moderator")	{  $_SESSION['drm_moderator'] = "allowed";  }
		if($arr_drm[7] == "drm_can_publish")	{  $_SESSION['drm_can_publish'] = "true";  }
		
		if(($acp == TRUE) AND ($_SESSION['user_class'] == "administrator")) {
			header("location:acp.php");
		}
		
	} else {
		session_destroy();
	}

}





?>