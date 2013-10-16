<?php


/**
 * Save, Delete, Preview and Update Pages
 * @author Patrick Konstandin
 */


//prohibit unauthorized access
require("core/access.php");

$show_form = "true";
$modus = "new";

if(!empty($_REQUEST['editpage'])) {
	$editpage = (int) $_REQUEST['editpage'];
	$modus = "update";
}


if(!empty($_REQUEST['preview_the_page'])) {
	$editpage = (int) $_REQUEST['editpage'];
	$modus = "preview";
}


$pdo_fields = array(
	'page_sort' => 'STR',
	'page_language' => 'STR',
	'page_linkname' => 'STR',
	'page_permalink' => 'STR',
	'page_title' => 'STR',
	'page_status' => 'STR',
	'page_usergroup' => 'STR',
	'page_content' => 'STR',
	'page_lastedit' => 'STR',
	'page_lastedit_from' => 'STR',
	'page_extracontent' => 'STR',
	'page_template' => 'STR',
	'page_template_layout' => 'STR',
	'page_meta_author' => 'STR',
	'page_meta_keywords' => 'STR',
	'page_meta_description' => 'STR',
	'page_meta_robots' => 'STR',
	'page_head_styles' => 'STR',
	'page_head_enhanced' => 'STR',
	'page_modul' => 'STR',
	'page_modul_query' => 'STR',
	'page_authorized_users' => 'STR',
	'page_version' => 'STR'
);

$pdo_fields_new = array(
	'page_id' => 'NULL',
	'page_sort' => 'STR',
	'page_language' => 'STR',
	'page_linkname' => 'STR',
	'page_permalink' => 'STR',
	'page_title' => 'STR',
	'page_status' => 'STR',
	'page_usergroup' => 'STR',
	'page_content' => 'STR',
	'page_lastedit' => 'STR',
	'page_lastedit_from' => 'STR',
	'page_extracontent' => 'STR',
	'page_template' => 'STR',
	'page_template_layout' => 'STR',
	'page_meta_author' => 'STR',
	'page_meta_keywords' => 'STR',
	'page_meta_description' => 'STR',
	'page_meta_robots' => 'STR',
	'page_head_styles' => 'STR',
	'page_head_enhanced' => 'STR',
	'page_modul' => 'STR',
	'page_modul_query' => 'STR',
	'page_authorized_users' => 'STR',
	'page_version' => 'STR'
);
	
$pdo_fields_cache = array(
	'page_id' => 'NULL',
	'page_id_original' => 'STR',
	'page_sort' => 'STR',
	'page_language' => 'STR',
	'page_linkname' => 'STR',
	'page_permalink' => 'STR',
	'page_title' => 'STR',
	'page_status' => 'STR',
	'page_usergroup' => 'STR',
	'page_content' => 'STR',
	'page_lastedit' => 'STR',
	'page_lastedit_from' => 'STR',
	'page_extracontent' => 'STR',
	'page_template' => 'STR',
	'page_template_layout' => 'STR',
	'page_meta_author' => 'STR',
	'page_meta_keywords' => 'STR',
	'page_meta_description' => 'STR',
	'page_meta_robots' => 'STR',
	'page_head_styles' => 'STR',
	'page_head_enhanced' => 'STR',
	'page_modul' => 'STR',
	'page_modul_query' => 'STR',
	'page_authorized_users' => 'STR',
	'page_cache_type' => 'STR',
	'page_version' => 'STR'
);


/**
 * if we have custom fields
 * expand the array ($pdo_fields...)
 */
 
if(preg_match("/custom_/i", implode(",", array_keys($_POST))) ){
  $custom_fields = get_custom_fields();
  $cnt_result = count($custom_fields);
  
  for($i=0;$i<$cnt_result;$i++) {
  	if(substr($custom_fields[$i],0,7) == "custom_") {
  		$cf = $custom_fields[$i];
  		$pdo_fields[$cf] = 'STR';
  		$pdo_fields_new[$cf] = 'STR';
  		$pdo_fields_cache[$cf] = 'STR';
  	}
  }      
}



