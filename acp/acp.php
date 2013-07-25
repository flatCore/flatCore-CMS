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
		<title>flatCore:ACP @  <?php echo"$_SERVER[SERVER_NAME] /// $tn"; ?></title>
		
		<link rel="icon" type="image/x-icon" href="images/favicon.ico" />
		
		<script language="javascript" type="text/javascript" src="../lib/js/tinymce/tinymce.min.js"></script>
		
		<script src="../lib/js/jquery/jquery.min.js"></script>
    <script src="../lib/js/jquery/bootstrap.min.js"></script>

		<!-- Add fancyBox -->
		<link rel="stylesheet" href="../lib/js/jquery/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="../lib/js/jquery/fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>
		
		<script type="text/javascript" src="../lib/js/jquery/jquery.textareaCounter.plugin.js"></script>
				
		<link rel="stylesheet" href="../lib/css/bootstrap.css" type="text/css" media="screen, projection">
		<link rel="stylesheet" href="../lib/css/bootstrap-responsive.css" type="text/css" media="screen, projection">
		<link rel="stylesheet" href="css/styles.css" type="text/css" media="screen, projection">
		
		<!-- uploader -->
		<script src="../lib/js/dropzone.js"></script>
		<link rel="stylesheet" href="../lib/css/dropzone.css" type="text/css" media="screen, projection">
		
		
		<?php

		/*
		 * individual modul header
		 * optional
		 */
		 
		if(is_file("../modules/$sub/backend/header.php")) {
			include("../modules/$sub/backend/header.php");
		}
		
		include("core/editors.php");
		include("core/templates.php");
		
		?>	
		
	</head>
	<body>
	
	<?php
	if(is_dir('../install')) {
		echo '<div style="padding:3px 15px;background-color:#b00;color:#000;border-bottom:1px solid #d00;">';
		echo "$lang[msg_update_modus_activated]";
		echo '</div>';
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
	copyright © 2010 - <?php echo date(Y); ?>, <a href="http://www.flatcore.de/" target="_blank">flatCore.de</a>
	</div>
	
	
	<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#bsTabs').tab();
    });
	</script>
	<script type="text/javascript">
	$(document).ready(function() {
		$(".fancybox").fancybox();
		
		$('#showVersions').collapse('hide');
		
		$('.tooltip_bottom').tooltip({
			placement: 'bottom'
		})
		
		$(".fancybox-iframe").fancybox({
			type: 'iframe',
			autoWidth: true,
			autoHeight: true
		});
		
	Dropzone.options.myDropzone = {
  	init: function() {
    	this.on("success", function(file, responseText) {
      	// Handle the responseText here. For example, add the text to the preview element:
				file.previewTemplate.appendChild(document.createTextNode(responseText));
			});
		}
	};

    var options = {   
        'originalStyle': 'text-right',
        'displayFormat': '<span class="label">#input</span> <span class="label">#words</span>'  
    };  
    $('.cntValues').textareaCount(options);  
  
		
	});
	</script>


	</body>
</html>


