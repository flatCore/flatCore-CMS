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


/*
get installed languages
example: $arr_lang[lang_sign] => de | $arr_lang[lang_desc] => Deutsch 
*/

$arr_lang = get_all_languages();

/* default: check all languages */
if($_SESSION[checked_lang_string] == "") {	
	foreach($arr_lang as $langstring) {
		$checked_lang_string .= "$langstring[lang_folder]-";
	}
	
	$_SESSION[checked_lang_string] = "$checked_lang_string";
}


/* default: check all pages */
if(!$_SESSION[set_filter]) {
	$_SESSION[checked_public] = "checked";
	$_SESSION[checked_private] = "checked";
	$_SESSION[checked_draft] = "checked";
}

/* set new filter */
if($_POST[set_filter]) {

	$_SESSION[set_filter] = true;

	unset($_SESSION[filter_string]);
	$set_status_filter = "page_status = 'foobar' "; // reset -> result = 0

	if($_POST[filter_public] != "" ) {
		$set_status_filter .= "OR page_status = 'public' ";
		$_SESSION[checked_public] = "checked";
	} else {
		$_SESSION[checked_public] = "";
	}
	if($_POST[filter_private] != "" ) {
		$set_status_filter .= "OR page_status = 'private' ";
		$_SESSION[checked_private] = "checked";
	} else {
		$_SESSION[checked_private] = "";
	}
	if($_POST[filter_draft] != "" ) {
		$set_status_filter .= "OR page_status = 'draft' ";
		$_SESSION[checked_draft] = "checked";
	} else {
		$_SESSION[checked_draft] = "";
	}


	if($_POST[filter_lang]) {
		$checked_lang_string = "";
		$set_lang_filter = "page_language = 'foobar' OR ";
		
		for($i=0;$i<count($arr_lang);$i++) {
		
			$lang_folder = $arr_lang[$i][lang_folder];
			if(in_array("$lang_folder",$_POST[filter_lang])) {
				$set_lang_filter .= "page_language = '$lang_folder' OR ";
				$checked_lang_string .= "$lang_folder-";	
			}
		
		} // eo $i
		
		$_SESSION[checked_lang_string] = "$checked_lang_string";
	}



} // eo $_POST[set_filter]


$set_lang_filter = substr("$set_lang_filter", 0, -3); // cut the last ' OR'


/* we start the filter - foobar can't exists -> matching all sites */
$filter_string = "WHERE page_status != 'foobar' ";

if($set_status_filter != "") {
	$filter_string .= " AND ($set_status_filter) ";
}

if($set_lang_filter != "") {
	$filter_string .= " AND ($set_lang_filter)";
}


if($_SESSION[filter_string] == "") {
	$_SESSION['filter_string'] = $filter_string;
}

// output


echo"<div id='wrapper'> ";

// content block
echo"<div id='contentbox'>";


include("$subinc.php");

echo"</div>"; // eol div contenbox

echo"</div>"; // eol div wrapper



echo"<div id='subnav'>";

// sub navigation
echo"<div id='subnav-inner'>";

echo"<a class='$sub_active[0]' href='$_SERVER[PHP_SELF]?tn=pages&sub=list'>$lang[page_list]</a>";
echo"<a class='$sub_active[2]' href='$_SERVER[PHP_SELF]?tn=pages&sub=new'>$lang[new_page]</a>";

if($sub == "edit") {
	echo"<a class='$sub_active[1]' href='$_SERVER[PHP_SELF]?tn=pages&sub=list'>$lang[page_edit]</a>";
} else {
	echo"<span class='submenu_disabled'>$lang[page_edit]</span>";
}

echo"<a class='$sub_active[3]' href='$_SERVER[PHP_SELF]?tn=pages&sub=customize'>$lang[page_customize]</a>";
echo"<a class='$sub_active[4]' href='$_SERVER[PHP_SELF]?tn=pages&sub=snippets'>$lang[snippets]</a>";
echo"<a class='$sub_active[5]' href='$_SERVER[PHP_SELF]?tn=pages&sub=rss'>RSS</a>";


if($subinc == "pages.list") {


echo"<h5>Filter</h5>\r";
echo"<form style='padding:8px;' action='$_SERVER[PHP_SELF]?tn=pages&sub=list' method='POST'>";

echo"<input type='checkbox' $_SESSION[checked_public] name='filter_public'> $lang[f_page_status_puplic]<br />";
echo"<input type='checkbox' $_SESSION[checked_private] name='filter_private'> $lang[f_page_status_private]<br />";
echo"<input type='checkbox' $_SESSION[checked_draft] name='filter_draft'> $lang[f_page_status_draft]<br />";

echo"<hr class='spacer'>\n";


/* Filter Languages */

for($i=0;$i<count($arr_lang);$i++) {
	$lang_desc = $arr_lang[$i][lang_desc];
	$lang_folder = $arr_lang[$i][lang_folder];
	
	if (strpos("$_SESSION[checked_lang_string]", "$lang_folder") !== false) {
		$checked_lang = "checked";
	} else {
		$checked_lang = "";
	}
	
	echo"<input type='checkbox' $checked_lang name='filter_lang[]' value='$lang_folder'> $lang_folder<br />\n";

} // eo $i

echo"<hr class='spacer'>\n";

echo"<input type='submit' class='btn btn-success btn-small' name='set_filter' value='$lang[display]'><br />";


echo"</form>";


}



echo"</div>"; // sub navigation EOL


// liveBox
include("livebox.php");

echo"</div>";
?>