/**
 * delete the page by page_id - $editpage
 */

if($_POST['delete_the_page']) {

	$dbh = new PDO("sqlite:".CONTENT_DB);

	if(is_numeric($editpage)){
		$sql = "DELETE FROM fc_pages WHERE page_id = $editpage";
	}

	$cnt_changes = $dbh->exec($sql);

	$dbh = null;

		if($cnt_changes > 0) {
			$success_message = "{OKAY} $lang[msg_page_deleted]";
			record_log("$_SESSION[user_nick]","deleted page id: $editpage","0");
			generate_xml_sitemap();
			delete_cache_file();
			unset($editpage);
			print_sysmsg("$success_message");
		}

$show_form = "false";

}



/**
 * Save, update or show preview
 */

if($_POST['save_the_page'] OR $_REQUEST['preview_the_page']) {


$page_lastedit = time();
$page_lastedit_from = "$_SESSION[user_nick]";

$page_position = $_POST[page_position];
$page_order = $_POST[page_order];

$page_sort = "$page_position.$page_order";

$page_version = $_POST[page_version];
$page_title = strip_tags($_POST[page_title]);
$page_linkname = strip_tags($_POST[page_linkname]);

if($page_position == "portal") {
	$page_sort = "portal";
} elseif ($page_position == "mainpage") {
	$page_sort = (int) $page_order;
} elseif ($page_position == "null") {
	$page_sort = "";
}



//usergroups
$arr_set_usergroup = $_POST[set_usergroup];

if(is_array($arr_set_usergroup)) {
	sort($arr_set_usergroup);
	$string_usergroup = implode(",", $arr_set_usergroup);
} else {
	$string_usergroup = "";
}

//set_authorized_admins
$arr_set_authorized_admins = $_POST[set_authorized_admins];

if(is_array($arr_set_authorized_admins)) {
	sort($arr_set_authorized_admins);
	$string_authorized_admins = implode(",", $arr_set_authorized_admins);
} else {
	$string_authorized_admins = "";
}

// template
$select_template = explode("<|-|>", $_POST[select_template]);
$page_template 			= $select_template[0];
$page_template_layout 	= $select_template[1];



// connect to database
$dbh = new PDO("sqlite:".CONTENT_DB);


/**
 * modus update
 */

if($modus == "update") {

	$page_version = $_POST[page_version]+1;
	
	$sql_u = generate_sql_update_str($pdo_fields,"fc_pages","WHERE page_id = $editpage");							
	$sth = $dbh->prepare($sql_u);
	generate_bindParam_str($pdo_fields,$sth);
	
	$sth->bindParam(':page_sort', $page_sort, PDO::PARAM_STR);
	$sth->bindParam(':page_usergroup', $string_usergroup, PDO::PARAM_STR);
	$sth->bindParam(':page_lastedit', $page_lastedit, PDO::PARAM_INT);
	$sth->bindParam(':page_lastedit_from', $_SESSION[user_nick], PDO::PARAM_STR);
	$sth->bindParam(':page_template', $page_template, PDO::PARAM_STR);
	$sth->bindParam(':page_template_layout', $page_template_layout, PDO::PARAM_STR);
	$sth->bindParam(':page_authorized_users', $string_authorized_admins, PDO::PARAM_STR);
	$sth->bindParam(':page_version', $page_version, PDO::PARAM_INT);
	
	$cnt_changes = $sth->execute();
	
		if($cnt_changes == TRUE) {
			$sys_message = "{OKAY} $lang[msg_page_updated]";
			record_log("$_SESSION[user_nick]","page update <b>$page_linkname</b> &raquo;$page_title&laquo;","0");
			generate_xml_sitemap();
			delete_cache_file();
		} else {
			$sys_message = "{error} $lang[msg_page_saved_error] ($page_sort)";
		}
		
		print_sysmsg("$sys_message");
	
	
	/* cache this version */
	
	$page_id_original = "$editpage";
	$page_cache_type = "history";
	
	$sql = generate_sql_insert_str($pdo_fields_cache,"fc_pages_cache");	
	$std = $dbh->prepare($sql);
	generate_bindParam_str($pdo_fields_cache,$std);
	
	$std->bindParam(':page_sort', $page_sort, PDO::PARAM_STR);
	$std->bindParam(':page_usergroup', $string_usergroup, PDO::PARAM_STR);
	$std->bindParam(':page_lastedit', $page_lastedit, PDO::PARAM_INT);
	$std->bindParam(':page_lastedit_from', $_SESSION[user_nick], PDO::PARAM_STR);
	$std->bindParam(':page_template', $page_template, PDO::PARAM_STR);
	$std->bindParam(':page_template_layout', $page_template_layout, PDO::PARAM_STR);
	$std->bindParam(':page_authorized_users', $string_authorized_admins, PDO::PARAM_STR);
	$std->bindParam(':page_version', $page_version, PDO::PARAM_INT);
	$std->bindParam(':page_id_original', $page_id_original, PDO::PARAM_STR);
	$std->bindParam(':page_cache_type', $page_cache_type, PDO::PARAM_STR);
	
	$cnt_changes_c = $std->execute();

} // eo modus update


/**
 * modus new page
 */							

if($modus == "new") {

	$page_id = null;
	$sql = generate_sql_insert_str($pdo_fields_new,"fc_pages");
	$sth = $dbh->prepare($sql);
	generate_bindParam_str($pdo_fields,$sth);
	
	$sth->bindParam(':page_usergroup', $string_usergroup, PDO::PARAM_STR);
	$sth->bindParam(':page_lastedit', $page_lastedit, PDO::PARAM_INT);
	$sth->bindParam(':page_lastedit_from', $_SESSION[user_nick], PDO::PARAM_STR);
	$sth->bindParam(':page_template', $page_template, PDO::PARAM_STR);
	$sth->bindParam(':page_template_layout', $page_template_layout, PDO::PARAM_STR);
	$sth->bindParam(':page_sort', $page_sort, PDO::PARAM_STR);
	$sth->bindParam(':page_authorized_users', $string_authorized_admins, PDO::PARAM_STR);
	
	$cnt_changes = $sth->execute();
	$editpage = $dbh->lastInsertId();
	
	if($cnt_changes == TRUE) {
		$sys_message = "{OKAY} $lang[msg_page_saved]";
		record_log("$_SESSION[user_nick]","new Page <i>$page_title</i>","0");
		generate_xml_sitemap();
		delete_cache_file();
	} else {
		$sys_message = "{error} $lang[msg_page_saved_error]";
	}
	
	print_sysmsg("$sys_message");
	
	
	/* cache the new page version */
	
	$page_version = 1;
	$page_id_original = "$editpage";
	$page_cache_type = "history";
	
	$sql = generate_sql_insert_str($pdo_fields_cache,"fc_pages_cache");					
	$std = $dbh->prepare($sql);
	
	generate_bindParam_str($pdo_fields_cache,$std);
	
	$std->bindParam(':page_sort', $page_sort, PDO::PARAM_STR);
	$std->bindParam(':page_usergroup', $string_usergroup, PDO::PARAM_STR);
	$std->bindParam(':page_lastedit', $page_lastedit, PDO::PARAM_INT);
	$std->bindParam(':page_lastedit_from', $_SESSION[user_nick], PDO::PARAM_STR);
	$std->bindParam(':page_template', $page_template, PDO::PARAM_STR);
	$std->bindParam(':page_template_layout', $page_template_layout, PDO::PARAM_STR);
	$std->bindParam(':page_authorized_users', $string_authorized_admins, PDO::PARAM_STR);
	$std->bindParam(':page_cache_type', $page_cache_type, PDO::PARAM_STR);
	$std->bindParam(':page_version', $page_version, PDO::PARAM_INT);
	
	$cnt_changes_c = $std->execute();

} // eo modus new



/**
 * modus preview
 */
 							
if($modus == "preview") {

	$page_id_original = "$editpage";
	$page_cache_type = "preview";
	
	$sql = generate_sql_insert_str($pdo_fields_cache,"fc_pages_cache");					
	$std = $dbh->prepare($sql);
	
	generate_bindParam_str($pdo_fields_cache,$std);
	
	$std->bindParam(':page_id_original', $page_id_original, PDO::PARAM_STR);
	$std->bindParam(':page_sort', $page_sort, PDO::PARAM_STR);
	$std->bindParam(':page_usergroup', $string_usergroup, PDO::PARAM_STR);
	$std->bindParam(':page_lastedit', $page_lastedit, PDO::PARAM_INT);
	$std->bindParam(':page_lastedit_from', $_SESSION[user_nick], PDO::PARAM_STR);
	$std->bindParam(':page_template', $page_template, PDO::PARAM_STR);
	$std->bindParam(':page_template_layout', $page_template_layout, PDO::PARAM_STR);
	$std->bindParam(':page_authorized_users', $string_authorized_admins, PDO::PARAM_STR);
	$std->bindParam(':page_version', $page_version, PDO::PARAM_INT);
	$std->bindParam(':page_cache_type', $page_cache_type, PDO::PARAM_STR);
	
	
	$cnt_changes_c = $std->execute();
	
	/* delete older entries from fc_pages_cache */
	$interval = time() - 86400; // now - 24h
	$count = $dbh->exec("DELETE FROM fc_pages_cache WHERE page_cache_type = 'preview' AND page_lastedit < '$interval'");
	
} // eo modus preview





$dbh = null;


/* generate cache files */
cache_lastedit();
cache_keywords();
mods_check_in();
cache_url_paths();

}




