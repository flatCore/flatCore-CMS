<?php
session_start();
error_reporting(0);

require '../config.php';
if(is_file('../'.FC_CONTENT_DIR.'/config.php')) {
	include '../'.FC_CONTENT_DIR.'/config.php';
}
require '../lib/Medoo.php';
use Medoo\Medoo;


if(isset($fc_content_files) && is_array($fc_content_files)) {
	/* switch database file $fc_db_content */
	include 'core/contentSwitch.php';
}

define("CONTENT_DB", "../$fc_db_content");
define("USER_DB", "../$fc_db_user");
define("STATS_DB", "../$fc_db_stats");
define("INDEX_DB", "../$fc_db_index");
define("FC_ROOT", str_replace("/acp","",FC_INC_DIR));
define("IMAGES_FOLDER", "$img_path");
define("FILES_FOLDER", "$files_path");
define("FC_SOURCE", "backend");

$db_content = new Medoo([
	'database_type' => 'sqlite',
	'database_file' => CONTENT_DB
]);

$db_user = new Medoo([
	'database_type' => 'sqlite',
	'database_file' => USER_DB
]);


require 'core/access.php';
include 'versions.php';
include 'core/icons.php';
include '../lib/parsedown/Parsedown.php';


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

require '../lib/lang/index.php';
require 'core/functions.php';
require 'core/switch.php';

$all_mods = get_all_moduls();
$all_plugins = get_all_plugins();

/* READ THE PREFS */
$fc_preferences = get_preferences();

foreach($fc_preferences as $k => $v) {
   $$k = stripslashes($v);
}

/* build absolute URL */
if($fc_preferences['prefs_cms_ssl_domain'] != '') {
	$fc_base_url = $prefs_cms_ssl_domain . $prefs_cms_base;
} else {
	$fc_base_url = $prefs_cms_domain . $prefs_cms_base;
}

if(!isset($_COOKIE['acptheme'])) {
	setcookie("acptheme", "dark",time()+(3600*24*365));
}

if(isset($_GET['theme'])) {
	if($_COOKIE["acptheme"] == 'light') {
		setcookie("acptheme", 'dark',time()+(3600*24*365));
		$set_acptheme = 'dark';
	} else {
		setcookie("acptheme", 'light',time()+(3600*24*365));
		$set_acptheme = 'light';
	}
}


