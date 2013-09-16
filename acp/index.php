<?php
session_start();
error_reporting(0);
require("../config.php");

$_SESSION['lang'] = "de";
require("../lib/lang/$_SESSION[lang]/acp/dict.php");

if($_POST[check] == "Login") {

	$login_name = strip_tags($_POST[login_name]);
	$login_psw  = strip_tags($_POST[login_psw]);
	
	
	// connect to database
	$dbh = new PDO("sqlite:../$fc_db_user");
	$sql = "SELECT * FROM fc_user	WHERE user_nick = :login_name AND user_verified = 'verified'";
	$sth = $dbh->prepare($sql);
	$sth->bindValue(':login_name', "$login_name", PDO::PARAM_STR);
	$sth->execute();
	
	$result = $sth->fetch(PDO::FETCH_ASSOC);
	
	$dbh = null;
	
	$login_hash  = "$login_psw"."$login_name";
	
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
		session_destroy();
	}

}

?>

<!DOCTYPE html>
<html>
<head>
<title>flatCore ACP:Login <?php echo"@ $_SERVER[SERVER_NAME]"; ?></title>
<link rel="stylesheet" href="../lib/css/bootstrap.css" type="text/css" media="screen, projection">

<style type="text/css">

	#center {
		position: absolute;
		top:45%;
		left: 50%;
		width: 500px;
		height: 250px;
		margin-top: -125px;
		margin-left: -250px;
	}
	
	form {
		padding: 25px;
		background-color: #f5f5f5;
		border-radius: 9px;
	}

</style>
</head>
<body>
	<div id="center">
		<form action="index.php" method="post" class="form-horizontal">
			<fieldset>
				<legend>Login:</legend>	
				<div class="control-group">
					<label class="control-label"><?php echo"$lang[f_user_nick]"; ?></label>
					<div class="controls">
						<input type="text" class="input-block-level" name="login_name">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo"$lang[f_user_psw]"; ?></label>
					<div class="controls">
						<input type="password" class="input-block-level" name="login_psw">
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<input type="submit" class="btn btn-success" name="check" value="Login">
					</div>
				</div>
				</fieldset>
		</form>
	</div>
</body>
</html>