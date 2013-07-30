<?php

//prohibit unauthorized access
require("core/access.php");


$sub_active[0] = "submenu";
$sub_active[1] = "submenu";
$sub_active[2] = "submenu";
$sub_active[3] = "submenu";
$sub_active[4] = "submenu";
$sub_active[5] = "submenu";

switch ($sub) {

case "list":
	$subinc = "pages.list";
	$sub_active[0] = "submenu_selected";
	break;
	
case "edit":
	$subinc = "pages.edit";
	$sub_active[1] = "submenu_selected";
	break;
	
case "new":
	$subinc = "pages.edit";
	$sub_active[2] = "submenu_selected";
	break;
	
case "customize":
	$subinc = "pages.customize";
	$sub_active[3] = "submenu_selected";
	break;

case "snippets":
	$subinc = "pages.snippets";
	$sub_active[4] = "submenu_selected";
	break;
		
case "rss":
	$subinc = "pages.edit_rss";
	$sub_active[5] = "submenu_selected";
	break;
	
default:
	$subinc = "pages.list";
	$sub_active[0] = "submenu_selected";
	break;

}


if($_SESSION[acp_pages] != "allowed" AND $subinc == "pages.edit" AND $sub == "new"){
	$subinc = "no_access";
}

if($_SESSION[acp_system] != "allowed" AND $subinc == "pages.customize"){
	$subinc = "no_access";
}



/**
 * get installed languages
 * example: $arr_lang[lang_sign] => de | $arr_lang[lang_desc] => Deutsch 
 */

$arr_lang = get_all_languages();

/* default: check all languages */
if(!isset($_SESSION[checked_lang_string])) {	
	foreach($arr_lang as $langstring) {
		$checked_lang_string .= "$langstring[lang_folder]-";
	}
	$_SESSION[checked_lang_string] = "$checked_lang_string";
}



/* change status of $_GET[switchLang] */
if($_GET['switchLang']) {
		if(strpos("$_SESSION[checked_lang_string]", "$_GET[switchLang]") !== false) {
			$checked_lang_string = str_replace("$_GET[switchLang]-", '', $_SESSION[checked_lang_string]);
		} else {
			$checked_lang_string = $_SESSION[checked_lang_string] . "$_GET[switchLang]-";
		}
		$_SESSION[checked_lang_string] = "$checked_lang_string";
}

/* build SQL query */
$set_lang_filter = "page_language = 'foobar' OR "; // reset -> result = 0
for($i=0;$i<count($arr_lang);$i++) {
	$lang_folder = $arr_lang[$i][lang_folder];
	if(strpos("$_SESSION[checked_lang_string]", "$lang_folder") !== false) {
		$set_lang_filter .= "page_language = '$lang_folder' OR ";
	}
}




if($_GET['switch']) {
	$_SESSION[set_status] = true;
}

if($_SESSION[checked_draft] == '' AND $_SESSION[checked_private] == '' AND $_SESSION[checked_public] == '' AND $_SESSION[set_status] == false) {
	$_SESSION[checked_public] = 'checked';
}


if($_GET['switch'] == 'statusDraft' AND $_SESSION[checked_draft] == '') {
	$_SESSION[checked_draft] = "checked";
} elseif($_GET['switch'] == 'statusDraft' AND $_SESSION[checked_draft] == 'checked') {
	$_SESSION[checked_draft] = "";
}

if($_GET['switch'] == 'statusPrivate' && $_SESSION[checked_private] == 'checked') {
	$_SESSION[checked_private] = "";
} elseif($_GET['switch'] == 'statusPrivate' && $_SESSION[checked_private] == '') {
	$_SESSION[checked_private] = "checked";
}

if($_GET['switch'] == 'statusPuplic' && $_SESSION[checked_public] == 'checked') {
	$_SESSION[checked_public] = "";
} elseif($_GET['switch'] == 'statusPuplic' && $_SESSION[checked_public] == '') {
	$_SESSION[checked_public] = "checked";
}


