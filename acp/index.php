<?php
session_start();

require("../config.php");

$_SESSION['lang'] = "de";
require("../lib/lang/$_SESSION[lang]/acp/dict.php");


if($_POST[check] == "Login") {

$login_name = strip_tags($_POST[login_name]);
$login_psw  = strip_tags($_POST[login_psw]);


// connect to database
$dbh = new PDO("sqlite:../$fc_db_user");


$sql = "SELECT 
	user_id,
	user_class,
	user_nick,
	user_psw,
	user_drm,
	user_firstname,
	user_lastname
	FROM fc_user
	WHERE user_nick = :login_name AND user_verified = 'verified'
	";
	
$sth = $dbh->prepare($sql);
$sth->bindValue(':login_name', "$login_name", PDO::PARAM_STR);
$sth->execute();


$result = $sth->fetch(PDO::FETCH_ASSOC);

$dbh = null;

$login_hash  = "$login_psw"."$login_name";


// check userdata
if(md5($login_hash) == "$result[user_psw]") {

	// correct login - write data in session
	$_SESSION[user_id] = "$result[user_id]";
	$_SESSION[user_nick] = "$result[user_nick]";
	$_SESSION[user_mail] = "$result[user_mail]";
	$_SESSION[user_class] = "$result[user_class]";
	$_SESSION[user_psw] = "$result[user_psw]";
	$_SESSION[user_firstname] = "$result[user_firstname]";
	$_SESSION[user_lastname] = "$result[user_lastname]";

	$arr_drm = explode("|", $result[user_drm]);

		if($arr_drm[0] == "drm_acp_pages")	{  $_SESSION[acp_pages] = "allowed";  }
		if($arr_drm[1] == "drm_acp_files")	{  $_SESSION[acp_files] = "allowed";  }
		if($arr_drm[2] == "drm_acp_user")	{  $_SESSION[acp_user] = "allowed";  }
		if($arr_drm[3] == "drm_acp_system")	{  $_SESSION[acp_system] = "allowed";  }
		if($arr_drm[4] == "drm_acp_editpages")	{  $_SESSION[acp_editpages] = "allowed";  }
		if($arr_drm[5] == "drm_acp_editownpages")	{  $_SESSION[acp_editownpages] = "allowed";  }
		if($arr_drm[6] == "drm_moderator")	{  $_SESSION[drm_moderator] = "allowed";  }
		if($arr_drm[7] == "drm_can_publish")	{  $_SESSION[drm_can_publish] = "true";  }

	if($_SESSION[user_class] == "administrator"){
	//goto acp
	header("location:acp.php");
	}


} else {
// NO correct login
session_destroy();
}


}

?>

<!DOCTYPE html>
<html>
<head>
<title>flatCore ACP:Login <?php echo"@ $_SERVER[SERVER_NAME]"; ?></title>

<style type="text/css">
* {padding:0;margin:0;}

body#login {
	background: #666 url(images/login_bg.jpg) no-repeat fixed center center;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #666;
}

#container {
	position: absolute;
	top:50%;
	left: 50%;
	width: 400px;
	height: 250px;
	margin-top: -125px;
	margin-left: -200px;
}


#container form {
	margin: 0;
	padding: 70px 4px 0 30px;
}





.inputtext {
	border: 1px solid #999;
	background: transparent url(images/white25.png) top repeat-x;
	width: 320px;
	margin: 4px 4px 4px 4px;
	padding: 3px 6px;
	color: #36c;
	font-size: 14px;
}

.inputsubmit {
	border: 1px solid #fff;
	background: #CCFF66 url(images/shiny_buttons.png) top left repeat-x;
	margin: 8px 4px 4px 4px;
	padding: 6px;
	font-weight: bold;
	font-size: 12px;
	color: #408000;
	letter-spacing: 1px;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
}

.inputsubmit:hover {
	background: #408000 url(images/shiny_buttons.png) top left repeat-x;
	color: #fff;
	border: 1px solid #408000;
}


</style>
</head>





<body id="login">

	<div id="container">
	
		<form action="index.php" method="post">
		
			<?php echo"$lang[f_user_nick]"; ?> <br>
			<input type="text" class="inputtext" name="login_name"><br>
			<?php echo"$lang[f_user_psw]"; ?> <br>
			<input type="password" class="inputtext" name="login_psw"><br>
			<input type="submit" class="inputsubmit" name="check" value="Login">
		
		</form>
	
	</div>

</body>


</html>