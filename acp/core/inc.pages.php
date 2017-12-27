<?php

//prohibit unauthorized access
require 'core/access.php';


switch ($sub) {

case "list":
	$subinc = "pages.list";
	break;
	
case "edit":
	$subinc = "pages.edit";
	break;
	
case "new":
	$subinc = "pages.edit";
	break;
	
case "customize":
	$subinc = "pages.customize";
	break;

case "snippets":
	$subinc = "pages.snippets";
	break;
		
case "rss":
	$subinc = "pages.edit_rss";
	break;
	
default:
	$subinc = "pages.list";
	break;

}


if($_SESSION['acp_pages'] != "allowed" AND $subinc == "pages.edit" AND $sub == "new"){
	$subinc = "no_access";
}

if($_SESSION['acp_system'] != "allowed" AND $subinc == "pages.customize"){
	$subinc = "no_access";
}



/**
 * get installed languages
 * example: $arr_lang[lang_sign] => de | $arr_lang[lang_desc] => Deutsch 
 */

$arr_lang = get_all_languages();
$fc_labels = fc_get_labels();

/* default: check default language */
if(!isset($_SESSION['checked_lang_string'])) {	
	$_SESSION['checked_lang_string'] = $languagePack .'-';
}

/* change status of $_GET[switchLang] */
if(isset($_GET['switchLang'])) {
		if(strpos("$_SESSION[checked_lang_string]", "$_GET[switchLang]") !== false) {
			$checked_lang_string = str_replace("$_GET[switchLang]-", '', $_SESSION['checked_lang_string']);
		} else {
			$checked_lang_string = $_SESSION['checked_lang_string'] . "$_GET[switchLang]-";
		}
		$_SESSION['checked_lang_string'] = "$checked_lang_string";
}

/* build SQL query */
$set_lang_filter = "";
for($i=0;$i<count($arr_lang);$i++) {
	$lang_folder = $arr_lang[$i]['lang_folder'];
	if(strpos("$_SESSION[checked_lang_string]", "$lang_folder") !== false) {
		$set_lang_filter .= "page_language = '$lang_folder' OR ";
	}
}

$set_lang_filter = substr("$set_lang_filter", 0, -3); // cut the last ' OR'


/* labels */
if(!isset($_SESSION['checked_label_str'])) {
	$_SESSION['checked_label_str'] = '';
}

if(isset($_POST['check_label'])) {
		if(strpos("$_SESSION[checked_label_str]", "$_POST[check_label]") !== false) {
			$checked_label_string = str_replace("$_POST[check_label]-", '', $_SESSION['checked_label_str']);
		} else {
			$checked_label_string = $_SESSION['checked_label_str'] . "$_POST[check_label]-";
		}
		$_SESSION['checked_label_str'] = "$checked_label_string";
}

/* build SQL query for labels */

$set_label_filter = '';

for($i=0;$i<count($fc_labels);$i++) {
	$label = $fc_labels[$i]['label_id'];
	if(strpos("$_SESSION[checked_label_str]", "$label") !== false) {
		$set_label_filter .= "page_labels = '$label' OR ";
	}
}

$set_label_filter = substr("$set_label_filter", 0, -3); // cut the last ' OR'


/* switch page status */

if(isset($_GET['switch'])) {
	$_SESSION['set_status'] = true;
}

if($_SESSION['checked_draft'] == '' AND $_SESSION['checked_private'] == '' AND $_SESSION['checked_public'] == '' AND $_SESSION['checked_ghost'] == '' AND $_SESSION['set_status'] == false) {
	$_SESSION['checked_public'] = 'checked';
}


if($_GET['switch'] == 'statusDraft' AND $_SESSION['checked_draft'] == '') {
	$_SESSION['checked_draft'] = "checked";
} elseif($_GET['switch'] == 'statusDraft' AND $_SESSION['checked_draft'] == 'checked') {
	$_SESSION['checked_draft'] = "";
}

if($_GET['switch'] == 'statusPrivate' && $_SESSION['checked_private'] == 'checked') {
	$_SESSION['checked_private'] = "";
} elseif($_GET['switch'] == 'statusPrivate' && $_SESSION['checked_private'] == '') {
	$_SESSION['checked_private'] = "checked";
}

if($_GET['switch'] == 'statusPuplic' && $_SESSION['checked_public'] == 'checked') {
	$_SESSION['checked_public'] = "";
} elseif($_GET['switch'] == 'statusPuplic' && $_SESSION['checked_public'] == '') {
	$_SESSION['checked_public'] = "checked";
}

if($_GET['switch'] == 'statusRedirect' && $_SESSION['checked_redirect'] == 'checked') {
	$_SESSION['checked_redirect'] = "";
} elseif($_GET['switch'] == 'statusRedirect' && $_SESSION['checked_redirect'] == '') {
	$_SESSION['checked_redirect'] = "checked";
}

if($_GET['switch'] == 'statusGhost' && $_SESSION['checked_ghost'] == 'checked') {
	$_SESSION['checked_ghost'] = "";
} elseif($_GET['switch'] == 'statusGhost' && $_SESSION['checked_ghost'] == '') {
	$_SESSION['checked_ghost'] = "checked";
}

$set_status_filter = "page_status = 'foobar' "; // reset -> result = 0


if($_SESSION['checked_draft'] == "checked") {
	$set_status_filter .= "OR page_status = 'draft' ";
	$btn_status_draft = 'active';
}

if($_SESSION['checked_private'] == "checked") {
	$set_status_filter .= "OR page_status = 'private' ";
	$btn_status_private = 'active';
}

if($_SESSION['checked_public'] == "checked") {
	$set_status_filter .= "OR page_status = 'public' ";
	$btn_status_public = 'active';
}

