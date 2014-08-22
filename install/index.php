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

$modus = '';

if(isset($_GET['l']) && is_dir('../lib/lang/'.basename($_GET['l']).'/')) {
	$_SESSION['lang'] = basename($_GET['l']);
}

if(!isset($_SESSION['lang']) || $_SESSION['lang'] == '') {
	$l = 'de';
	$modus = 'choose_lang';
} else {
	$l = $_SESSION['lang'];
}

require("../config.php");
require("php/functions.php");
include('../lib/lang/'.$l.'/dict-install.php');



if(is_file("../$fc_db_content") && $modus != 'choose_lang'){
	$modus = "update";
} elseif($modus != 'choose_lang') {
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
			<div style="float:right;margin-top:-28px;"><span class="label label-info"><?php echo"$modus" ?></span></div>
			<h1>flatCore <small>Installation & Setup</small></h1>
		</div>
		<div id="inst-body">
			<?php
			if($modus == "install") {
				include("inc.install.php");
			} elseif($modus == "update") {
				include("inc.update.php");
			} else {
				echo '<h3 class="text-center">Choose your Language ...</h3><hr>';
				echo '<div class="row">';
				echo '<div class="col-md-6">';
				echo '<p class="text-center"><a href="'.$_SERVER['PHP_SELF'].'?l=de"><img src="../lib/lang/de/flag.png" class="img-rounded"><br>DE</a></p>';
				echo '</div>';
				echo '<div class="col-md-6">';
				echo '<p class="text-center"><a href="'.$_SERVER['PHP_SELF'].'?l=en"><img src="../lib/lang/en/flag.png" class="img-rounded"><br>EN</a></p>';
				echo '</div>';
				echo '</div>';
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

