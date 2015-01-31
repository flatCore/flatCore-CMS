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

$form_tpl = file_get_contents('../templates/media-edit-form.tpl');

if(isset($_REQUEST['file'])) {
	$media_filename = basename($_REQUEST['file']);
	
	if($_REQUEST['folder'] == "2") {
		$preview_src = '<p>Filetype: '.substr(strrchr($media_filename, "."), 1).'</p>';
		$realpath = FILES_FOLDER . '/' . $media_filename;
	} else {
		$preview_src = '<img src="'.IMAGES_FOLDER . '/' . $media_filename.'" class="img-responsive">';
		$realpath = IMAGES_FOLDER . '/' . $media_filename;
	}	
}

$abs_path = str_replace('../','/',$realpath);
$filesize = readable_filesize(filesize("../$realpath"));
$lastedit = date('d.m.Y H:i',filemtime("../$realpath"));

if(isset($_POST['saveMedia'])) {
	$savedMedia = fc_write_media_data($_POST['realpath'],$_POST['title'],$_POST['description'],$_POST['keywords'],$_POST['text']);
	if($savedMedia == 'success') {
		$message = '<div class="alert alert-success alert-auto-close">'.$lang['db_changed'].'</div>';
	} else {
		$message = '<div class="alert alert-danger alert-auto-close">'.$lang['db_not_changed'].$savedMedia.'</div>';
	}
	$form_tpl = str_replace('{message}', $message, $form_tpl);
} else {
	$form_tpl = str_replace('{message}', '', $form_tpl);
}


$media_data = fc_get_media_data($realpath);


$form_tpl = str_replace('{form_action}', "#", $form_tpl);
$form_tpl = str_replace('{filename}', $media_filename, $form_tpl);
$form_tpl = str_replace('{realpath}', $realpath, $form_tpl);
$form_tpl = str_replace('{showpath}', $abs_path, $form_tpl);
$form_tpl = str_replace('{filesize}', $filesize, $form_tpl);
$form_tpl = str_replace('{edittime}', $lastedit, $form_tpl);
$form_tpl = str_replace('{folder}', $_REQUEST['folder'], $form_tpl);
$form_tpl = str_replace('{title}', $media_data['media_title'], $form_tpl);
$form_tpl = str_replace('{description}', $media_data['media_description'], $form_tpl);
$form_tpl = str_replace('{keywords}', $media_data['media_keywords'], $form_tpl);
$form_tpl = str_replace('{text}', $media_data['media_text'], $form_tpl);
$form_tpl = str_replace('{label_title}', $lang['label_title'], $form_tpl);
$form_tpl = str_replace('{label_description}', $lang['label_description'], $form_tpl);
$form_tpl = str_replace('{label_keywords}', $lang['label_keywords'], $form_tpl);
$form_tpl = str_replace('{label_text}', $lang['label_text'], $form_tpl);
$form_tpl = str_replace('{preview}', $preview_src, $form_tpl);
$form_tpl = str_replace('{save}', $lang['save'], $form_tpl);

echo $form_tpl;


?>

<script>
$(document).ready(function(){
  $("#media_form").bind("submit", function() {
      $.ajax({
          type : "POST",
          cache : false,
          url: "../acp/core/ajax.media.php",
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
});

</script>