/* get the data to fill the form (again) */
if(is_numeric($editpage)) {

	$dbh = new PDO("sqlite:".CONTENT_DB);
	
	if($modus == "preview") {
		$sql = "SELECT * FROM fc_pages_cache WHERE page_id_original = $editpage ORDER BY page_id DESC";
	} else {
		$sql = "SELECT * FROM fc_pages WHERE page_id = $editpage";
	}
	
	
	if(!empty($_REQUEST['restore_id'])) {
		$restore_id = (int) $_REQUEST['restore_id'];
		$sql = "SELECT * FROM fc_pages_cache WHERE page_id = $restore_id";
		
		$restore_page_version = $dbh->query("SELECT page_version FROM fc_pages WHERE page_id = $editpage")->fetch();
		
	}
	
	$result = $dbh->query($sql);
	$result = $result->fetch(PDO::FETCH_ASSOC);
	
	foreach($result as $k => $v) {
	   $$k = htmlspecialchars(stripslashes($v));
	}
	
	if(is_array($restore_page_version)) {
		$page_version = $restore_page_version[page_version];
	}
	
	
	echo"<h3>$lang[h_modus_editpage] - $page_title (Version: $page_version)</h3>";
		//set submit button
		$submit_button = "<input type='submit' class='btn btn-success' name='save_the_page' value='$lang[update_page]'>";
		$delete_button = "<input type='submit' class='btn btn-danger' name='delete_the_page' value='$lang[delete_page]' onclick=\"return confirm('$lang[confirm_delete_data]')\">";
		$previev_button = "<input type='submit' class='btn' id='preview_the_page' name='preview_the_page' value='$lang[preview]'>";
	
} else {
	// modus newpage
	
	
	echo"<h3>$lang[h_modus_newpage]</h3>";
	
		//set submit button
		$submit_button = "<input type='submit' class='btn btn-success' name='save_the_page' value='$lang[save_new_page]'>";
		$delete_button = "";
		$previev_button = "<input type='submit' class='btn' id='preview_the_page' name='preview_the_page' value='$lang[preview]'>";
}



