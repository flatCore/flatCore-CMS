<?php
session_start();

require("../config.php");

define("CONTENT_DB", "../$fc_db_content");
define("USER_DB", "../$fc_db_user");
define("STATS_DB", "../$fc_db_stats");
define("FC_ROOT", str_replace("/acp","",FC_INC_DIR));
define("IMAGES_FOLDER", "$img_path");
define("FILES_FOLDER", "$files_path");

require("core/access.php");

if(!isset($_SESSION[editor_class])) {
	$_SESSION[editor_class] = "wysiwyg";
}

/* switch editor - plain text or wysiwyg */
if($_GET[editor] == "toggle") {
	if($_SESSION[editor_class] == "plain") {
		$_SESSION[editor_class] = "wysiwyg";
	} elseif ($_SESSION[editor_class] == "wysiwyg") {
		$_SESSION[editor_class] = "plain";
	}
}

if($_SESSION[editor_class] == "wysiwyg") {
	$editor_class = "mceEditor";
	$editor_small_class = "mceEditor_small";
	$editor_btn = "Plain Text";
} else {
	$editor_class = "plain";
	$editor_small_class = "plain";
	$editor_btn = "WYSIWYG";
}



/* set language */
if($_SESSION[lang] == "") {
	$_SESSION['lang'] = "$languagePack";
}

if($_GET[set_lang]) {
	$set_lang = strip_tags($_GET[set_lang]);
	if(is_file("../lib/lang/$set_lang/acp/dict.php")) {
		$_SESSION['lang'] = "$set_lang";
	}
}

require("../lib/lang/$_SESSION[lang]/acp/dict.php");
require("core/functions.php");
require("core/database.php");

/**
 * including vars
 * tn -> mainscripts
 * sub -> subscripts
 */

if(!isset($_GET['tn'])){
	$tn = "dasboard";
} else {
	$tn = clean_vars("$_GET[tn]");
}

if(!isset($_GET['sub'])){
	$sub = "";
} else {
	$sub = clean_vars("$_GET[sub]");
}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		
		<link rel="icon" type="image/x-icon" href="images/favicon.ico" />
		
		<script language="javascript" type="text/javascript" src="../lib/js/tiny_mce/tiny_mce.js"></script>
		
		<script type="text/javascript" src="../lib/js/mootools/mootools-core.js"></script>
		<script type="text/javascript" src="../lib/js/mootools/mootools-more.js"></script>
		
		<script type="text/javascript" src="../lib/js/mootools/SimpleTabs.js"></script>
		<script type="text/javascript" src="../lib/js/mootools/milkbox.js"></script>
		
		<script type="text/javascript" src="../lib/js/mootools/Request.File.js"></script>
		<script type="text/javascript" src="../lib/js/mootools/Form.MultipleFileInput.js"></script>
		<script type="text/javascript" src="../lib/js/mootools/Form.Upload.js"></script>
				
		<link rel="stylesheet" href="../lib/css/bootstrap.css" type="text/css" media="screen, projection">
		<link rel="stylesheet" href="css/styles.css" type="text/css" media="screen, projection">
		<link rel="stylesheet" href="../lib/css/milkbox.css" media="screen" />
		
		
		

<title>flatCore:ACP @  <?php echo"$_SERVER[SERVER_NAME] /// $tn"; ?></title>
		

<?php
/*
 * include individual header
 * otional
 */

if(is_file("inc/head.$tn.php")){
	include("inc/head.$tn.php");
}
		
/*
 * individual modul header
 * optional
 */
 
if(is_file("../modules/$sub/backend/header.php")) {
	include("../modules/$sub/backend/header.php");
}


include("inc/editors.php");

?>
		
		

		
		
	</head>
	<body>
	
	<?php
	if(is_dir('../install')) {
		echo'<div style="padding:3px 15px;background-color:#b00;color:#000;border-bottom:1px solid #d00;">';
		echo"$lang[msg_update_modus_activated]";
		echo'</div>';
	}
	
	?>
	
	<div id="bigHeader">
	<?php require("core/topNav.php"); ?>
	</div>

	<div id="container">
		<?php include("core/$maininc.php"); ?>
	</div>
	
	<div id="footer">
	<b>flatCore</b> Content Management System<br />
	copyright © 2010 - <?php echo date(Y); ?>, <a href="http://www.flatcore.de/" target="_blank">flatCore.de</a><br />
	</div>
	

<script type="text/javascript"> 

/* <![CDATA[ */

window.addEvent('domready', function() {

var myTips = new Tips('.styledTip');

new Fx.Accordion($('accordion'), '#accordion h5', '#accordion .content');



var tabs = new SimpleTabs($('tabsBlock'), {
	selector: 'h4'
});




var toggle_status = {
    'true': '-',
    'false': '+'
  };
  
var myVerticalSlide = new Fx.Slide('vertical_slide').hide();

$('v_toggle').addEvent('click', function(event){
    event.stop();
    myVerticalSlide.toggle();
  });

myVerticalSlide.addEvent('complete', function() {
		$('vertical_status').set('html', toggle_status[myVerticalSlide.open]);
	});
	


}); // eo domready



/* ]]> */

</script>


	</body>
</html>


