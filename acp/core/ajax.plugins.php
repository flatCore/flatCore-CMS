<?php

session_start();
error_reporting(0);

require("../../config.php");

define("CONTENT_DB", "../../$fc_db_content");
define("FC_ROOT", str_replace("/acp","",FC_INC_DIR));
define("IMAGES_FOLDER", "../$img_path");
define("FILES_FOLDER", "../$files_path");

require_once('access.php');
require_once('functions.php');
require_once('database.php');
require('../../lib/lang/'.$_SESSION['lang'].'/dict-backend.php');


if(isset($_POST['plugin'])) {
	$file = basename($_POST['plugin']);
	
	if(is_file('../../'.FC_CONTENT_DIR.'/plugins/'.$file)) {
		$filepath = '../../'.FC_CONTENT_DIR.'/plugins/'.$file;
		$content = $_POST['plugin_src'];
		file_put_contents($filepath, $content, LOCK_EX);
	}
	
	$message = '<div class="alert alert-success alert-auto-close">'.$lang['db_changed'].'</div>';
}


$plugin = basename($_REQUEST['plugin']);

if(is_file('../../'.FC_CONTENT_DIR.'/plugins/'.$plugin)) {
	$plugin_src = file_get_contents('../../'.FC_CONTENT_DIR.'/plugins/'.$plugin);
	$plugin_src = htmlentities($plugin_src,ENT_QUOTES,"UTF-8");
	
	if(!is_writable('../../'.FC_CONTENT_DIR.'/plugins/'.$plugin)) {
		$message = '<div class="alert alert-info">The file is not writable</div>';
	}
	
}


$form_tpl = file_get_contents('../templates/plugin-edit-form.tpl');


$form_tpl = str_replace('{form_action}', "#", $form_tpl);
$form_tpl = str_replace('{plugin}', $plugin, $form_tpl);
$form_tpl = str_replace('{filename}', $plugin, $form_tpl);
$form_tpl = str_replace('{label_filename}', $lang['label_filename'], $form_tpl);
$form_tpl = str_replace('{label_content}', $lang['label_content'], $form_tpl);
$form_tpl = str_replace('{plugin_src}', $plugin_src, $form_tpl);
$form_tpl = str_replace('{save}', $lang['save'], $form_tpl);
$form_tpl = str_replace('{message}', $message, $form_tpl);

echo $form_tpl;


?>

<script>
$(document).ready(function(){
  $("#pluginForm").bind("submit", function() {
      $.ajax({
          type : "POST",
          cache : false,
          url: "../acp/core/ajax.plugins.php",
          data: $(this).serializeArray(),
          success:function(data){
              $.fancybox(data);
          }
      });
      return false;
	});
	$('.fancybox').fancybox({
			minWidth: '50%',
			height: '90%'
		});
	setTimeout(function() {
      $(".alert-auto-close").slideUp('slow');
	}, 2000);

	var HTMLeditor = ace.edit("aceCodeEditor");
	var HTMLtextarea = $('textarea[class*=aceEditor_code]').hide();
				  
	HTMLeditor.getSession().setValue(HTMLtextarea.val());
	HTMLeditor.setTheme("ace/theme/chrome");
	HTMLeditor.getSession().setMode("ace/mode/php");
	HTMLeditor.setShowPrintMargin(false);
	HTMLeditor.getSession().on('change', function(){
	HTMLtextarea.val(HTMLeditor.getSession().getValue());
	});
	
});

</script>