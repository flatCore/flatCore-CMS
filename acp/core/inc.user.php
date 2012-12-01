<?php
//prohibit unauthorized access
require("core/access.php");



$sub_active[0] = "submenu";
$sub_active[1] = "submenu";
$sub_active[2] = "submenu";
$sub_active[3] = "submenu";

switch ($sub) {

case "list":
	$subinc = "user.list";
	$sub_active[0] = "submenu_selected";
	break;
	
case "edit":
	$subinc = "user.edit";
	$sub_active[1] = "submenu_selected";
	break;
	
case "new":
	$subinc = "user.edit";
	$sub_active[2] = "submenu_selected";
	break;
	
case "groups":
	$subinc = "user.groups";
	$sub_active[3] = "submenu_selected";
	break;
	
default:
	$subinc = "user.list";
	$sub_active[0] = "submenu_selected";
	break;

}



if($_SESSION[acp_user] != "allowed" AND $subinc == "user.edit"){
$subinc = "no_access";
}

if($_SESSION[acp_user] != "allowed" AND $subinc == "user.groups"){
$subinc = "no_access";
}



/*
Output
*/







echo"<div id='wrapper'> ";

echo"<div id='contentbox'>";

include("./core/$subinc.php");

echo"</div>"; // eol div contenbox

echo"</div>"; // eol div wrapper






// sub navigation
echo"<div id='subnav'>";
echo"<div id='subnav-inner'>";

echo"<a class='$sub_active[0]' href='$_SERVER[PHP_SELF]?tn=user&sub=list'>$lang[list_user]</a>";
echo"<a class='$sub_active[2]' href='$_SERVER[PHP_SELF]?tn=user&sub=new'>$lang[new_user]</a>";

if($sub == "edit") {
	echo"<a class='$sub_active[1]' href='$_SERVER[PHP_SELF]?tn=user&sub=list'>$lang[edit_user]</a>";
} else {
	echo"<span class='submenu_disabled'>$lang[edit_user]</span>";
}

echo"<a class='$sub_active[3]' href='$_SERVER[PHP_SELF]?tn=user&sub=groups'>$lang[edit_groups]</a>";


echo"<h5>$lang[h_search_user]</h5>";
echo"<div style='padding: 0 4px;width:180px;'>
<form action='$_SERVER[PHP_SELF]?tn=user' method='POST'>";
echo"<input type='text' name='findUser' class='input100'> ";
echo"<input type='submit' class='btn-green' value='$lang[submit]'>";
echo"</form></div>";



echo"</div>"; // sub navigation EOL



// liveBox
include("livebox.php");
echo"</div>";

?>