<?php
session_start();
error_reporting(0);
require('../config.php');
require('../core/functions/func_userdata.php');

$_SESSION['lang'] = "de";
require("../lib/lang/$_SESSION[lang]/acp/dict.php");

if($_POST['check'] == "Login") {

	$login_name = strip_tags($_POST['login_name']);
	$login_psw  = strip_tags($_POST['login_psw']);
	
	fc_user_login($login_name,$login_psw,$acp=TRUE);
	
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Login <?php echo $_SERVER['SERVER_NAME']; ?></title>
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