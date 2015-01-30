<?php

session_start();
error_reporting(0);

require("../../config.php");

define("CONTENT_DB", "../../$fc_db_content");
define("FC_ROOT", str_replace("/acp","",FC_INC_DIR));
define("IMAGES_FOLDER", "../$img_path");
define("FILES_FOLDER", "$files_path");

require_once('access.php');
require_once('functions.php');
require_once('database.php');
require('../../lib/lang/'.$_SESSION['lang'].'/dict-backend.php');

$form_tpl = file_get_contents('../templates/media-edit-form.tpl');

if(isset($_REQUEST['image'])) {
	$image_filename = basename($_REQUEST['image']);
	$preview_src = '<img src="'.IMAGES_FOLDER . '/' . $image_filename.'" class="img-responsive">';
}


if(isset($_POST['saveImage'])) {
	$savedImage = fc_write_images_data($image_filename,$_POST['title'],$_POST['description'],$_POST['keywords'],$_POST['text']);
	if($savedImage == 'success') {
		$message = '<div class="alert alert-success alert-auto-close">'.$lang['db_changed'].'</div>';
	} else {
		$message = '<div class="alert alert-danger alert-auto-close">'.$lang['db_not_changed'].'</div>';
	}
	$form_tpl = str_replace('{message}', $message, $form_tpl);
} else {
	$form_tpl = str_replace('{message}', '', $form_tpl);
}


$images_data = fc_get_images_data($image_filename);


$form_tpl = str_replace('{form_action}', "#", $form_tpl);
$form_tpl = str_replace('{filename}', $image_filename, $form_tpl);
$form_tpl = str_replace('{title}', $images_data['media_title'], $form_tpl);
$form_tpl = str_replace('{description}', $images_data['media_description'], $form_tpl);
$form_tpl = str_replace('{keywords}', $images_data['media_keywords'], $form_tpl);
$form_tpl = str_replace('{text}', $images_data['media_text'], $form_tpl);
$form_tpl = str_replace('{label_title}', $lang['f_page_title'], $form_tpl);
$form_tpl = str_replace('{label_description}', $lang['f_meta_description'], $form_tpl);
$form_tpl = str_replace('{label_keywords}', $lang['f_meta_keywords'], $form_tpl);
$form_tpl = str_replace('{label_text}', $lang['tab_content'], $form_tpl);
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