<?php

session_start();
error_reporting(0);

require_once 'access.php';

$set_lang = $languagePack;
if(isset($_REQUEST['set_lang'])) {
	$set_lang = $_REQUEST['set_lang'];
	unset($media_data);
}

$form_tpl = file_get_contents('templates/media-edit-form.tpl');


if(isset($_REQUEST['file'])) {
	$media_filename = strip_tags($_REQUEST['file']);
	if(stripos($_REQUEST['file'],"$files_path") !== FALSE) {
		$preview_src = '<p>Filetype: '.substr(strrchr($media_filename, "."), 1).'</p>';
		$realpath = $media_filename;
		$img_dimensions = '';
		$shortcode = 'file';
	} else {
		$preview_src = '<img src="'. $media_filename.'" class="img-fluid">';
		$realpath = $media_filename;
		list($img_width, $img_height) = getimagesize("./$media_filename");
		$img_dimensions = ' | '.$img_width.' x '.$img_height.' px';
		$shortcode = 'image';
	}	
}

$abs_path = str_replace('../','/',$realpath);
$filesize = filesize("../$realpath");
$rfilesize = readable_filesize(filesize("$realpath"));
$lastedit = date('d.m.Y H:i',filemtime("$realpath"));


if(isset($_POST['save'])) {
	$savedMedia = fc_write_media_data($_POST['realpath'],$_POST['title'],$_POST['notes'],$_POST['keywords'],$_POST['text'],$_POST['url'],$_POST['alt'],$_POST['set_lang'],$_POST['credit'],$_POST['priority'],$_POST['license'],time(),$filesize,$_POST['version'],$_POST['media_labels']);
	if($savedMedia == 'success') {
		$message = '<div class="alert alert-success alert-auto-close">'.$lang['db_changed'].'</div>';
	} else {
		$message = '<div class="alert alert-danger alert-auto-close">'.$lang['db_not_changed'].$savedMedia.'</div>';
	}
	$form_tpl = str_replace('{message}', $message, $form_tpl);
} else {
	$form_tpl = str_replace('{message}', '', $form_tpl);
}


echo '<div class="subHeader">';
echo '<a class="btn btn-fc" href="?tn=filebrowser&sub=browse">'.$icon['angle_left'].'</a> ';
echo '<span class="ms-3">' . $media_filename.'</span>';
echo '</div>';

$arr_lang = get_all_languages();

$langSwitch = '<div class="btn-group" role="group">';
foreach($arr_lang as $langs) {
	$btn_status = '';
	if($langs['lang_sign'] == "$set_lang") { $btn_status = 'active'; }
	$langSwitch .= '<button type="submit" class="btn btn-fc btn-sm '.$btn_status.'" name="set_lang" value="'.$langs['lang_sign'].'">'.$langs['lang_sign'].'</button>';
}
$langSwitch .= '</div>';
$langSwitch .= '<input type="hidden" name="file" value="'.$media_filename.'">';
$langSwitch .= '<input type="hidden" name="folder" value="'.$_REQUEST['folder'].'">';


$media_data = fc_get_media_data($realpath,$set_lang);


/* labels */

$cnt_labels = count($fc_labels);
$arr_checked_labels = explode(",", $media_data['media_labels']);

for($i=0;$i<$cnt_labels;$i++) {
	$label_title = $fc_labels[$i]['label_title'];
	$label_id = $fc_labels[$i]['label_id'];
	$label_color = $fc_labels[$i]['label_color'];
	
  if(in_array("$label_id", $arr_checked_labels)) {
		$checked_label = "checked";
	} else {
		$checked_label = "";
	}
	
	$checkbox_set_labels .= '<div class="form-check form-check-inline">';
 	$checkbox_set_labels .= '<input class="form-check-input" id="label'.$label_id.'" type="checkbox" '.$checked_label.' name="media_labels[]" value="'.$label_id.'">';
 	$checkbox_set_labels .= '<label class="form-check-label" for="label'.$label_id.'" style="border-bottom: 1px solid '.$label_color.'">'.$label_title.'</label>';
	$checkbox_set_labels .= '</div>';
}

$form_tpl = str_replace('{media_labels}', $checkbox_set_labels, $form_tpl);

$form_tpl = str_replace('{form_action}', "?tn=filebrowser&sub=edit", $form_tpl);
$form_tpl = str_replace('{filename}', $media_filename, $form_tpl);
$form_tpl = str_replace('{file}', $media_filename, $form_tpl);
$form_tpl = str_replace('{basename}', basename($media_filename), $form_tpl);
$form_tpl = str_replace('{realpath}', $realpath, $form_tpl);
$form_tpl = str_replace('{showpath}', $abs_path, $form_tpl);
$form_tpl = str_replace('{rfilesize}', $rfilesize, $form_tpl);
$form_tpl = str_replace('{image_dimensions}', $img_dimensions, $form_tpl);
$form_tpl = str_replace('{edittime}', $lastedit, $form_tpl);
$form_tpl = str_replace('{folder}', $_REQUEST['folder'], $form_tpl);
$form_tpl = str_replace('{title}', $media_data['media_title'], $form_tpl);
$form_tpl = str_replace('{description}', $media_data['media_description'], $form_tpl);
$form_tpl = str_replace('{keywords}', $media_data['media_keywords'], $form_tpl);
$form_tpl = str_replace('{text}', $media_data['media_text'], $form_tpl);
$form_tpl = str_replace('{label_title}', $lang['label_title'], $form_tpl);
$form_tpl = str_replace('{label_description}', $lang['label_description'], $form_tpl);
$form_tpl = str_replace('{label_keywords}', $lang['label_keywords'], $form_tpl);
$form_tpl = str_replace('{label_alt}', $lang['label_alt'], $form_tpl);
$form_tpl = str_replace('{alt}', $media_data['media_alt'], $form_tpl);
$form_tpl = str_replace('{label_url}', $lang['label_url'], $form_tpl);
$form_tpl = str_replace('{url}', $media_data['media_url'], $form_tpl);
$form_tpl = str_replace('{label_priority}', $lang['label_priority'], $form_tpl);
$form_tpl = str_replace('{priority}', $media_data['media_priority'], $form_tpl);
$form_tpl = str_replace('{label_license}', $lang['label_license'], $form_tpl);
$form_tpl = str_replace('{license}', $media_data['media_license'], $form_tpl);
$form_tpl = str_replace('{label_credits}', $lang['label_credits'], $form_tpl);
$form_tpl = str_replace('{version}', $media_data['media_version'], $form_tpl);
$form_tpl = str_replace('{label_version}', $lang['label_version'], $form_tpl);
$form_tpl = str_replace('{credit}', $media_data['media_credit'], $form_tpl);
$form_tpl = str_replace('{label_notes}', $lang['label_notes'], $form_tpl);
$form_tpl = str_replace('{notes}', $media_data['media_notes'], $form_tpl);
$form_tpl = str_replace('{label_text}', $lang['label_text'], $form_tpl);
$form_tpl = str_replace('{preview}', $preview_src, $form_tpl);
$form_tpl = str_replace('{save}', $lang['save'], $form_tpl);
$form_tpl = str_replace('{set_lang}', $set_lang, $form_tpl);
$form_tpl = str_replace('{filesize}', $filesize, $form_tpl);
$form_tpl = str_replace('{lang_switch}', $langSwitch, $form_tpl);
$form_tpl = str_replace('{shortcode}', $shortcode, $form_tpl);
$form_tpl = str_replace('{token}',$_SESSION['token'],$form_tpl);

echo $form_tpl;


?>