if($_SESSION['checked_ghost'] == "checked") {
	$set_status_filter .= "OR page_status = 'ghost' ";
	$btn_status_ghost = 'active';
}

if($_SESSION['checked_redirect'] == "checked") {
	$btn_status_redirect = 'active';
}


/* filter pages by keywords $kw_filter */

/* expand filter */
if(isset($_POST['kw_filter'])) {
	$_SESSION['kw_filter'] = $_SESSION['kw_filter'] . ' ' . $_POST['kw_filter'];
}

$set_keyword_filter = "page_language = 'foobar' OR "; // reset -> result = 0
$set_keyword_filter = '';

/* remove keyword from filter list */
if(isset($_REQUEST['rm_keyword'])) {
	$all_filter = explode(" ", $_SESSION['kw_filter']);
	unset($_SESSION['kw_filter'],$f);
	foreach($all_filter as $f) {
		if($_REQUEST['rm_keyword'] == "$f") { continue; }
		if($f == "") { continue; }
		$_SESSION['kw_filter'] .= "$f ";
	}
}

if($_SESSION['kw_filter'] != "") {
	unset($all_filter);
	$all_filter = explode(" ", $_SESSION['kw_filter']);
	
	foreach($all_filter as $f) {
		if($_REQUEST['rm_keyword'] == "$f") { continue; }
		if($f == "") { continue; }
		$set_keyword_filter .= "(page_meta_keywords like '%$f%' OR page_title like '%$f%' OR page_linkname like '%$f%') AND";
		$btn_remove_keyword .= "<a class='btn btn-xs btn-fc' href='acp.php?tn=pages&sub=list&rm_keyword=$f'><span class='glyphicon glyphicon-remove'></span> $f</a> ";
	}
	
}

$set_keyword_filter = substr("$set_keyword_filter", 0, -4); // cut the last ' AND'





$filter_string = "WHERE page_status != 'foobar' "; // -> result = match all pages

if($set_status_filter != "") {
	$filter_string .= " AND ($set_status_filter) ";
}

if($set_lang_filter != "") {
	$filter_string .= " AND ($set_lang_filter)";
}

if($set_label_filter != "") {
	$filter_string .= " AND ($set_label_filter)";
}

if($set_keyword_filter != "") {
	$filter_string .= " AND $set_keyword_filter";
}


$_SESSION['filter_string'] = $filter_string;


if($subinc == "pages.list") {
	
	
	$label_btn  = '<form action="acp.php?tn=pages&sub=list" method="POST" class="form-horizontal">';
	$this_btn_status = '';
	foreach($fc_labels as $label) {
		
		if(strpos("$_SESSION[checked_label_str]", $label['label_id']) !== false) {
			$this_btn_status = 'btn-label-dot active';
		} else {
			$this_btn_status = 'btn-label-dot';
		}		
			
		$label_btn .= '<button name="check_label" value="'.$label['label_id'].'" class="'.$this_btn_status.'">';
		$label_btn .= '<span class="label-dot" style="background-color:'.$label['label_color'].';" title="'.$label['label_title'].'"></span>';
		$label_btn .= '</button>';
		
	}
	$label_btn .= '</form>';
	

	/* Filter Languages */
	$lang_btn_group = '<div class="btn-group">';

	for($i=0;$i<count($arr_lang);$i++) {
		$lang_desc = $arr_lang[$i]['lang_desc'];
		$lang_folder = $arr_lang[$i]['lang_folder'];
		
		$this_btn_status = '';
		if(strpos("$_SESSION[checked_lang_string]", "$lang_folder") !== false) {
			$this_btn_status = 'btn btn-default active btn-sm';
		} else {
			$this_btn_status = 'btn btn-default btn-sm';
		}
		
		$lang_btn_group .= '<a href="acp.php?tn=pages&sub=list&switchLang='.$lang_folder.'" class="'.$this_btn_status.'">'.$lang_folder.'</a>';
	}
	
	$lang_btn_group .= '</div>';
	
	
	
	$status_btn_group  = '<div class="btn-group">';
	$status_btn_group .= '<a href="acp.php?tn=pages&sub=list&switch=statusPuplic" class="btn btn-default btn-sm btn-public '.$btn_status_public.'">'.$lang['f_page_status_puplic'].'</a>';
	$status_btn_group .= '<a href="acp.php?tn=pages&sub=list&switch=statusGhost" class="btn btn-default btn-sm btn-ghost '.$btn_status_ghost.'">'.$lang['f_page_status_ghost'].'</a>';
	$status_btn_group .= '<a href="acp.php?tn=pages&sub=list&switch=statusPrivate" class="btn btn-default btn-sm btn-private '.$btn_status_private.'">'.$lang['f_page_status_private'].'</a>';
	$status_btn_group .= '<a href="acp.php?tn=pages&sub=list&switch=statusDraft" class="btn btn-default btn-sm btn-draft '.$btn_status_draft.'">'.$lang['f_page_status_draft'].'</a>';
	$status_btn_group .= '</div> ';
	
	$status_btn_group .= '<div class="btn-group">';
	$status_btn_group .= '<a href="acp.php?tn=pages&sub=list&switch=statusRedirect" class="btn btn-default btn-redirect btn-sm '.$btn_status_redirect.'">'.$lang['btn_redirect'].'</a>';
	$status_btn_group .= '</div>';

	$kw_form  = '<form action="acp.php?tn=pages&sub=list" method="POST" class="form-horizontal">';
	$kw_form .= '<div class="input-group">';
	$kw_form .= '<span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>';
	$kw_form .= '<input class="form-control" type="text" name="kw_filter" value="" placeholder="Filter">';
	$kw_form .= '</div>';
	$kw_form .= '</form>';

}


include $subinc.'.php';

?>