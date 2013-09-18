<?php

//prohibit unauthorized access
require("core/access.php");

$btn_status_puplic ="<button class='btn btn-success btn-mini disabled' title='$lang[f_page_status_puplic]'><i class='icon-globe icon-white'></i></button>";
$btn_status_private ="<button class='btn btn-danger btn-mini disabled' title='$lang[f_page_status_private]'><i class='icon-lock icon-white'></i></button>";
$btn_status_draft ="<button class='btn btn-mini disabled' title='$lang[f_page_status_draft]'><i class='icon-pencil'></i></button>";

$dbh = new PDO("sqlite:".CONTENT_DB);

unset($result);
/* $_SESSION[filter_string] was defined in inc.pages.php */
$sql = "SELECT page_id,	page_language, page_linkname, page_title, page_sort, page_lastedit,	page_lastedit_from, page_status, page_template,	page_authorized_users, page_permalink
		FROM fc_pages
		$_SESSION[filter_string]
		ORDER BY page_language ASC, page_sort ASC";

	foreach ($dbh->query($sql) as $row) {
		$result[] = $row;
	}
	
$dbh = null;
   
$cnt_result = count($result);


echo '<div class="row-fluid">';
echo '<div class="span5">';
echo $kw_form;
echo '<p style="padding:0;">' . $btn_remove_keyword . '</p>';
echo '</div>';
echo '<div class="span7">';
echo '<div style="float:right;">';
echo $status_btn_group . ' ' . $lang_btn_group;
echo '</div>';
echo '<div class="clearfix"></div>';
echo '</div>';
echo '</div>';

/**
 * list all pages where page_sort != empty
 */

echo '<fieldset>';
echo '<legend>' . $lang['legend_structured_pages'] . '</legend>';

echo '<table class="table-list table" border="0" cellpadding="0" cellspacing="0">';
echo"<tr>
<td class='head'>$lang[h_page_sort]</td>
<td class='head'>$lang[h_page_linkname]</td>
<td class='head'>$lang[h_page_title]</td>
<td class='head'>Hits</td>
<td class='head' width='100'>$lang[h_action]</td>
</tr>";

for($i=0;$i<$cnt_result;$i++) {

	if($result[$i]['page_sort'] == "" || $result[$i]['page_sort'] == 'portal') {
		continue;
	}

	$page_id = $result[$i]['page_id'];
	$page_sort = $result[$i]['page_sort'];
	$page_linkname = stripslashes($result[$i]['page_linkname']);
	$page_title = stripslashes($result[$i]['page_title']);
	$page_status = $result[$i]['page_status'];
	$page_lastedit = $result[$i]['page_lastedit'];
	$page_lastedit_from = $result[$i]['page_lastedit_from'];
	$page_template = $result[$i]['page_template'];
	$page_authorized_users = $result[$i]['page_authorized_users'];
	$page_language = $result[$i]['page_language'];
	$page_permalink = $result[$i]['page_permalink'];
	
	if($page_template == "use_standard") {
		$show_template_name =  "$lang[use_standard]";
	} else {
		$show_template_name = "$page_template";
	}

	$pi = get_page_impression($page_id);
	
	if($page_status == "public") {
		$status = "$btn_status_puplic";
	} elseif($page_status == "private") {
		$status = "$btn_status_private";
	} elseif($page_status == "draft") {
		$status = "$btn_status_draft";
	}
	
	$last_edit = date("d.m.Y H:i:s",$page_lastedit) . " ($page_lastedit_from)";
	
	/* check for display edit button */
	
	if($_SESSION['acp_editpages'] == "allowed"){
		$edit_button = "<a class='btn btn-mini' href='$_SERVER[PHP_SELF]?tn=pages&sub=edit&editpage=$page_id'>$lang[edit]</a>";
	} else {
		$edit_button = "<br>";
	}
	
	$arr_checked_admins = explode(",",$page_authorized_users);
	if(in_array("$_SESSION[user_nick]", $arr_checked_admins)) {
		$edit_button = "<a class='btn btn-small' href='$_SERVER[PHP_SELF]?tn=pages&sub=edit&editpage=$page_id'>$lang[edit]</a>";
	}
	
	/* mark main and subpages | or not */
	$subpage_marker = '';
	$td_class = '';
	if(strpos($page_sort, '.') !== false) {
		$subpage_marker = '&raquo;';
		$td_class = "subpage";
	} else {
		$td_class = "mainpage";
	}
	
	if($fc_mod_rewrite == "permalink") {
		$frontend_link = "../$page_permalink";
	} else {
		$frontend_link = "../index.php?p=$page_id";
	}
	
	echo"<tr>
			<td class='$td_class'><p class='extrainfo condensed'>$subpage_marker $page_sort</p></td>
			<td class='$td_class'><a class='darklink tooltip_bottom' data-toggle='tooltip' title='$frontend_link' href='$frontend_link'>$page_linkname</a></td>
			<td class='$td_class'>$page_title<p class='extrainfo condensed'>$last_edit | Style: $show_template_name | $page_language</p></td>
			<td class='$td_class' style='text-align:right;'>$pi</td>
			<td  class='$td_class'><div class='btn-group'>$status $edit_button</div></td>
		</tr>";

} // eol for $i