$set_status_filter = "page_status = 'foobar' "; // reset -> result = 0


if($_SESSION[checked_draft] == "checked") {
	$set_status_filter .= "OR page_status = 'draft' ";
	$btn_status_draft = 'btn-primary';
}

if($_SESSION[checked_private] == "checked") {
	$set_status_filter .= "OR page_status = 'private' ";
	$btn_status_private = 'btn-primary';
}

if($_SESSION[checked_public] == "checked") {
	$set_status_filter .= "OR page_status = 'public' ";
	$btn_status_public = 'btn-primary';
}


$set_lang_filter = substr("$set_lang_filter", 0, -3); // cut the last ' OR'


$filter_string = "WHERE page_status != 'foobar' "; // reset -> result = 0

if($set_status_filter != "") {
	$filter_string .= " AND ($set_status_filter) ";
}

if($set_lang_filter != "") {
	$filter_string .= " AND ($set_lang_filter)";
}


$_SESSION['filter_string'] = $filter_string;


if($subinc == "pages.list") {

	/* Filter Languages */
	$lang_btn_group = '<div class="btn-group">';
	for($i=0;$i<count($arr_lang);$i++) {
		$lang_desc = $arr_lang[$i][lang_desc];
		$lang_folder = $arr_lang[$i][lang_folder];
		
		$this_btn_status = '';
		if(strpos("$_SESSION[checked_lang_string]", "$lang_folder") !== false) {
			$this_btn_status = 'btn-primary';
		}
		
		$lang_btn_group .= '<a href="acp.php?tn=pages&sub=list&switchLang='.$lang_folder.'" class="btn btn-small '.$this_btn_status.'">'.$lang_folder.'</a>';
	
	} // eo $i
	
	$lang_btn_group .= '</div>';
	
	$status_btn_group  = '<div class="btn-group">';
	$status_btn_group .= '<a href="acp.php?tn=pages&sub=list&switch=statusPuplic" class="btn btn-small '.$btn_status_public.'">'.$lang[f_page_status_puplic].'</a>';
	$status_btn_group .= '<a href="acp.php?tn=pages&sub=list&switch=statusPrivate" class="btn btn-small '.$btn_status_private.'">'.$lang[f_page_status_private].'</a>';
	$status_btn_group .= '<a href="acp.php?tn=pages&sub=list&switch=statusDraft" class="btn btn-small '.$btn_status_draft.'">'.$lang[f_page_status_draft].'</a>';
	$status_btn_group .= '</div>';

}







echo '<div id="wrapper">';

// content block
echo '<div id="contentbox">';
include("$subinc.php");
echo '</div>'; // eol div contenbox

echo '</div>'; // eol div wrapper



echo '<div id="subnav">';

// sub navigation
echo '<div id="subnav-inner">';

echo "<a class='$sub_active[0]' href='$_SERVER[PHP_SELF]?tn=pages&sub=list'>$lang[page_list]</a>";
echo "<a class='$sub_active[2]' href='$_SERVER[PHP_SELF]?tn=pages&sub=new'>$lang[new_page]</a>";

if($sub == "edit") {
	echo "<a class='$sub_active[1]' href='$_SERVER[PHP_SELF]?tn=pages&sub=list'>$lang[page_edit]</a>";
} else {
	echo "<span class='submenu_disabled'>$lang[page_edit]</span>";
}

echo "<a class='$sub_active[3]' href='$_SERVER[PHP_SELF]?tn=pages&sub=customize'>$lang[page_customize]</a>";
echo "<a class='$sub_active[4]' href='$_SERVER[PHP_SELF]?tn=pages&sub=snippets'>$lang[snippets]</a>";
echo "<a class='$sub_active[5]' href='$_SERVER[PHP_SELF]?tn=pages&sub=rss'>RSS</a>";



echo '</div>'; // sub navigation EOL


// liveBox
include("livebox.php");

echo '</div>';
?>