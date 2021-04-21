<?php
//error_reporting(E_ALL ^E_NOTICE);
require 'access.php';

$core_docs = fc_get_core_docs();
$modules_docs = fc_get_modules_docs();
$themes_docs = fc_get_themes_docs();

$Parsedown = new Parsedown();

$all_docs = $core_docs;
if(is_array($modules_docs)) {
	$all_docs = array_merge($all_docs, $modules_docs);
}

if(is_array($themes_docs)) {
	$all_docs = array_merge($all_docs, $themes_docs);
}

$docs_file = '';

if(isset($_POST['docs_file'])) {
	$_SESSION['docs_file'] = (int) $_POST['docs_file'];
}

foreach($all_docs as $k => $v) {
	$parsed_docs[] = fc_parse_doc_md_file($v);
}


echo '<form action="#" method="post" id="setHelp">';
echo '<div class="row">';
echo '<div class="col-md-9">';
echo '<select name="docs_file" class="form-control custom-select" onchange="this.form.submit()">';

echo '<optgroup label="flatCore">';
foreach($parsed_docs as $k => $v) {
	
	if(substr($parsed_docs[$k]['filepath'], 0, 4) != 'docs') {
		continue;
	}
	
	$sel = '';
	if($_SESSION['docs_file'] == $k) {
		$sel = 'selected';
	}
	
	if($parsed_docs[$k]['header']['navigation'] != '') {
		$show_title = $parsed_docs[$k]['header']['navigation'];
	} else {
		$show_title = $parsed_docs[$k]['filename'];
	}
	
	echo '<option value="'.$k.'" '.$sel.'>'.$show_title.'</option>';
}
echo '</optgroup>';

echo '<optgroup label="Modules">';
foreach($parsed_docs as $k => $v) {
	
	if(substr($parsed_docs[$k]['filepath'], 0, 11) != '../modules/') {
		continue;
	}
	
	$sel = '';
	if($_SESSION['docs_file'] == $k) {
		$sel = 'selected';
	}
	
	if($parsed_docs[$k]['header']['navigation'] != '') {
		$show_title = $parsed_docs[$k]['header']['navigation'];
	} else {
		$show_title = $parsed_docs[$k]['filename'];
	}
	
	$path = explode('/', $parsed_docs[$k]['filepath']);	
	echo '<option value="'.$k.'" '.$sel.'>'.$path[2].' > '.$show_title.'</option>';
}
echo '</optgroup>';

echo '<optgroup label="Themes">';
foreach($parsed_docs as $k => $v) {
	
	if(substr($parsed_docs[$k]['filepath'], 0, 10) != '../styles/') {
		continue;
	}
	
	$sel = '';
	if($_SESSION['docs_file'] == $k) {
		$sel = 'selected';
	}
	
	if($parsed_docs[$k]['header']['navigation'] != '') {
		$show_title = $parsed_docs[$k]['header']['navigation'];
	} else {
		$show_title = $parsed_docs[$k]['filename'];
	}
	
	$path = explode('/', $parsed_docs[$k]['filepath']);	
	echo '<option value="'.$k.'" '.$sel.'>'.$path[2].' > '.$show_title.'</option>';
}
echo '</optgroup>';

echo '</select>';

echo '</div>';
echo '<div class="col-md-3">';
echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '<button type="submit" name="s" class="btn btn-fc w-100">'.$icon['sync_alt'].'</button>';
echo '</div>';
echo '</div>';

echo '</form>';


if(isset($_SESSION['docs_file'])) {
	echo '<hr>'.$parsed_docs[$_SESSION['docs_file']]['content'];
}




$helpURL = 'https://flatcore.org/documentation/';

if($languagePack == 'de') {
	$helpURL = 'https://flatcore.org/de/dokumentation/';	
}

echo '<hr><a target="_blank" href="'.$helpURL.'" title="'.$helpURL.'" class="btn btn-fc w-100">'.$icon['question'].' '.$lang['show_help'].'</a>';
echo '<a target="_blank" href="https://github.com/flatCore/flatCore-CMS/discussions" title="Discussions" class="btn btn-primary w-100">'.$icon['comments'].' Discussions</a>';



function fc_parse_doc_md_file($path) {
	
	global $Parsedown;
	
	if(is_file($path)) {
		$src = file_get_contents($path);
		$src_content = explode('---',$src);
		$header_length = strlen($src_content[1])+6;
		$content = substr($src, $header_length);
		$parsed_header = Spyc::YAMLLoadString($src_content[1]);
		$parsed_content = $Parsedown->text($content);
		$filemtime = filemtime($path);
	} else {
		$parsed = 'FILE NOT FOUND';
	}
	
	$doc['header'] = $parsed_header;
	$doc['content'] = $parsed_content;
	$doc['filename'] = basename($path);
	$doc['filepath'] = $path;
	
	return $doc;
}


?>