if($_SESSION[acp_editpages] != "allowed") {
	$arr_checked_admins = explode(",",$page_authorized_users);
	if(!in_array("$_SESSION[user_nick]", $arr_checked_admins)) {
		$show_form = "false";
		echo"<p>$lang[drm_no_access]</p>";
	}
}


if($show_form == "true") {
	include("pages.edit_form.php");
}


/* Attach the preview */

if(!empty($_REQUEST['preview_the_page'])) {

	echo'<hr><div class="alert alert-info alert-block">';
	echo"<iframe src='../index.php?preview=$editpage' width='100%' height='600'>";
	echo"<a href='../index.php?preview=$editpage' target='_blank'>../index.php?preview=$editpage</a>";
	echo"</iframe>";
	echo'</div>';

}



/* show older versions of the current page */

if($show_form == "true" AND $sub != "new") {

	$dbh = new PDO("sqlite:".CONTENT_DB);
	
	$max = 50;
	$cnt_all_sql = "SELECT COUNT(*) AS 'nbr' FROM fc_pages_cache WHERE page_id_original = $editpage AND page_cache_type = 'history' ";
	$cnt_all = $dbh->query("$cnt_all_sql")->fetch(PDO::FETCH_ASSOC);
	$delete_nbr = $cnt_all[nbr]-$max;
	
	$sql = "SELECT page_id, page_linkname, page_title, page_lastedit, page_lastedit_from, page_version
					FROM fc_pages_cache
					WHERE page_id_original = $editpage AND page_cache_type = 'history'
					ORDER BY page_id DESC";
	
	 foreach ($dbh->query($sql) as $row) {
	   $cache_result[] = $row;
	 }
	
	$cnt_result = count($cache_result);
	
	echo '<hr>';	
	echo '<div class="accordion" id="versionsToggle">';
	echo '<div class="accordion-group">';
	echo '<div class="accordion-heading">';
	echo '<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#showVersions">Versions</a>';
	echo '</div>';
	
	echo '<div id="showVersions" class="accordion-body collapse in">';
	echo '<div class="accordion-inner">';

	echo '<table class="table table-condensed table-hover">';

	for($i=0;$i<$cnt_result;$i++) {
	
		$nbr = $i+1;
		$page_id = $cache_result[$i][page_id];

		
		if($i >= $max) {
			
			$del_sql = "DELETE FROM fc_pages_cache
									WHERE page_id IN (
										SELECT page_id
										FROM fc_pages_cache
										WHERE page_id_original = '$editpage'
										ORDER BY page_lastedit ASC
										LIMIT 1)";
			
			$cnt_changes = $dbh->exec($del_sql);
			continue;
		}
		
		
		
			$date = date("d.m.Y",$cache_result[$i][page_lastedit]);
			$time = date("H:i:s",$cache_result[$i][page_lastedit]);
			$yesterday = date('d.m.Y', time()-(60*60*24));
			$today = date('d.m.Y', time());
			
			if($date == "$today") {
				$setdate = "$lang[date_today]";
			} elseif($date == "$yesterday") {
				$setdate = "$lang[date_yesterday]";
			} else {
				$setdate = $date;
			}
			
			echo "<tr>
					<td>" . $cache_result[$i][page_version] . "</td>
					<td width='100'>$setdate</td>
					<td width='100'>$time</td>
					<td>" . $cache_result[$i][page_title] . "</td>
					<td> " . $cache_result[$i][page_lastedit_from] . "</td>
					<td width='100' align='right'><a class='btn btn-small' href='$_SERVER[PHP_SELF]?tn=pages&sub=edit&restore_id=$page_id&editpage=$editpage'>$lang[edit]</a></td>
				</tr>";
	}
	
	$dbh = null;
	
	echo '</table>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</fieldset>';

}

?>