echo"</table>";
echo"</fieldset>";





/**
 * list all pages where
 * page_sort == empty
 * or page_sort == portal
 */

echo '<fieldset>';
echo '<legend>' . $lang['legend_unstructured_pages'] . '</legend>';

echo '<table class="table-list table" border="0" cellpadding="0" cellspacing="0">';
echo"<tr>
<td class='head'>$lang[h_page_linkname]</td>
<td class='head'>$lang[h_page_title]</td>
<td class='head'>Hits</td>
<td class='head' width='100'>$lang[h_action]</td>
</tr>";

for($i=0;$i<$cnt_result;$i++) {

	if($result[$i]['page_sort'] != "" && $result[$i]['page_sort'] != 'portal') {
		continue;
	}

	$page_id = $result[$i]['page_id'];
	$page_sort = $result[$i]['page_sort'];
	$page_linkname = stripslashes($result[$i]['page_linkname']);
	$page_title = stripslashes($result[$i]['page_title']);
	$page_status = $result[$i]['page_status'];
	$page_lastedit = $result[$i]['page_lastedit'];
	$page_lastedit_from = $result[$i]['page_lastedit_from'];
	$page_template = $result[$i]['page_template'];
	$page_authorized_users = $result[$i]['page_authorized_users'];
	$page_language = $result[$i]['page_language'];
	$page_permalink = $result[$i]['page_permalink'];
	
	if($page_template == "use_standard") {
		$show_template_name =  "$lang[use_standard]";
	} else {
		$show_template_name = "$page_template";
	}
	
	if($page_sort == 'portal') {
		$page_linkname = '<i class="icon-home"></i> ' . $page_linkname;
	}
	
	$hits_id = $page_id;	
	if($page_sort == "portal") {
		$hits_id = "portal_$page_language";
	}
	
	$pi = get_page_impression($hits_id);
	
	if($page_status == "public") {
		$status = "$btn_status_puplic";
	} elseif($page_status == "private") {
		$status = "$btn_status_private";
	} elseif($page_status == "draft") {
		$status = "$btn_status_draft";
	}
	
	$last_edit = date("d.m.Y H:i:s",$page_lastedit) . "($page_lastedit_from)";
	
	/* check for display edit button */
	
	if($_SESSION['acp_editpages'] == "allowed"){
		$edit_button = "<a class='btn btn-mini' href='$_SERVER[PHP_SELF]?tn=pages&sub=edit&editpage=$page_id'>$lang[edit]</a>";
	} else {
		$edit_button = "<br />";
	}
	
	$arr_checked_admins = explode(",",$page_authorized_users);
	if(in_array("$_SESSION[user_nick]", $arr_checked_admins)) {
		$edit_button = "<a class='btn btn-mini' href='$_SERVER[PHP_SELF]?tn=pages&sub=edit&editpage=$page_id'>$lang[edit]</a>";
	}
	
	if($fc_mod_rewrite == "permalink") {
		$frontend_link = "../$page_permalink";
	} else {
		$frontend_link = "../index.php?p=$page_id";
	}
	
	echo"<tr>
			<td><a class='darklink tooltip_bottom' data-toggle='tooltip' title='$frontend_link' href='$frontend_link'>$page_linkname</a></td>
			<td><span class='bold'>$page_title</span><p class='extrainfo condensed'>$last_edit | Style: $show_template_name | $lang[f_page_language]: $page_language</p></td>
			<td style='text-align:right;'>$pi</td>
			<td><div class='btn-group'>$status $edit_button</div></td>
		</tr>";

} // eol for $i

echo"</table>";
echo"</fieldset>";

?>
