<?php

//prohibit unauthorized access
require("core/access.php");

$dbh = new PDO("sqlite:".CONTENT_DB);

unset($result);
/* $_SESSION[filter_string] was defined in inc.pages.php */
$sql = "SELECT page_id,	page_language, page_linkname, page_title, page_sort, page_lastedit,	page_lastedit_from, page_status, page_template,	page_modul, page_authorized_users, page_permalink, page_redirect, page_redirect_code
		FROM fc_pages
		$_SESSION[filter_string]
		ORDER BY page_language ASC, page_sort ASC, page_linkname ASC";

foreach ($dbh->query($sql) as $row) {
	$result[] = $row;
}
	
$dbh = null;
   
$cnt_result = count($result);


echo '<div class="row">';
echo '<div class="col-md-5">';
echo $kw_form;
echo '<p style="padding:0;">' . $btn_remove_keyword . '</p>';
echo '</div>';
echo '<div class="col-md-7">';
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

echo '<table class="table-list table-pages table" border="0" cellpadding="0" cellspacing="0">';

echo '<thead>';
echo '<tr>';
echo '<th>'.$lang['h_page_sort'].'</th>';
echo '<th>'.$lang['h_page_linkname'].'</th>';
echo '<th>'.$lang['h_page_title'].'</th>';
echo '<th class="text-right">'.$lang['h_page_hits'].'</th>';
echo '<th style="width:120px;">'.$lang['h_action'].'</th>';
echo '</tr>';
echo '</thead>';