if(isset($set_acptheme)) {
	$acptheme = $set_acptheme;
} else {
	$acptheme = $_COOKIE["acptheme"];
}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>ACP | <?php echo $fc_base_url . ' | ' . $tn; ?></title>
		
		<link rel="icon" type="image/x-icon" href="images/favicon.ico" />
		
		<script src="../lib/js/jquery/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script language="javascript" type="text/javascript" src="../lib/js/tinymce/tinymce.min.js"></script>
    <script language="javascript" type="text/javascript" src="../lib/js/tinymce/jquery.tinymce.min.js"></script>

		<!-- Add fancyBox -->
		<link rel="stylesheet" href="./css/jquery.fancybox.min.css?v=2.1.5" type="text/css" media="screen" />
		<script type="text/javascript" src="./js/jquery.fancybox.min.js?v=2.1.5"></script>
		
		<script type="text/javascript" src="../lib/js/jquery/jquery.textareaCounter.plugin.js"></script>

		<link rel="stylesheet" href="css/bootstrap.min.css?v=4.3.1" type="text/css" media="screen, projection">
		
		<?php
		if($acptheme == 'dark') {
			echo '<link rel="stylesheet" href="css/styles_dark.css?v=20200124" type="text/css" media="screen, projection">';
		} else {
			echo '<link rel="stylesheet" href="css/styles_light.css?v=20200124" type="text/css" media="screen, projection">';
		}
		?>
				
		<link href="fontawesome/css/all.min.css" rel="stylesheet">
		
		<script type="text/javascript">
			var languagePack = "<?php echo $languagePack; ?>";
			var ace_theme = 'chrome';
			var tinymce_skin = 'oxide';
			var acptheme = "<?php echo $acptheme; ?>";
			if(acptheme === 'dark') {
				var ace_theme = 'twilight';
				var tinymce_skin = 'oxide-dark';
			}
		</script>
		
		
		<!-- jquery.matchHeight-min.js-->
		<script type="text/javascript" src="../lib/js/jquery/jquery.matchHeight-min.js"></script>
				
		<!-- uploader -->
		<script src="../lib/js/dropzone.js"></script>
		<link rel="stylesheet" href="../lib/css/dropzone.css" type="text/css" media="screen, projection">
		
		<!-- ACE Editor -->
		<script src="../lib/js/ace/ace.js" data-ace-base="../lib/js/ace" type="text/javascript" charset="utf-8"></script>
		
		<!-- dirty forms -->
		<script src="../lib/js/jquery/jquery.dirtyforms.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" src="../lib/js/jquery/jquery.dirtyforms.tinymce.js"></script>
		
		<!-- Tags -->
		<script type="text/javascript" src="../lib/js/bootstrap-tagsinput.min.js"></script>
		
		<!-- image picker -->
		<script type="text/javascript" src="../lib/js/jquery/image-picker.min.js"></script>
	
		
		<?php

		/*
		 * individual modul header
		 * optional
		 */
		 
		if(is_file("../modules/$sub/backend/header.php")) {
			include '../modules/'.$sub.'/backend/header.php';
		}
		
		
		include 'core/templates.php';
		
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
			<a id="sidebar-dashboard" href="acp.php?tn=dashboard"></a>
			<div id="page-sidebar-inner">
			<?php include 'core/'.$navinc.'.php'; ?>
			<?php include 'core/livebox.php'; ?>
			</div>
		</div>
			
		<div id="page-content">


    <div id="expireDiv" class="expire-hidden">
        Your session is about to expire. You will be logged out in <span id="currentSeconds"></span> seconds.
        If you want to continue, please save your work and refresh the page.
    </div>
	
		<?php
		if(isset($fc_content_files) && is_array($fc_content_files)) {
			echo '<div id="contentSwitch" class="clearfix">';
			echo $fc_content_switch;
			echo '</div>';
		}
		?>

		<div id="bigHeader">
		<?php require 'core/topNav.php'; ?>
		</div>
	
	
		<div id="container">
			<?php include 'core/'.$maininc.'.php'; ?>
		</div>

		<?php include 'core/editors.php'; ?>
	
		<div id="footer">
			<p class="text-center">
			<?php
				$arr_lang = get_all_languages();
				for($i=0;$i<count($arr_lang);$i++) {
	$lang_icon = '<img src="../lib/lang/'.$arr_lang[$i]['lang_folder'].'/flag.png" style="vertical-align: baseline; width:18px; height:auto;">';
	echo '<a class="btn btn-fc" href="acp.php?set_lang='.$arr_lang[$i]['lang_folder'].'">'.$lang_icon.' '.$arr_lang[$i]['lang_desc'].'</a> ';
}
			?>
			</p>
			
		<p><b>flatCore</b> Content Management System (<?php echo $fc_version_name . ' <small>B: ' . $fc_version_build; ?>)</small><br />
		copyright © <?php echo date('Y'); ?>, <a href="https://www.flatcore.org/" target="_blank">flatCore.org</a> | <a href="https://github.com/flatCore/flatCore-CMS"><i class="fab fa-github"></i> flatCore-CMS</a></p>
		<p class="d-none"><?php echo microtime(true)-$_SERVER['REQUEST_TIME_FLOAT']; ?></p>
		</div>
		
		</div>
		
		<div class="bottom-bar">
			<?php
				if($acptheme == 'dark') {
					echo '<a class="btn btn-sm btn-fc" href="acp.php?tn='.$tn.'&theme=true">Light Theme</a>';
				} else {
					echo '<a class="btn btn-sm btn-fc" href="acp.php?tn='.$tn.'&theme=true">Dark Theme</a>';
				}
			?>
			<button type="button" class="btn btn-dark btn-sm" data-toggle="modal" data-target="#uploadModal"><?php echo $icon['upload']; ?> Upload</button>
		</div>
		<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-hidden="true">
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h4 class="modal-title" id="myModalLabel"><?php echo $icon['upload']; ?> Upload</h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      </div>
		      <div class="modal-body">
		        <?php include 'core/files.upload.php'; ?>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-fc" data-dismiss="modal">Close</button>
		      </div>
		    </div>
		  </div>
		</div>



		<script type="text/javascript">
			
			$(function() {

				/* toggle editor class [mceEditor|plain|aceEditor_html] */
				var editor_mode = localStorage.getItem('editor_mode');	
				if(!editor_mode) {
					editor_mode = 'optE1';
					localStorage.setItem("editor_mode", editor_mode);
				}
								
		    $('input[name="optEditor"]').on("change", function () {
			    var button = $("input[name='optEditor']:checked").val();
	    		localStorage.setItem("editor_mode", button);
	    		switchEditorMode(button);
				});
							
				if(editor_mode !== 'optE1') {
					switchEditorMode(editor_mode);
				} else {
					<?php echo $tinyMCE_config_contents; ?>
				}
				
				//setAceEditor();
				
				$("input[value="+editor_mode+"]").parent().addClass('active');
			
				function switchEditorMode(mode) {
					
					var textEditor = $('textarea[class*=switchEditor]');
					textEditor.removeClass();
					textEditor.removeAttr('style');
					var divEditor = $('.aceCodeEditor');
					
		  		if(mode == 'optE1') {
			  		/* switch to wysiwyg */
		    		textEditor.addClass('mceEditor form-control switchEditor');
		    		textEditor.css("display","flex");
		    		divEditor.remove();
		    		/* load configs again */
		    		<?php echo $tinyMCE_config_contents; ?>
		    		tinymce.EditorManager.execCommand('mceAddEditor',false, '#textEditor');
		  		}
		  		if(mode == 'optE2') {
			  		/* switch to plain textarea */
			  		divEditor.remove();
		    		textEditor.addClass('plain form-control switchEditor');
		    		textEditor.css("display","block");
		    		textEditor.css("visibility","visible");
			  		if(tinymce.editors.length > 0) {
				  		$('div.mceEditor').remove();
							tinymce.remove('.switchEditor');
						}
		  		}
		  		if(mode == 'optE3') {
			  		/* switch to ace editor */
			  		if(tinymce.editors.length > 0) {
			  			tinymce.EditorManager.execCommand('mceRemoveEditor',true, '#textEditor');
			  			$('div.mceEditor').remove();
							tinymce.remove();
						}
						textEditor.addClass('aceEditor_code form-control switchEditor');
		    		setAceEditor();
		  		}	
					
				}
				
				function setAceEditor() {
					if($('.aceEditor_code').length != 0) {
						$('textarea[class*=switchEditor]').each(function () {
							
							var textarea = $(this);
							var textarea_id = textarea.attr('id');
							var editDiv = $('<div>', {
                position: 'absolute',
                'class': textarea.attr('class')+' aceCodeEditor'
            	}).insertBefore(textarea);
            	
            	var HTMLtextarea = $('textarea[class*=aceEditor_code]').hide();
							var aceEditor = ace.edit(editDiv[0]);
							aceEditor.$blockScrolling = Infinity;
							aceEditor.getSession().setMode({ path:'ace/mode/html', inline:true });
							aceEditor.getSession().setValue(textarea.val());
							aceEditor.setTheme("ace/theme/" + ace_theme);
							aceEditor.getSession().setUseWorker(false);
							aceEditor.setShowPrintMargin(false);
							
							aceEditor.getSession().on('change', function(){
								textarea.val(aceEditor.getSession().getValue());
							});
		
						});
				  }
				}

				/* dirty forms */
				$('form').dirtyForms();
				$.DirtyForms.dialog = false;
				
				
				$("#toggleExpand").click(function() {
				  $('.info-collapse').toggleClass('info-hide');
				  $('.glyphicon-collapse-down').toggleClass('glyphicon-collapse-up');
				  $('.controls-container .btn-sm').toggleClass('btn-xs');
				});
							

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
				$('[data-toggle="popover"]').popover()

				
				$(".fancybox").fancybox();
				
				$(".fancybox-iframe").fancybox({
					type: 'iframe',
					autoWidth: true,
					autoHeight: true
				});
				
				$(".fancybox-docs").fancybox({
					type: 'iframe',
					width: '77%',
					height: '90%',
					buttons: ['close'],
				});
												
				$(".fancybox-ajax").fancybox({
					type: 'ajax',
					minWidth: '450px',
					height: '90%'
				});
				
				
				$("select.image-picker").imagepicker({
		    	hide_select : true,
		      show_label  : true
				});
				
				$('.filter-images').keyup(function() {
		    	var value = $(this).val();
					var exp = new RegExp('^' + value, 'i');
				
			    $('.thumbnail').not('.selected').each(function() {
			        var isMatch = exp.test($('p:first', this).text());
			        $(this).toggle(isMatch);
			    });
				});

			
	      $(document).on('mouseenter', '.hiddenControls', function () {
					$(this).find('.controls').fadeIn();
	      }).on('mouseleave', '.hiddenControls', function () {
					$(this).find('.controls').hide();
				});

		    
				$(document).on('click', 'a[href^=\\#]', function(e){
        	e.preventDefault();
					var id = $(this).attr('href');
					$('html,body').animate({scrollTop: $(id).offset().top}, 500);
    		});		    
		    
				
		    
		    
				
				Dropzone.options.myDropzone = {
			  	init: function() {
			    	this.on("success", function(file, responseText) {
							file.previewTemplate.appendChild(document.createTextNode(responseText));
						});
					}
				};
				
				Dropzone.options.dropAddons = {
			  	init: function() {
			    	this.on("success", function(file, responseText) {
			      	window.location.href = "acp.php?tn=moduls&sub=u";
						});
					}
				};
	
			  var options = {   
			      'originalStyle': 'text-left',
			      'displayFormat': '<span class="label label-default">#input</span> <span class="label label-default">#words</span>'  
			  };  
			  $('.cntValues').textareaCount(options);  
			  
		

			
				$.fn.matchHeight._update('.equal');
				
				/* css and html editor for page header */
				if($('#CSSeditor').length != 0) {
					var CSSeditor = ace.edit("CSSeditor");
					var CSStextarea = $('textarea[class*=aceEditor_css]').hide();
				  CSSeditor.$blockScrolling = Infinity;
				  CSSeditor.getSession().setValue(CSStextarea.val());
				  CSSeditor.setTheme("ace/theme/" + ace_theme);
				  CSSeditor.getSession().setMode("ace/mode/css");
				  CSSeditor.getSession().setUseWorker(false);
				  CSSeditor.setShowPrintMargin(false);
				  CSSeditor.getSession().on('change', function(){
						CSStextarea.val(CSSeditor.getSession().getValue());
					});
				}
				
				if($('#HTMLeditor').length != 0) {
					var HTMLeditor = ace.edit("HTMLeditor");
					var HTMLtextarea = $('textarea[class*=aceEditor_html]').hide();
				  HTMLeditor.$blockScrolling = Infinity;
				  HTMLeditor.getSession().setValue(HTMLtextarea.val());
				  HTMLeditor.setTheme("ace/theme/" + ace_theme);
				  HTMLeditor.getSession().setMode({ path:'ace/mode/html', inline:true });
				  HTMLeditor.getSession().setUseWorker(false);
				  HTMLeditor.setShowPrintMargin(false);
					HTMLeditor.getSession().on('change', function(){
						HTMLtextarea.val(HTMLeditor.getSession().getValue());
					});
			  }
			  
			  /* ace editor instead of <pre>, readonly */
			  $('textarea[data-editor]').each(function () {
			  	var textarea = $(this);
			  	var mode = textarea.data('editor');
					var editDiv = $('<div>', {
                position: 'absolute',
                width: '100%',
                height: '400px',
                'class': textarea.attr('class')
            }).insertBefore(textarea);
            textarea.css('display', 'none');
            var editor = ace.edit(editDiv[0]);
            editor.$blockScrolling = Infinity;
            editor.getSession().setValue(textarea.val());
            editor.setTheme("ace/theme/" + ace_theme);
            editor.getSession().setMode("ace/mode/" + mode);
            editor.getSession().setUseWorker(false);
            editor.setShowPrintMargin(false);
            editor.setReadOnly(true);
			  });
			  
			});

	$(document).ready(function() {
   	stretchAppContainer();
   	
	  $( "div.scroll-box" ).each(function() {
	  	var divTop = $(this).offset().top;
	   	var newHeight = $('div.app-container').innerHeight() - divTop +40;
	   	$(this).height(newHeight);
	  });


		// filter snippets
		/*
		$('.filter-list').keyup(function() {
	
			var value = $(this).val();
			var exp = new RegExp('^' + value, 'i');
				
			$('a.filter-list-item').each(function() {
				var isMatch = exp.test($(this).data("title"));
				$(this).toggle(isMatch);
			});
		});
		*/

	
	
	//SIDEBAR
	
var sidebarState = sessionStorage.getItem('sidebarState');

windowWidth = $(window).width();

$(window).resize(function() {
  windowWidth = $(window).width();

  if( windowWidth < 992 ){ //992 is the value of $screen-md-min in boostrap variables.scss
					$('#page-sidebar-inner').addClass('sidebar-collapsed').removeClass('sidebar-expanded');
					$('#page-content').addClass('sb-collapsed').removeClass('sb-expanded');
					$('#page-sidebar').addClass('sb-collapsed').removeClass('sb-expanded');
    }
    else{
        if(sidebarState){
					$('#page-sidebar-inner').addClass('sidebar-collapsed').removeClass('sidebar-expanded');
					$('#page-content').addClass('sb-collapsed').removeClass('sb-expanded');
					$('#page-sidebar').addClass('sb-collapsed').removeClass('sb-expanded');
        }
        else{
    $('#page-sidebar-inner').addClass('sidebar-expanded').removeClass('sidebar-collapsed');
    $('#page-content').addClass('sb-expanded').removeClass('sb-collapsed');
    $('#page-sidebar').addClass('sb-expanded').removeClass('sb-collapsed');
        }
    }
});

function setSidebarState(value){
    sessionStorage.setItem('sidebarState', value);
}

function clearSidebarState(){
    sessionStorage.removeItem('sidebarState');
}

function collapseSidebar(){
    $('#page-sidebar-inner').addClass('sidebar-collapsed').removeClass('sidebar-expanded');
    $('#page-content').addClass('sb-collapsed').removeClass('sb-expanded');
    $('#page-sidebar').addClass('sb-collapsed').removeClass('sb-expanded');
    $('.caret_left').addClass('d-none');
    $('.caret_right').removeClass('d-none');
}

function expandSidebar(){
    $('#page-sidebar-inner').addClass('sidebar-expanded').removeClass('sidebar-collapsed');
    $('#page-content').addClass('sb-expanded').removeClass('sb-collapsed');
    $('#page-sidebar').addClass('sb-expanded').removeClass('sb-collapsed');
    $('.caret_right').addClass('d-none');
    $('.caret_left').removeClass('d-none');
}

$(function(){

    /** check sessionStorage to expand/collapse sidebar onload **/
    if (sidebarState == "collapsed") {
    	collapseSidebar();
    } else {

    	if( windowWidth < 992 ) {
				$('#page-sidebar-inner').addClass('sidebar-collapsed').removeClass('sidebar-expanded');
				$('#page-content').addClass('sb-collapsed').removeClass('sb-expanded');
				$('#page-sidebar').addClass('sb-collapsed').removeClass('sb-expanded');
      } else {
      
      	if(sidebarState){
					$('#page-sidebar-inner').addClass('sidebar-collapsed').removeClass('sidebar-expanded');
					$('#page-content').addClass('sb-collapsed').removeClass('sb-expanded');
					$('#page-sidebar').addClass('sb-collapsed').removeClass('sb-expanded');
        } else {
					$('#page-sidebar-inner').addClass('sidebar-expanded').removeClass('sidebar-collapsed');
					$('#page-content').addClass('sb-expanded').removeClass('sb-collapsed');
					$('#page-sidebar').addClass('sb-expanded').removeClass('sb-collapsed');
				  $('.caret_right').addClass('d-none');
					$('.caret_left').removeClass('d-none');        }
      }  
    }


    /** collapse the sidebar navigation **/    
    $('#toggleNav').click(function(){
        if(!($('#page-sidebar-inner').hasClass('sidebar-collapsed'))) { // if sidebar is not yet collapsed
          collapseSidebar();
          setSidebarState('collapsed');
        } else {
        	expandSidebar();
          clearSidebarState();
        }
        return false;
    })
})

//SIDEBAR
	
	
	});
	


  $(window).resize(function () {
  	stretchAppContainer();
		$( "div.scroll-box" ).each(function() {
			var divTop = $(this).offset().top;
		  var newHeight = $('div.app-container').innerHeight() - divTop +40;
		  $(this).height(newHeight);
		});
  });


  function stretchAppContainer() {
  	var appContainer = $('div.app-container');
  	if(appContainer.length) {
	  	if(window.matchMedia('(max-width: 767px)').matches) {
				appContainer.height('auto');
			} else {
    		var divTop = appContainer.offset().top;
				var winHeight = $(window).height();
				var divHeight = winHeight - divTop;
				appContainer.height(divHeight);
			}
    }
  }

   
	<?php
		$maxlifetime = ini_get("session.gc_maxlifetime");
		echo "var maxlifetime = '{$maxlifetime}';";
	?>
	
	
 var countdown = {
        startInterval : function() {
            var currentId = setInterval(function(){
                $('#currentSeconds').html(maxlifetime);
                
                if(maxlifetime == 60) {
	                $('#expireDiv').removeClass('expire-hidden');
	                $('#expireDiv').addClass('expire-start');
                }
                if(maxlifetime == 30) {
	                $('#expireDiv').addClass('expire-soon');
                }
                if(maxlifetime == 15) {
	                $('#expireDiv').addClass('expire-danger');
                }
                if(maxlifetime == 0) {
	                //window.location.href = "/index.php?goto=logout";
                }
                --maxlifetime;
            }, 1000);
            countdown.intervalId = currentId;
        }
    };
    countdown.startInterval();

</script>

	</body>
</html>