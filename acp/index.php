<?php
session_start();
error_reporting(0);

require '../lib/Medoo.php';
use Medoo\Medoo;

require '../config.php';
if(is_file('../'.FC_CONTENT_DIR.'/config.php')) {
	include '../'.FC_CONTENT_DIR.'/config.php';
}


if(is_file('../config_database.php')) {
	include '../config_database.php';
	$db_type = 'mysql';
	
	$database = new Medoo([

		'database_type' => 'mysql',
		'database_name' => "$database_name",
		'server' => "$database_host",
		'username' => "$database_user",
		'password' => "$database_psw",
	 
		'charset' => 'utf8',
		'port' => $database_port,
	 
		'prefix' => DB_PREFIX
	]);
	
	$db_content = $database;
	$db_user = $database;
	$db_statistics = $database;	
	
	
	
} else {
	$db_type = 'sqlite';
	
	if(isset($fc_content_files) && is_array($fc_content_files)) {
		/* switch database file $fc_db_content */
		include 'core/contentSwitch.php';
	}
	
	
	define("CONTENT_DB", "../$fc_db_content");
	define("USER_DB", "../$fc_db_user");
	define("STATS_DB", "../$fc_db_stats");	

	$db_content = new Medoo([
		'database_type' => 'sqlite',
		'database_file' => CONTENT_DB
	]);
	
	$db_user = new Medoo([
		'database_type' => 'sqlite',
		'database_file' => USER_DB
	]);
	
	$db_statistics = new Medoo([
		'database_type' => 'sqlite',
		'database_file' => STATS_DB
	]);	
	
}






require '../core/functions/func_userdata.php';
require '../lib/lang/'.$languagePack.'/dict-backend.php';

if(isset($_POST['check']) && ($_POST['check'] == "Login")) {

	$remember = false;
	if(isset($_POST['remember_me'])) {
		$remember = true;
	}
		
	fc_user_login($_POST['login_name'],$_POST['login_psw'],$acp=TRUE,$remember);
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Login <?php echo $_SERVER['SERVER_NAME']; ?></title>
	<meta name="robots" content="noindex">
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" media="screen, projection">

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
				<div class="form-group row">
					<label class="col-sm-3 col-form-label"><?php echo $lang['f_user_nick']; ?></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="login_name" autofocus="autofocus">
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-3 col-form-label"><?php echo $lang['f_user_psw']; ?></label>
					<div class="col-sm-9">
						<input type="password" class="form-control" name="login_psw">
					</div>
				</div>
			  <div class="form-group row">
			    <div class="offset-sm-3 col-sm-9">
			      <div class="form-check form-check-inline">
			        <label>
			          <input type="checkbox" name="remember_me"> <?php echo $lang['remember_me']; ?>
			        </label>
			      </div>
			    </div>
			  </div>
				<div class="form-group row">
					<div class="offset-sm-3 col-sm-9">
						<input type="submit" class="btn btn-success" name="check" value="Login">
					</div>
				</div>
				</fieldset>
		</form>
	</div>
</body>
</html>