for($i=0;$i<$cnt_result;$i++) {

	if($result[$i]['page_sort'] == "" || $result[$i]['page_sort'] == 'portal') {
		continue;
	}
	
	unset($show_redirect);

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
	$page_redirect = $result[$i]['page_redirect'];
	$page_redirect_code = $result[$i]['page_redirect_code'];
	$page_modul = $result[$i]['page_modul'];
	
	if($page_template == "use_standard") {
		$show_template_name =  "$lang[use_standard]";
	} else {
		$show_template_name = "$page_template";
	}

	$pi = get_page_impression($page_id);
	
	if($page_status == "public") {
		$tr_status_class = 'page-list-public';
	} elseif($page_status == "ghost") {
		$tr_status_class = 'page-list-ghost';
	} elseif($page_status == "private") {
		$tr_status_class = 'page-list-private';
	} elseif($page_status == "draft") {
		$tr_status_class = 'page-list-draft';
	}
	
	$last_edit = date("d.m.Y H:i:s",$page_lastedit) . " ($page_lastedit_from)";
	
	/* check for display edit button */
	
	if($_SESSION['acp_editpages'] == "allowed"){
		$edit_button = "<a class='btn btn-sm btn-default' href='$_SERVER[PHP_SELF]?tn=pages&sub=edit&editpage=$page_id'><span class='glyphicon glyphicon-edit'></span> $lang[edit]</a>";
	} else {
		$edit_button = "<br>";
	}
	
	$arr_checked_admins = explode(",",$page_authorized_users);
	if(in_array("$_SESSION[user_nick]", $arr_checked_admins)) {
		$edit_button = "<a class='btn btn-sm btn-default' href='$_SERVER[PHP_SELF]?tn=pages&sub=edit&editpage=$page_id'><span class='glyphicon glyphicon-edit'></span> $lang[edit]</a>";
	}
	
	/* mark main and subpages | or not */
	$td_class = '';
	if(strpos($page_sort, '.') !== false) {
		$tr_page_class = 'subpage';
	} else {
		$tr_page_class = 'mainpage';
	}
	
	if($fc_mod_rewrite == "permalink") {
		$frontend_link = "../$page_permalink";
	} else {
		$frontend_link = "../index.php?p=$page_id";
	}
	
	$show_mod = '';
	if($page_modul != '') {
		$page_modul_title = substr($page_modul, 0,-4);
		$show_mod = ' <small><span class="glyphicon glyphicon-cog" title="'.$page_modul_title.'"></span></small>';
	}
	
	if($page_redirect != '') {
		$show_redirect = '<small class="text-primary"><span class="glyphicon glyphicon-arrow-right"></span> '.$page_redirect.'</small>';
		if($_SESSION['checked_redirect'] != "checked") {
			continue;
		}
	}
	
	echo"<tr class='$tr_status_class $tr_page_class'>
			<td><div class='extrainfo condensed'>$status_marker $page_sort</div></td>
			<td><a class='darklink tooltip_bottom' data-toggle='tooltip' title='$frontend_link' href='$frontend_link'>$page_linkname</a>$show_mod</td>
			<td>$page_title<p class='extrainfo condensed'>$last_edit | Style: $show_template_name | $page_language <br>$show_redirect</p></td>
			<td style='text-align:right;'>$pi</td>
			<td><div class='btn-group'>$edit_button</div></td>
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

echo '<table class="table-list table-pages table" border="0" cellpadding="0" cellspacing="0">';

echo '<thead>';
echo '<tr>';
echo '<th>'.$lang['h_page_linkname'].'</th>';
echo '<th>'.$lang['h_page_title'].'</th>';
echo '<th class="text-right">'.$lang['h_page_hits'].'</th>';
echo '<th style="width:120px;">'.$lang['h_action'].'</th>';
echo '</tr>';
echo '</thead>';


for($i=0;$i<$cnt_result;$i++) {

	if($result[$i]['page_sort'] != "" && $result[$i]['page_sort'] != 'portal') {
		continue;
	}
	
	unset($show_redirect);

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
	$page_redirect = $result[$i]['page_redirect'];
	
	if($page_template == "use_standard") {
		$show_template_name =  "$lang[use_standard]";
	} else {
		$show_template_name = "$page_template";
	}
	
	if($page_sort == 'portal') {
		$page_linkname = '<span class="glyphicon glyphicon-home"></span> ' . $page_linkname;
	}
	
	$hits_id = $page_id;	
	if($page_sort == "portal") {
		$hits_id = "portal_$page_language";
	}
	
	$pi = get_page_impression($hits_id);
	
	if($page_status == "public") {
		$tr_status_class = 'page-list-public';
	} elseif($page_status == "ghost") {
		$tr_status_class = 'page-list-ghost';
	} elseif($page_status == "private") {
		$tr_status_class = 'page-list-private';
	} elseif($page_status == "draft") {
		$tr_status_class = 'page-list-draft';
	}
	
	$last_edit = date("d.m.Y H:i:s",$page_lastedit) . "($page_lastedit_from)";
	
	/* check for display edit button */
	
	if($_SESSION['acp_editpages'] == "allowed"){
		$edit_button = "<a class='btn btn-sm btn-default' href='$_SERVER[PHP_SELF]?tn=pages&sub=edit&editpage=$page_id'><span class='glyphicon glyphicon-edit'></span> $lang[edit]</a>";
	} else {
		$edit_button = "<br />";
	}
	
	$arr_checked_admins = explode(",",$page_authorized_users);
	if(in_array("$_SESSION[user_nick]", $arr_checked_admins)) {
		$edit_button = "<a class='btn btn-sm btn-default' href='$_SERVER[PHP_SELF]?tn=pages&sub=edit&editpage=$page_id'><span class='glyphicon glyphicon-edit'></span> $lang[edit]</a>";
	}
	
	if($fc_mod_rewrite == "permalink") {
		$frontend_link = "../$page_permalink";
	} else {
		$frontend_link = "../index.php?p=$page_id";
	}
	
	if($page_redirect != '') {
		$show_redirect = '<small class="text-primary"><span class="glyphicon glyphicon-arrow-right"></span> '.$page_redirect.'</small>';
		if($_SESSION['checked_redirect'] != "checked") {
			continue;
		}
	}

	
	echo"<tr class='$tr_status_class'>
			<td><a class='darklink tooltip_bottom' data-toggle='tooltip' title='$frontend_link' href='$frontend_link'>$page_linkname</a></td>
			<td><span class='bold'>$page_title</span><p class='extrainfo condensed'>$last_edit | Style: $show_template_name | $lang[f_page_language]: $page_language<br>$show_redirect</p></td>
			<td style='text-align:right;'>$pi</td>
			<td><div class='btn-group'>$edit_button</div></td>
		</tr>";

} // eol for $i

echo"</table>";
echo"</fieldset>";

?>
