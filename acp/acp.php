<?php
session_start();
error_reporting(0);

require '../lib/Medoo.php';
use Medoo\Medoo;

require '../lib/Spyc/Spyc.php';
include 'core/icons.php';

require '../config.php';
if(is_file(FC_CONTENT_DIR.'/config.php')) {
	include FC_CONTENT_DIR.'/config.php';
}
if(is_file(FC_CONTENT_DIR.'/config_smtp.php')) {
	include FC_CONTENT_DIR.'/config_smtp.php';
}


if(is_file('../config_database.php')) {
	include '../config_database.php';
	$db_type = 'mysql';
	
	$database = new Medoo([
		'type' => 'mysql',
		'database' => "$database_name",
		'host' => "$database_host",
		'username' => "$database_user",
		'password' => "$database_psw",
		'charset' => 'utf8',
		'port' => $database_port,
		'prefix' => DB_PREFIX
	]);
	
	$db_content = $database;
	$db_user = $database;
	$db_statistics = $database;
	$db_posts = $database;
	
	
} else {
	$db_type = 'sqlite';
	
	if(isset($fc_content_files) && is_array($fc_content_files)) {
		/* switch database file $fc_db_content */
		include 'core/contentSwitch.php';
	}
	
	
	define("CONTENT_DB", "$fc_db_content");
	define("USER_DB", "$fc_db_user");
	define("STATS_DB", "$fc_db_stats");
	define("POSTS_DB", "$fc_db_posts");

	$db_content = new Medoo([
		'type' => 'sqlite',
		'database' => CONTENT_DB
	]);
	
	$db_user = new Medoo([
		'type' => 'sqlite',
		'database' => USER_DB
	]);
	
	$db_statistics = new Medoo([
		'type' => 'sqlite',
		'database' => STATS_DB
	]);

	$db_posts = new Medoo([
		'type' => 'sqlite',
		'database' => POSTS_DB
	]);
	
}

define("INDEX_DB", "$fc_db_index");
define("FC_ROOT", str_replace("/acp","",FC_INC_DIR));
define("IMAGES_FOLDER", "$img_path");
define("FILES_FOLDER", "$files_path");
define("FC_SOURCE", "backend");


$db_index = new Medoo([
	'type' => 'sqlite',
	'database' => INDEX_DB
]);	



require 'core/access.php';
include 'versions.php';
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
require '../global/functions.php';
require 'core/switch.php';

$all_mods = get_all_moduls();
$all_plugins = get_all_plugins();
$fc_labels = fc_get_labels();
$cnt_labels = count($fc_labels);

/**
 * read the preferences
 * OLD: do not use the old $prefs_default_language
 * NEW: use $fc_prefs['default_language']
 */
 
$fc_get_preferences = fc_get_preferences();

foreach($fc_get_preferences as $k => $v) {
	$key = $fc_get_preferences[$k]['option_key'];
	$value = $fc_get_preferences[$k]['option_value'];
	$fc_preferences[$key] = $value;
    $fc_prefs[$key] = $value;
}

foreach($fc_prefs as $k => $v) {
   $$k = stripslashes($v);
}

/**
 * $default_lang_code (string) the default language code
 */
 
if($prefs_default_language != '') {
	include '../lib/lang/'.$prefs_default_language.'/index.php';
	$default_lang_code = $lang_sign; // de|en|es ...
}

/**
 * $lang_codes (array) all available lang codes
 * hide languages from $prefs_deactivated_languages
 */
$arr_lang = get_all_languages();
if($prefs_deactivated_languages != '') {
	$arr_lang_deactivated = json_decode($prefs_deactivated_languages);
}

foreach($arr_lang as $l) {
	if(is_array($arr_lang_deactivated) && (in_array($l['lang_folder'],$arr_lang_deactivated))) {
		continue;
	}
	
	$langs[] = $l['lang_sign'];
}
$lang_codes = array_values(array_unique($langs));

/* build absolute URL */
if($fc_preferences['prefs_cms_ssl_domain'] != '') {
	$fc_base_url = $prefs_cms_ssl_domain . $prefs_cms_base;
} else {
	$fc_base_url = $prefs_cms_domain . $prefs_cms_base;
}

if(!isset($_COOKIE['acptheme'])) {
	setcookie("acptheme", "dark",time()+(3600*24*365));
}

if(isset($_GET['theme']) && ($_GET['theme'] == 'light_mono')) {
	setcookie("acptheme", 'light_mono',time()+(3600*24*365));
	$set_acptheme = 'light_mono';
}

if(isset($_GET['theme']) && ($_GET['theme'] == 'light')) {
	setcookie("acptheme", 'light',time()+(3600*24*365));
	$set_acptheme = 'light';
}

if(isset($_GET['theme']) && ($_GET['theme'] == 'dark_mono')) {
	setcookie("acptheme", 'dark_mono',time()+(3600*24*365));
	$set_acptheme = 'dark_mono';
}

