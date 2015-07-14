<?php
session_start();
error_reporting(0);
require('core/access.php');
require('../config.php');
include('versions.php');

if(is_file('../'.FC_CONTENT_DIR.'/config.php')) {
	include('../'.FC_CONTENT_DIR.'/config.php');
}

if(isset($fc_content_files) && is_array($fc_content_files)) {
	/* switch database file $fc_db_content */
	include('core/contentSwitch.php');
}

define("CONTENT_DB", "../$fc_db_content");
define("USER_DB", "../$fc_db_user");
define("STATS_DB", "../$fc_db_stats");
define("FC_ROOT", str_replace("/acp","",FC_INC_DIR));
define("IMAGES_FOLDER", "$img_path");
define("FILES_FOLDER", "$files_path");
define("FC_SOURCE", "backend");

if(!isset($_SESSION['editor_class'])) {
	$_SESSION['editor_class'] = "wysiwyg";
}

/* switch editor - plain text or wysiwyg */
if(isset($_GET['editor'])) {
	
	if($_GET['editor'] == 'wysiwyg') {
		$_SESSION['editor_class'] = "wysiwyg";
	} elseif($_GET['editor'] == 'plain') {
		$_SESSION['editor_class'] = "plain";
	} else {
		$_SESSION['editor_class'] = "code";
	}

}

if($_SESSION['editor_class'] == "wysiwyg") {
	$editor_class = "mceEditor";
	$editor_small_class = "mceEditor_small";
} elseif($_SESSION['editor_class'] == "plain") {
	$editor_class = "plain";
	$editor_small_class = "plain";
} else {
	$editor_class = "aceEditor_html";
	$editor_small_class = "aceEditor_html";
}



/* set language */

if(!isset($_SESSION['lang'])) {
	$_SESSION['lang'] = "$languagePack";
}

if(isset($_GET['set_lang'])) {
	$set_lang = strip_tags($_GET['set_lang']);
	if(is_dir("../lib/lang/$set_lang/")) {
		$_SESSION['lang'] = "$set_lang";
	}
}

if(isset($_SESSION['lang'])) {
	$languagePack = basename($_SESSION['lang']);
}

