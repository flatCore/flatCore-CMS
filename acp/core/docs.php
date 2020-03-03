<?php


$core_docs = fc_get_core_docs();
$modules_docs = fc_get_modules_docs();
$themes_docs = fc_get_themes_docs();
$docs_file = '';

if(isset($_POST['docs_file'])) {
	$docs_file = $_POST['docs_file'];
	if(is_file($docs_file)) {
		$_SESSION['docs_file'] = $docs_file;
	}
	
}




echo '<form action="#" method="post" id="setHelp">';
echo '<div class="row">';
echo '<div class="col-md-9">';
echo '<select name="docs_file" class="form-control custom-select" onchange="this.form.submit()">';

echo '<optgroup label="flatCore">';
foreach($core_docs as $d) {
	$sel = '';
	if($_SESSION['docs_file'] == $d) {
		$sel = 'selected';
	}
	echo '<option value="'.$d.'" '.$sel.'>'.basename($d).'</option>';
}
echo '</optgroup>';

echo '<optgroup label="Modules">';
foreach($modules_docs as $d) {
	$sel = '';
	if($_SESSION['docs_file'] == $d) {
		$sel = 'selected';
	}
	$path = explode('/', $d);
	echo '<option value="'.$d.'" '.$sel.'>'.$path[2].' > '.basename($d).'</option>';
}
echo '</optgroup>';

echo '<optgroup label="Themes">';
foreach($themes_docs as $d) {
	$sel = '';
	if($_SESSION['docs_file'] == $d) {
		$sel = 'selected';
	}
	$path = explode('/', $d);
	echo '<option value="'.$d.'" '.$sel.'>'.$path[2].' > '.basename($d).'</option>';
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
