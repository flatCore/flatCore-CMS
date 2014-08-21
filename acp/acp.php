<?php
session_start();
error_reporting(0);
require("core/access.php");
require("../config.php");

if(is_file('../'.FC_CONTENT_DIR.'/config.php')) {
	include('../'.FC_CONTENT_DIR.'/config.php');
}

if(is_array($fc_content_files)) {
	/* switch database file $fc_db_content */
	include('core/contentSwitch.php');
}

define("CONTENT_DB", "../$fc_db_content");
define("USER_DB", "../$fc_db_user");
define("STATS_DB", "../$fc_db_stats");
define("FC_ROOT", str_replace("/acp","",FC_INC_DIR));
define("IMAGES_FOLDER", "$img_path");
define("FILES_FOLDER", "$files_path");

if(!isset($_SESSION['editor_class'])) {
	$_SESSION['editor_class'] = "wysiwyg";
}

/* switch editor - plain text or wysiwyg */
if(isset($_GET['editor'])) {
	if($_SESSION['editor_class'] == "plain") {
		$_SESSION['editor_class'] = "wysiwyg";
	} elseif ($_SESSION['editor_class'] == "wysiwyg") {
		$_SESSION['editor_class'] = "plain";
	}
}

if($_SESSION['editor_class'] == "wysiwyg") {
	$editor_class = "mceEditor";
	$editor_small_class = "mceEditor_small";
	$editor_btn = "Plain Text";
} else {
	$editor_class = "plain";
	$editor_small_class = "plain";
	$editor_btn = "WYSIWYG";
}



/* set language */
if($_SESSION['lang'] == "") {
	$_SESSION['lang'] = "$languagePack";
}

if(isset($_GET['set_lang'])) {
	$set_lang = strip_tags($_GET['set_lang']);
	if(is_file("../lib/lang/$set_lang/dict-backend.php")) {
		$_SESSION['lang'] = "$set_lang";
	}
}

require("../lib/lang/$_SESSION[lang]/dict-backend.php");
require("core/functions.php");
require("core/database.php");
require("core/switch.php");


/* READ THE PREFS */
$fc_preferences = get_preferences();

foreach($fc_preferences as $k => $v) {
   $$k = stripslashes($v);
}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>ACP | <?php echo $_SERVER['SERVER_NAME'] . ' | ' . $tn; ?></title>
		
		<link rel="icon" type="image/x-icon" href="images/favicon.ico" />
		
		<script language="javascript" type="text/javascript" src="../lib/js/tinymce/tinymce.min.js"></script>
		
		<script src="../lib/js/jquery/jquery.min.js"></script>
    <script src="../lib/js/bootstrap.min.js"></script>

		<!-- Add fancyBox -->
		<link rel="stylesheet" href="../lib/js/jquery/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="../lib/js/jquery/fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>
		
		<script type="text/javascript" src="../lib/js/jquery/jquery.textareaCounter.plugin.js"></script>
				
		<link rel="stylesheet" href="../lib/css/bootstrap.min.css" type="text/css" media="screen, projection">
		<link rel="stylesheet" href="css/styles.css" type="text/css" media="screen, projection">
		
		<!-- masonry -->
		<script type="text/javascript" src="../lib/js/masonry.pkgd.min.js"></script>
		<script type="text/javascript" src="../lib/js/imagesloaded.pkgd.min.js"></script>
		
		<!-- bootstrap switch -->
		<link rel="stylesheet" href="../lib/css/bootstrap-switch.css" type="text/css" media="screen, projection">
		<script type="text/javascript" src="../lib/js/bootstrap-switch.min.js"></script>
		
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

	<div id="page-sidebar">
		<a href="acp.php?tn=dashboard" id="dashboard" title="Dashboard"></a>
		<?php include("core/$navinc.php"); ?>
		<?php include('core/livebox.php'); ?>
	</div>
		
	<div id="page-content">

	<?php
	if(is_array($fc_content_files)) {
		echo '<div id="contentSwitch" class="clearfix">';
		echo $fc_content_switch;
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
			placement: 'bottom',
			delay: { show: 1000, hide: 100 }
		})
		
		$('.tooltip').tooltip()
		
		$(".fancybox-iframe").fancybox({
			type: 'iframe',
			autoWidth: true,
			autoHeight: true
		});
		
		$(".fancybox-docs").fancybox({
			type: 'iframe',
			width: '77%',
			height: '90%'
		});
		
   $('.collapse').on('show.bs.collapse', function() {
        var id = $(this).attr('id');
        $('a[href="#' + id + '"]').addClass('active');
    });
    $('.collapse').on('hide.bs.collapse', function() {
        var id = $(this).attr('id');
        $('a[href="#' + id + '"]').removeClass('active');
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
	      'originalStyle': 'text-left',
	      'displayFormat': '<span class="label label-default">#input</span> <span class="label label-default">#words</span>'  
	  };  
	  $('.cntValues').textareaCount(options);  
	  

		var $container = $('#masonry-container');
		
		$('#masonry-container').imagesLoaded( function(){
		  $('#masonry-container').masonry({
		   itemSelector: '.masonry-item',
		   isAnimated: true,
		   isFitWidth: true,
		 
		   gutter: 10
		  });
		});
	
		
	});
	</script>


	</body>
</html>