require("../lib/lang/index.php");
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
		
		<script src="../lib/js/jquery/jquery.min.js"></script>
    <script src="../lib/js/bootstrap.min.js"></script>
    
    <script language="javascript" type="text/javascript" src="../lib/js/tinymce/tinymce.min.js"></script>
    <script language="javascript" type="text/javascript" src="../lib/js/tinymce/jquery.tinymce.min.js"></script>

		<!-- Add fancyBox -->
		<link rel="stylesheet" href="../lib/js/jquery/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="../lib/js/jquery/fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>
		
		<script type="text/javascript" src="../lib/js/jquery/jquery.textareaCounter.plugin.js"></script>
				
		<link rel="stylesheet" href="../lib/css/bootstrap.min.css?v=3.3.2" type="text/css" media="screen, projection">
		<link rel="stylesheet" href="css/styles.css?v=20150225" type="text/css" media="screen, projection">
		
		<!-- masonry -->
		<script type="text/javascript" src="../lib/js/masonry.pkgd.min.js"></script>
		<script type="text/javascript" src="../lib/js/imagesloaded.pkgd.min.js"></script>
		
		<!-- Chart.js -->
		<script type="text/javascript" src="../lib/js/Chart.min.js"></script>
		
		<!-- jquery.matchHeight-min.js-->
		<script type="text/javascript" src="../lib/js/jquery/jquery.matchHeight-min.js"></script>
		
		<!-- bootstrap switch -->
		<link rel="stylesheet" href="../lib/css/bootstrap-switch.css" type="text/css" media="screen, projection">
		<script type="text/javascript" src="../lib/js/bootstrap-switch.min.js"></script>
		
		<!-- uploader -->
		<script src="../lib/js/dropzone.js"></script>
		<link rel="stylesheet" href="../lib/css/dropzone.css" type="text/css" media="screen, projection">
		
		<!-- ACE Editor -->
		<script src="../lib/js/ace/ace.js" type="text/javascript" charset="utf-8"></script>
		
		<!-- dirty forms -->
		<script src="../lib/js/jquery/jquery.dirtyforms.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" src="../lib/js/jquery/jquery.dirtyforms.tinymce.js"></script>
		
		<!-- Tags -->
		<script type="text/javascript" src="../lib/js/bootstrap-tagsinput.min.js"></script>
		
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
		if(is_file('../maintance.html')) {
			echo '<div style="padding:3px 15px;background-color:#b00;color:#000;border-bottom:1px solid #d00;">';
			echo $lang['msg_update_modus_activated'];
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
		if(isset($fc_content_files) && is_array($fc_content_files)) {
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
		<b>flatCore</b> Content Management System <small>(<?php echo $fc_version_name; ?>)</small><br />
		copyright © 2010 - <?php echo date('Y'); ?>, <a href="http://www.flatcore.de/" target="_blank">flatCore.de</a>
		</div>
		
		</div>
		
		<div style="position:fixed;bottom:0;right:0;padding:10px;">
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal"><span class="glyphicon glyphicon-upload"></span> Upload</button>
		</div>
		<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-hidden="true">
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-upload"></span> Upload</h4>
		      </div>
		      <div class="modal-body">
		        <?php include("core/files.upload.php"); ?>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		      </div>
		    </div>
		  </div>
		</div>





		<script type="text/javascript">

			$(document).ready(function() {
				
		    $('input[name="optEditor"]').on("change", function () {
			    var button = $("input[name='optEditor']:checked").val();
	    		localStorage.setItem("editor_mode", button);
	    		switchEditorMode(button);
	    		
	    		if(button == 'optE1') {
		    		tinymce.execCommand('mceAddEditor', false, "textEditor");
	    		}
	    		
				});
							
			
				/* toggle editor class [mceEditor|plain|aceEditor_html] */
				var editor_mode = localStorage.getItem('editor_mode');	
				if(!editor_mode) {
					editor_mode = 'optE1';
					localStorage.setItem("editor_mode", editor_mode);
				}
				setAceEditor();
				switchEditorMode(editor_mode);
				
				/* dirty forms */
				$('form').dirtyForms();
				$.DirtyForms.dialog = false;

				$("input[value="+editor_mode+"]").parent().addClass('active');
			
				function switchEditorMode(mode) {
					
					var textEditor = $('textarea[id="textEditor"]');
					textEditor.removeClass();
					textEditor.removeAttr('style');
					var divEditor = $('#aceCodeEditor');
					
		  		if(mode == 'optE1') {
		    		textEditor.addClass('mceEditor form-control');
		    		textEditor.css("display","block");
		    		divEditor.remove();
		  		}
		  		if(mode == 'optE2') {
			  		divEditor.remove();
			  		if(tinymce.editors.length > 0) {
			  			tinymce.execCommand('mceFocus', false, 'textEditor');
							tinymce.execCommand('mceRemoveControl', true, "textEditor");
							tinymce.remove();
							$('div.mce-tinymce').remove();
						}
		    		textEditor.addClass('plain form-control');
		    		textEditor.css("display","block");
		    		textEditor.css("visibility","visible");
		  		}
		  		if(mode == 'optE3') {
			  		if(tinymce.editors.length > 0) {
			  			tinymce.execCommand('mceFocus', false, 'textEditor');
							tinymce.execCommand('mceRemoveControl', true, "textEditor");
							tinymce.remove();
						}
						textEditor.addClass('aceEditor_code form-control');
						textEditor.after('<div id="aceCodeEditor"></div>');
		    		setAceEditor();
		    		divEditor.css("width","100%");
		    		divEditor.css("height","350px");
		  		}	
					
				}
				
				function setAceEditor() {
					
					if($('#aceCodeEditor').length != 0) {
						var aceEditor = ace.edit("aceCodeEditor");
						var HTMLtextarea = $('textarea[class*=aceEditor_code]').hide();
					  
					  aceEditor.getSession().setValue(HTMLtextarea.val());
					  aceEditor.setTheme("ace/theme/chrome");
					  aceEditor.getSession().setMode("ace/mode/html");
					  aceEditor.setShowPrintMargin(false);
						aceEditor.getSession().on('change', function(){
							HTMLtextarea.val(HTMLeditor.getSession().getValue());
						});
				  }
				}
			

				$('#bsTabs').tab();
					
		  	setTimeout(function() {
		        $(".alert-auto-close").slideUp('slow');
				}, 2000);
			
				$('#showVersions').collapse('hide');
				
				$('.tooltip_bottom').tooltip({
					placement: 'bottom',
					delay: { show: 1000, hide: 100 }
				})
			
				$('.tooltip').tooltip();
				
				$(".fancybox").fancybox();
				
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
				
				$(".fancybox-ajax").fancybox({
					type: 'ajax',
					minWidth: '50%',
					height: '90%'
				});
			
	      $(document).on('mouseenter', '.hiddenControls', function () {
					$(this).find('.controls').fadeIn();
	      }).on('mouseleave', '.hiddenControls', function () {
					$(this).find('.controls').hide();
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
			
				$.fn.matchHeight._update('.equal');


				/* css and html editor for page header */
				if($('#CSSeditor').length != 0) {
					var CSSeditor = ace.edit("CSSeditor");
					var CSStextarea = $('textarea[class*=aceEditor_css]').hide();
				  
				  CSSeditor.getSession().setValue(CSStextarea.val());
				  CSSeditor.setTheme("ace/theme/chrome");
				  CSSeditor.getSession().setMode("ace/mode/css");
				  CSSeditor.setShowPrintMargin(false);
					CSSeditor.getSession().on('change', function(){
						CSStextarea.val(CSSeditor.getSession().getValue());
					});
				}
				
				if($('#HTMLeditor').length != 0) {
					var HTMLeditor = ace.edit("HTMLeditor");
					var HTMLtextarea = $('textarea[class*=aceEditor_html]').hide();
				  
				  HTMLeditor.getSession().setValue(HTMLtextarea.val());
				  HTMLeditor.setTheme("ace/theme/chrome");
				  HTMLeditor.getSession().setMode("ace/mode/html");
				  HTMLeditor.setShowPrintMargin(false);
					HTMLeditor.getSession().on('change', function(){
						HTMLtextarea.val(HTMLeditor.getSession().getValue());
					});
			  }
			
			});
			
			
			
		</script>

	</body>
</html>