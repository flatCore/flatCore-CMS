<?php


$doc_file = basename($_GET['tn']);

if(isset($_GET['sub'])) {
	$doc_file .= '-'.basename($_GET['sub']);
}

$doc_file .= '.md';

$doc_inc = 'docs/'.$languagePack.'/'.$doc_file;

if(is_file($doc_inc)) {
	
	$help_text = file_get_contents($doc_inc);
	$Parsedown = new Parsedown();
	$help_text_html = $Parsedown->text($help_text);
	
	echo $help_text_html;
	
} else {
	echo '<hr>';
	echo '<div class="alert alert-info">';
	echo '<p>'.$lang['msg_no_help_doc'].'</p>';
	echo '</div>';
}

$helpURL = 'https://flatcore.org/documentation/';

if($languagePack == 'de') {
	$helpURL = 'https://flatcore.org/de/dokumentation/';	
}

echo '<hr><a target="_blank" href="'.$helpURL.'" title="'.$helpURL.'" class="btn btn-fc btn-block">'.$icon['question'].' '.$lang['show_help'].'</a>';

?>