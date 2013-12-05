<?php

/**
 * flatCore Content Management System
 * Installer/Updater
 *
 * @package: install/
 * @author: Patrick Konstandin <support@flatfiler.de>
 *
 */

session_start();
error_reporting(0);

require("../config.php");
require("php/functions.php");
include("lang/$languagePack.php");


if(is_file("../$fc_db_content")) {
	$modus = "update";
} else {
	$modus = "install";
}

if($modus == "update") {
	/* updates for admins only */
	if($_SESSION['user_class'] != "administrator"){
		//move to login or die
		header("location:login.php");
		die("PERMISSION DENIED!");
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo"$modus"; ?> flatCore | Content Management System</title>
	<link media="screen" rel="stylesheet" type="text/css" href="../lib/css/bootstrap.min.css" />
	<link media="screen" rel="stylesheet" type="text/css" href="css/styles.css" />
</head>
<body>
<div id="inst-frame">
	<div id="inst-background">
		<div id="inst-header">
			<h1>flatCore <small>Installation & Setup</small></h1>
			<p>Modus: <span class="label label-inverse"><?php echo"$modus"; ?></span></p>
		</div>
		<div id="inst-body">
			<?php
			if($modus == "install") {
				include("inc.install.php");
			} else {
				include("inc.update.php");
			}
			
			?>
		</div>
		<div id="inst-footer">
			<a href="http://www.flatcore.de">
			<img src="images/logo.png" alt="flatCore Logo">
			<h3>flatCore<br><small>Content Management System</small></h3>
			</a>
		</div>
	</div>
</div>
</body>
</html>

