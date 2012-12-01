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








/*
Generate random Password
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









?>