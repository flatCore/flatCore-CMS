<?php

require 'access.php';

$core_docs = fc_get_core_docs();
$modules_docs = fc_get_modules_docs();
$themes_docs = fc_get_themes_docs();

$all_docs = $core_docs;
if(is_array($modules_docs)) {
	$all_docs = array_merge($all_docs, $modules_docs);
}

if(is_array($themes_docs)) {
	$all_docs = array_merge($all_docs, $themes_docs);
}

$docs_file = '';

if(isset($_POST['docs_file'])) {
	$docs_file_id = (int) $_POST['docs_file'];
	if(is_file($all_docs[$docs_file_id])) {
		$_SESSION['docs_file'] = $all_docs[$docs_file_id];
	}
}


echo '<form action="#" method="post" id="setHelp">';
echo '<div class="row">';
echo '<div class="col-md-9">';
echo '<select name="docs_file" class="form-control custom-select" onchange="this.form.submit()">';

echo '<optgroup label="flatCore">';
foreach($all_docs as $k => $v) {
	
	if(substr($v, 0, 4) != 'docs') {
		continue;
	}
	
	$sel = '';
	if($_SESSION['docs_file'] == $v) {
		$sel = 'selected';
	}
	echo '<option value="'.$k.'" '.$sel.'>'.basename($v).'</option>';
}
echo '</optgroup>';

echo '<optgroup label="Modules">';
foreach($all_docs as $k => $v) {
	
	if(substr($v, 0, 11) != '../modules/') {
		continue;
	}
	
	$sel = '';
	if($_SESSION['docs_file'] == $v) {
		$sel = 'selected';
	}
	
	$path = explode('/', $v);	
	echo '<option value="'.$k.'" '.$sel.'>'.$path[2].' > '.basename($v).'</option>';
}
echo '</optgroup>';

echo '<optgroup label="Themes">';
foreach($all_docs as $k => $v) {
	
	if(substr($v, 0, 10) != '../styles/') {
		continue;
	}
	
	$sel = '';
	if($_SESSION['docs_file'] == $v) {
		$sel = 'selected';
	}
	
	$path = explode('/', $v);	
	echo '<option value="'.$k.'" '.$sel.'>'.$path[2].' > '.basename($v).'</option>';
}
echo '</optgroup>';

echo '</select>';

echo '</div>';
echo '<div class="col-md-3">';
echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '<button type="submit" name="s" class="btn btn-fc btn-block">'.$icon['sync_alt'].'</button>';
echo '</div>';
echo '</div>';

echo '</form>';


if(isset($_SESSION['docs_file'])) {
	$help_text = file_get_contents($_SESSION['docs_file']);
	$Parsedown = new Parsedown();
	$help_text_html = $Parsedown->text($help_text);
	echo '<hr>'.$help_text_html;
}




$helpURL = 'https://flatcore.org/documentation/';

if($languagePack == 'de') {
	$helpURL = 'https://flatcore.org/de/dokumentation/';	
}

echo '<hr><a target="_blank" href="'.$helpURL.'" title="'.$helpURL.'" class="btn btn-fc btn-block">'.$icon['question'].' '.$lang['show_help'].'</a>';

?>