if(isset($_GET['theme']) && ($_GET['theme'] == 'dark')) {
	setcookie("acptheme", 'dark',time()+(3600*24*365));
	$set_acptheme = 'dark';
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
		
		<script src="theme/js/backend.min.js?v=20200117"></script>
		<script src="theme/js/tinymce/tinymce.min.js"></script>
		<script src="theme/js/tinymce-jquery/dist/tinymce-jquery.min.js"></script>
		<script src="theme/js/ace/ace.js" data-ace-base="theme/js/ace" type="text/javascript" charset="utf-8"></script>
		
		<?php
		if($acptheme == 'dark') {
			$style_file = 'theme/css/styles_dark.css?v='.time();
		} else if($acptheme == 'light') {			
			$style_file = 'theme/css/styles_light.css?v='.time();
		} else if($acptheme == 'dark_mono') {			
			$style_file = 'theme/css/styles_dark_mono.css?v='.time();
		} else {			
			$style_file = 'theme/css/styles_light_mono.css?v='.time();
		}
		echo '<link rel="stylesheet" href="'.$style_file.'" type="text/css" media="screen, projection">';
		?>
				
		<link href="fontawesome/css/all.min.css" rel="stylesheet">
		
		<script type="text/javascript">
			var languagePack = "<?php echo $languagePack; ?>";
			var ace_theme = 'chrome';
			var tinymce_skin = 'oxide';
			var acptheme = "<?php echo $acptheme; ?>";
			if(acptheme === 'dark' || acptheme === 'dark_mono') {
				var ace_theme = 'twilight';
				var tinymce_skin = 'oxide-dark';
			}
		</script>
		
		
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

		<div id="page-sidebar">
			<a id="sidebar-dashboard" href="acp.php?tn=dashboard"></a>
			<div id="page-sidebar-inner">
			<?php include 'core/'.$navinc.'.php'; ?>
			<?php include 'core/sidebar_footer.php'; ?>
			</div>
		</div>
		
		<div id="page-sidebar-help">
			<div id="page-sidebar-help-inner">		
				<?php require 'core/docs.php'; ?>
			</div>
		</div>
			
		<div id="page-content">

            <?php
            if(is_file('../maintenance.html')) {
                echo '<div class="alert alert-danger rounded-0 m-0">';
                echo $lang['msg_update_modus_activated'];
                echo '</div>';
            }
            ?>


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
			<hr>
		<p>
			<img src="images/fc-logo.svg" alt="fc-logo" width="60px"><br>
			<b>flatCore</b> Content Management System (<?php echo $fc_version_name . ' <small>B: ' . $fc_version_build; ?>)</small><br>
		copyright © <?php echo date('Y'); ?>, <a href="https://flatcore.org/" target="_blank">flatCore.org</a> | <a href="https://github.com/flatCore/flatCore-CMS"><i class="fab fa-github"></i> flatCore-CMS</a>
		</p>
		<p class="d-none"><?php echo microtime(true)-$_SERVER['REQUEST_TIME_FLOAT']; ?></p>
		</div>
		
		</div>
		
		<div class="bottom-bar">
			<?php			
				if($acptheme == 'dark') {
					$active_dark = 'active';
				} else if($acptheme == 'dark_mono') {
					$active_dark_mono = 'active';
				} else if($acptheme == 'light') {
					$active_light = 'active';
				} else {
					$active_light_mono = 'active';
				}
				
				echo '<a title="Light" class="styleswitch styleswitch-light '.$active_light.'" href="acp.php?tn='.$tn.'&theme=light">'.$icon['circle'].'</a>';
				echo '<a title="Light Mono" class="styleswitch styleswitch-light-mono '.$active_light_mono.'" href="acp.php?tn='.$tn.'&theme=light_mono">'.$icon['circle'].'</a>';
				echo '<a title="Dark" class="styleswitch styleswitch-dark '.$active_dark.'" href="acp.php?tn='.$tn.'&theme=dark">'.$icon['circle'].'</a>';
				echo '<a title="Dark Mono" class="styleswitch styleswitch-dark-mono '.$active_dark_mono.'" href="acp.php?tn='.$tn.'&theme=dark_mono">'.$icon['circle'].'</a>';
			?>
			<div class="divider"></div>
			<button type="button" class="btn btn-fc btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal"><?php echo $icon['upload']; ?> Upload</button>
		</div>
		<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-hidden="true">
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h4 class="modal-title" id="myModalLabel"><?php echo $icon['upload']; ?> Upload</h4>
		        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      </div>
		      <div class="modal-body">
		        <?php include 'core/files.upload.php'; ?>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-fc" data-bs-dismiss="modal">Close</button>
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
			  		if(tinymce.editors.length > 0) {
				  		tinymce.EditorManager.execCommand('mceRemoveEditor',true, '#textEditor');
				  		$('div.mceEditor').remove();
							tinymce.remove('.switchEditor');
							tinymce.remove();
						}
			  		divEditor.remove();
		    		textEditor.addClass('plain form-control switchEditor');
		    		textEditor.css("visibility","visible");
		    		textEditor.css("display","flex");
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
		  		
		  		$("input[name='optEditor']").parent().removeClass('active');
		  		$("input[value="+mode+"]").parent().addClass('active');
					
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

				  
			});

   
	<?php
		$gc_maxlifetime = ini_get("session.gc_maxlifetime");
		if($prefs_acp_session_lifetime > $gc_maxlifetime) {
			$maxlifetime = $prefs_acp_session_lifetime;
		} else {
			$maxlifetime = $gc_maxlifetime;
		}
		
		if($_COOKIE['identifier'] != '') {
			echo "var auto_logout = false;";
		} else {
			echo "var auto_logout = true;";
		}
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
                if(maxlifetime < 0) {
	                window.location.href = "/index.php?goto=logout";
                }
                --maxlifetime;
            }, 1000);
            countdown.intervalId = currentId;
        }
    };
    if(auto_logout !== false) {
	    countdown.startInterval();
    }

</script>

	</body>
</html>