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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="en-us" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<link media="screen" rel="stylesheet" type="text/css" href="css/styles.css" />
		<title><?php echo"$modus"; ?> flatCore | Content Management System</title>
		
		
		
</head>
<body>
	
<div id="frame">
	
	<div id="header">
		<h1>Installation & Setup</h1>
		<p>Modus: <?php echo"$modus"; ?></p>
	</div>

	<div id="body">
	
		<?php
		
		if($modus == "install") {
			include("inc.install.php");
		} else {
			include("inc.update.php");
		}
		
		?>
	
	</div>


	<div id="footer">
		<p>flatCore | Content Management System<br>
		<a href="http://www.flatcore.de">powered by flatcore.de</a></p>
	</div>

</div>

</body>
</html>