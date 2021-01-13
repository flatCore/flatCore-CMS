<?php

/**
 * Save, Duplicate, Delete, Preview and Update Pages
 * @author Patrick Konstandin
 *
 */

//prohibit unauthorized access
require 'core/access.php';

$show_form = "true";
$modus = "new";

foreach($_POST as $key => $val) {
	$$key = $val; 
}

if(!empty($_POST['editpage'])) {
	$editpage = (int) $_POST['editpage'];
	$modus = "update";
}

if((!empty($_POST['duplicate'])) OR ($_POST['modus'] == 'duplicate')) {
	$editpage = (int) $_POST['duplicate'];
	$modus = "duplicate";
}

if(!empty($_POST['preview_the_page'])) {
	$editpage = (int) $_POST['editpage'];
	$modus = "preview";
}


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
  		$custom_fields[] = $cf;
  	}
  }      
}


/**
 * delete the page by page_id - $editpage
 */

if(isset($_POST['delete_the_page'])) {


	if(is_numeric($editpage)) {
		$comment_id = 'p'.$editpage;
		
		/**
		 * we check, if this page has subpages
		 * if there are subpages, we can not delete the page
		 */

		$delete_page = $db_content->get("fc_pages", ["page_sort","page_language"],[
			"page_id" => $editpage
		]);
		
		$delpage_sort = $delete_page['page_sort'];
		$delpage_lang = $delete_page['page_language'];
		
		if($delpage_sort != '') {
			$subpages = $db_content->select("fc_pages", ["page_sort","page_title"],[
				"AND" => [
					"page_sort[~]" => "$delpage_sort%",
					"page_language" => $delpage_lang
				]
			]);
		} else {
			$subpages = '';
		}
		
		if(is_array($subpages)) {
			echo '<div class="alert alert-danger">';
			echo $lang['msg_error_deleting_sub_pages'];
			
			echo '<ol>';
			foreach($subpages as $pages) {
				echo '<li>'.$pages['page_title'].'</li>';
			}
			echo '</ol>';
			
			echo '</div>';
		} else {

			$del_page = $db_content->delete("fc_pages", [
				"page_id" => $editpage
			]);
			$db_content->delete("fc_pages_cache", [
				"page_id_original" => $editpage
			]);
			$db_content->delete("fc_pages_cache", [
				"page_id_original" => NULL
			]);
			$db_content->delete("fc_comments", [
				"comment_parent" => $comment_id
			]);
			
			if($del_page->rowCount() > 0) {
				$success_message = '{OKAY} '. $lang['msg_page_deleted'];
				record_log($_SESSION['user_nick'],"deleted page id: $editpage","10");
				generate_xml_sitemap();
				delete_cache_file();
				unset($editpage);
				print_sysmsg("$success_message");
			}
		}
	}




	$show_form = "false";
}



/**
 * Save, update or show preview
 */

if($_POST['save_the_page'] OR $_POST['preview_the_page']) {
	
	$page_lastedit = time();
	$page_lastedit_from = $_SESSION['user_nick'];
	
	$page_position = $_POST['page_position'];
	$page_order = $_POST['page_order'];
	
	if($page_position != 'null' OR $page_position != 'portal') {
		$page_order = (int) $page_order;
		if(strlen($page_order) < $prefs_pagesort_minlength) {
			$page_order = str_pad($page_order, $prefs_pagesort_minlength, "0", STR_PAD_LEFT);
		}
		
	}
	
	$page_sort = "$page_position.$page_order";
	
	$page_version = $_POST['page_version'];
	$page_title = strip_tags($_POST['page_title']);
	$page_linkname = strip_tags($_POST['page_linkname']);
	
	if($page_position == "portal") {
		$page_sort = "portal";
	}
	if($page_position == "mainpage") {
		$page_sort = (int) $page_order;
	}
	if($page_position == "null") {
		$page_sort = "";
	}
	
	/* page thumbnails */
	if(count($_POST['picker1_images']) > 1) {
		$page_thumbnail = implode("<->", $_POST['picker1_images']);
	} else {
		$pt = $_POST['picker1_images'];
		$page_thumbnail = $pt[0];
	}
	
	$page_hash = clean_filename($_POST['page_hash']);


	//usergroups
	$arr_set_usergroup = $_POST['set_usergroup'];
	
	if(is_array($arr_set_usergroup)) {
		sort($arr_set_usergroup);
		$string_usergroup = implode(",", $arr_set_usergroup);
	} else {
		$string_usergroup = "";
	}
	
	//set_authorized_admins
	$arr_set_authorized_admins = $_POST['set_authorized_admins'];
	
	if(is_array($arr_set_authorized_admins)) {
		sort($arr_set_authorized_admins);
		$string_authorized_admins = implode(",", $arr_set_authorized_admins);
	} else {
		$string_authorized_admins = "";
	}
	
	/* password */

	if($_POST['page_psw'] != '') {
		$page_psw = md5($_POST['page_psw']);
	}
	if($_POST['page_psw_relay'] != '' && $_POST['page_psw'] == '') {
		$page_psw = $_POST['page_psw_relay'];
	}
	if($_POST['page_psw_reset'] == 'reset') {
		$page_psw = '';
	}
	
	/* labels */
	$arr_labels = $_POST['set_page_labels'];
	if(is_array($arr_labels)) {
		sort($arr_labels);
		$string_labels = implode(",", $arr_labels);
	} else {
		$string_labels = "";
	}

	/* categories */
	$arr_categories = $_POST['set_page_categories'];
	if(is_array($arr_categories)) {
		sort($arr_categories);
		$string_page_categories = implode(",", $arr_categories);
	} else {
		$string_page_categories = "";
	}	
	
	// template
	$select_template = explode("<|-|>", $_POST['select_template']);
	$page_template 			= $select_template[0];
	$page_template_layout 	= $select_template[1];
	
	/* page_meta_robots */
	$page_meta_robots = implode(',',$_POST['page_meta_robots']);

	/* addon injection */
	$page_addon_string = '';
	if(is_array($_POST['addon'])) {
		$page_addon_string = json_encode($_POST['addon'],JSON_UNESCAPED_UNICODE);
	}
	
	/* posts categories */
	if(is_array($_POST['page_post_categories'])) {
		$string_categories = implode(",", $_POST['page_post_categories']);
	} else {
		$string_categories = "";
	}
	
	/* posts types */
	if(is_array($_POST['page_post_types'])) {
		$string_types = implode("-", $_POST['page_post_types']);
	} else {
		$string_types = "";
	}

	/**
	 * modus update
	 */
	
	if($modus == "update") {
	
		$page_version = $_POST['page_version']+1;
		
		$columns = [
			"page_sort" => "$page_sort",
			"page_language" => "$page_language",
			"page_linkname" => "$page_linkname",
			"page_permalink" => "$page_permalink",
			"page_permalink_short" => "$page_permalink_short",
			"page_classes" => "$page_classes",
			"page_hash" => "$page_hash",
			"page_type_of_use" => "$page_type_of_use",
			"page_redirect" => "$page_redirect",
			"page_redirect_code" => "$page_redirect_code",
			"page_funnel_uri" => "$page_funnel_uri",
			"page_title" => "$page_title",
			"page_status" => "$page_status",
			"page_usergroup" => "$page_usergroup",
			"page_content" => "$page_content",
			"page_extracontent" => "$page_extracontent",
			"page_lastedit" => $page_lastedit,
			"page_lastedit_from" => $_SESSION['user_nick'],
			"page_template" => "$page_template",
			"page_template_layout" => "$page_template_layout",
			"page_meta_author" => "$page_meta_author",
			"page_meta_keywords" => "$page_meta_keywords",
			"page_meta_description" => "$page_meta_description",
			"page_meta_robots" => "$page_meta_robots",
			"page_head_styles" => "$page_head_styles",
			"page_head_enhanced" => "$page_head_enhanced",
			"page_thumbnail" => "$page_thumbnail",
			"page_modul" => "$page_modul",
			"page_modul_query" => "$page_modul_query",
			"page_addon_string" => "$page_addon_string",
			"page_posts_categories" => "$string_categories",
			"page_posts_types" => "$string_types",
			"page_authorized_users" => "$page_authorized_users",
			"page_version" => $page_version,
			"page_labels" => "$string_labels",
			"page_categories" => "$string_page_categories",
			"page_comments" => "$page_comments",
			"page_psw" => "$page_psw"		
		];
		
		/* add the custom fields */
		foreach($custom_fields as $f) {
			$columns[$f] = "${$f}";
		}
				
		$cnt_changes = $db_content->update("fc_pages", $columns, [
			"page_id" => $editpage
		]);
	
		if($cnt_changes->rowCount() > 0) {
			$sys_message = "{OKAY} $lang[msg_page_updated]";
			record_log("$_SESSION[user_nick]","page update <b>$page_linkname</b> &raquo;$page_title&laquo;","5");
			generate_xml_sitemap();
			delete_cache_file();
		} else {
			$sys_message = "{error} $lang[msg_page_saved_error] ($page_sort)";
		}
		
		print_sysmsg("$sys_message");
	
	
		/* cache this version */
		
		$page_id_original = "$editpage";
		$page_cache_type = "history";
		
		
		$columns_cache = [
			"page_id_original" => "$page_id_original",
			"page_cache_type" => "$page_cache_type",
			
			"page_sort" => "$page_sort",
			"page_language" => "$page_language",
			"page_linkname" => "$page_linkname",
			"page_permalink" => "$page_permalink",
			"page_permalink_short" => "$page_permalink_short",
			"page_classes" => "$page_classes",
			"page_hash" => "$page_hash",
			"page_type_of_use" => "$page_type_of_use",
			"page_redirect" => "$page_redirect",
			"page_redirect_code" => "$page_redirect_code",
			"page_funnel_uri" => "$page_funnel_uri",
			"page_title" => "$page_title",
			"page_status" => "$page_status",
			"page_usergroup" => "$page_usergroup",
			"page_content" => "$page_content",
			"page_extracontent" => "$page_extracontent",
			"page_lastedit" => $page_lastedit,
			"page_lastedit_from" => $_SESSION['user_nick'],
			"page_template" => "$page_template",
			"page_template_layout" => "$page_template_layout",
			"page_meta_author" => "$page_meta_author",
			"page_meta_keywords" => "$page_meta_keywords",
			"page_meta_description" => "$page_meta_description",
			"page_meta_robots" => "$page_meta_robots",
			"page_head_styles" => "$page_head_styles",
			"page_head_enhanced" => "$page_head_enhanced",
			"page_thumbnail" => "$page_thumbnail",
			"page_modul" => "$page_modul",
			"page_modul_query" => "$page_modul_query",
			"page_addon_string" => "$page_addon_string",
			"page_posts_categories" => "$string_categories",
			"page_posts_types" => "$string_types",
			"page_authorized_users" => "$page_authorized_users",
			"page_version" => $page_version,
			"page_labels" => "$string_labels",
			"page_categories" => "$string_page_categories",
			"page_comments" => "$page_comments",
			"page_psw" => "$page_psw"
		];
		
		/* add the custom fields */
		foreach($custom_fields as $f) {
			$columns_cache[$f] = "${$f}";
		}

		$cnt_changes_c = $db_content->insert("fc_pages_cache", $columns_cache);	
		
	
	} // eo modus update


	/**
	 * modus new page
	 * or duplicate page
	 */							
	
	if($modus == "new" || $modus == 'duplicate') {
		
		$columns_new = [
			"page_sort" => "$page_sort",
			"page_language" => "$page_language",
			"page_linkname" => "$page_linkname",
			"page_permalink" => "$page_permalink",
			"page_permalink_short" => "$page_permalink_short",
			"page_classes" => "$page_classes",
			"page_hash" => "$page_hash",
			"page_type_of_use" => "$page_type_of_use",
			"page_redirect" => "$page_redirect",
			"page_redirect_code" => "$page_redirect_code",
			"page_funnel_uri" => "$page_funnel_uri",
			"page_title" => "$page_title",
			"page_status" => "$page_status",
			"page_usergroup" => "$page_usergroup",
			"page_content" => "$page_content",
			"page_extracontent" => "$page_extracontent",
			"page_lastedit" => $page_lastedit,
			"page_lastedit_from" => $_SESSION['user_nick'],
			"page_template" => "$page_template",
			"page_template_layout" => "$page_template_layout",
			"page_meta_author" => "$page_meta_author",
			"page_meta_keywords" => "$page_meta_keywords",
			"page_meta_description" => "$page_meta_description",
			"page_meta_robots" => "$page_meta_robots",
			"page_head_styles" => "$page_head_styles",
			"page_head_enhanced" => "$page_head_enhanced",
			"page_thumbnail" => "$page_thumbnail",
			"page_modul" => "$page_modul",
			"page_modul_query" => "$page_modul_query",
			"page_addon_string" => "$page_addon_string",
			"page_posts_categories" => "$string_categories",
			"page_posts_types" => "$string_types",
			"page_authorized_users" => "$page_authorized_users",
			"page_version" => $page_version,
			"page_labels" => "$string_labels",
			"page_categories" => "$string_page_categories",
			"page_comments" => "$page_comments",
			"page_psw" => "$page_psw"
		];
		
		/* add the custom fields */
		foreach($custom_fields as $f) {
			$columns_new[$f] = "${$f}";
		}
		
		$cnt_changes = $db_content->insert("fc_pages",$columns_new);
		
		$editpage = $db_content->id();
		
		
		if($cnt_changes->rowCount() > 0) {
			$sys_message = "{OKAY} $lang[msg_page_saved]";
			record_log("$_SESSION[user_nick]","new Page <i>$page_title</i>","5");
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
		
		$columns_cache = [
			"page_id_original" => "$page_id_original",
			"page_cache_type" => "$page_cache_type",
			
			"page_sort" => "$page_sort",
			"page_language" => "$page_language",
			"page_linkname" => "$page_linkname",
			"page_permalink" => "$page_language",
			"page_permalink_short" => "$page_permalink_short",
			"page_classes" => "$page_classes",
			"page_hash" => "$page_hash",
			"page_type_of_use" => "$page_type_of_use",
			"page_redirect" => "$page_redirect",
			"page_redirect_code" => "$page_redirect_code",
			"page_funnel_uri" => "$page_funnel_uri",
			"page_title" => "$page_title",
			"page_status" => "$page_status",
			"page_usergroup" => "$page_usergroup",
			"page_content" => "$page_content",
			"page_extracontent" => "$page_extracontent",
			"page_lastedit" => $page_lastedit,
			"page_lastedit_from" => $_SESSION['user_nick'],
			"page_template" => "$page_template",
			"page_template_layout" => "$page_template_layout",
			"page_meta_author" => "$page_meta_author",
			"page_meta_keywords" => "$page_meta_keywords",
			"page_meta_description" => "$page_meta_description",
			"page_meta_robots" => "$page_meta_robots",
			"page_head_styles" => "$page_head_styles",
			"page_head_enhanced" => "$page_head_enhanced",
			"page_thumbnail" => "$page_thumbnail",
			"page_modul" => "$page_modul",
			"page_modul_query" => "$page_modul_query",
			"page_addon_string" => "$page_addon_string",
			"page_posts_categories" => "$string_categories",
			"page_posts_types" => "$string_types",
			"page_authorized_users" => "$page_authorized_users",
			"page_version" => $page_version,
			"page_labels" => "$string_labels",
			"page_categories" => "$string_page_categories",
			"page_comments" => "$page_comments",
			"page_psw" => "$page_psw"
		];
		
		/* add the custom fields */
		foreach($custom_fields as $f) {
			$columns_cache[$f] = "${$f}";
		}

		$cnt_changes_c = $db_content->insert("fc_pages_cache",$columns_cache);	
		
	
	} // eo modus new



	/**
	 * modus preview
	 */
	 							
	if($modus == "preview") {
		
		$page_id_original = $editpage;
		$page_cache_type = "preview";
		
		$columns_preview = [
			"page_id_original" => "$page_id_original",
			"page_cache_type" => "$page_cache_type",
			
			"page_sort" => "$page_sort",
			"page_language" => "$page_language",
			"page_linkname" => "$page_linkname",
			"page_permalink" => "$page_language",
			"page_permalink_short" => "$page_permalink_short",
			"page_classes" => "$page_classes",
			"page_hash" => "$page_hash",
			"page_type_of_use" => "$page_type_of_use",
			"page_redirect" => "$page_redirect",
			"page_redirect_code" => "$page_redirect_code",
			"page_funnel_uri" => "$page_funnel_uri",
			"page_title" => "$page_title",
			"page_status" => "$page_status",
			"page_usergroup" => "$page_usergroup",
			"page_content" => "$page_content",
			"page_extracontent" => "$page_extracontent",
			"page_lastedit" => $page_lastedit,
			"page_lastedit_from" => $_SESSION['user_nick'],
			"page_template" => "$page_template",
			"page_template_layout" => "$page_template_layout",
			"page_meta_author" => "$page_meta_author",
			"page_meta_keywords" => "$page_meta_keywords",
			"page_meta_description" => "$page_meta_description",
			"page_meta_robots" => "$page_meta_robots",
			"page_head_styles" => "$page_head_styles",
			"page_head_enhanced" => "$page_head_enhanced",
			"page_thumbnail" => "$page_thumbnail",
			"page_modul" => "$page_modul",
			"page_modul_query" => "$page_modul_query",
			"page_addon_string" => "$page_addon_string",
			"page_posts_categories" => "$string_categories",
			"page_posts_types" => "$string_types",
			"page_authorized_users" => "$page_authorized_users",
			"page_version" => $page_version,
			"page_labels" => "$string_labels",
			"page_categories" => "$string_page_categories",
			"page_comments" => "$page_comments",
			"page_psw" => "$page_psw"
		];
		
		/* add the custom fields */
		foreach($custom_fields as $f) {
			$columns_preview[$f] = "${$f}";
		}
		
		$cnt_changes_c = $db_content->insert("fc_pages_cache",$columns_preview);	
		
		
		/* delete older entries from fc_pages_cache */
		$interval = time() - 86400; // now - 24h		
		$db_content->delete("fc_pages_cache", [
			"AND" => [
				"page_cache_type" => "preview",
				"page_lastedit[<]" => $interval
			]
		]);
		
		
	} // eo modus preview



	$dbh = null;
	
	
	/* generate cache files */
	cache_lastedit();
	cache_keywords();
	mods_check_in();
	cache_url_paths();
	
	fc_get_hook('page_updated',$_POST);	
	fc_delete_smarty_cache(md5($_POST['page_permalink']));
	
	if($_POST['page_status'] == 'ghost' OR $_POST['page_status'] == 'public') {
		fc_update_or_insert_index($_POST['page_permalink']);
	}
	

}


/* get the data to fill the form (again) */
if(is_numeric($editpage)) {

	if($modus == "preview") {		
		$page_data = $db_content->get("fc_pages_cache","*",[
			"AND" => [
			"page_id_original" => $editpage
		],
			"ORDER" => ["page_id" => "DESC"]
		]);
		
	} else {
		$page_data = $db_content->get("fc_pages","*",[ "page_id" => $editpage ]);
	}
	
	if(!empty($_POST['restore_id'])) {
		$restore_id = (int) $_POST['restore_id'];
		$page_data = $db_content->get("fc_pages_cache","*",[ "page_id" => $restore_id ]);	
		$restore_page_version = $db_content->query("SELECT page_version FROM fc_pages WHERE page_id = $editpage")->fetch();
	}

	
	foreach($page_data as $k => $v) {
	   $$k = htmlentities(stripslashes($v), ENT_QUOTES, "UTF-8");
	}
	
	if(is_array($restore_page_version)) {
		$page_version = $restore_page_version['page_version'];
	}
	
	
	$form_title = '<h3>'.$lang['h_modus_editpage'].' - <small>'.$page_title.' (Version: '.$page_version.' ID: '.$editpage.')</small></h3>';
	//set submit button
	$submit_button = "<input type='submit' class='btn btn-save w-100' name='save_the_page' value='$lang[update_page]'>";
	$delete_button = "<hr><input type='submit' class='btn btn-danger btn-sm btn-block' name='delete_the_page' value='$lang[delete_page]' onclick=\"return confirm('$lang[confirm_delete_data]')\">";
	$previev_button = "<input type='submit' class='btn btn-fc w-100' id='preview_the_page' name='preview_the_page' value='$lang[preview]'>";
	
	if($modus == 'duplicate') {
		$form_title = '<h3>'.$lang['h_modus_duplicate'].' - '.$page_title.'</h3>';
		$submit_button = "<input type='submit' class='btn btn-save w-100 btn-outline-success' name='save_the_page' value='$lang[save_duplicate]'>";
		$delete_button = '';
		$previev_button = '';
	}
	
} else {
	// modus newpage
	
	
	$form_title = '<h3>'.$lang['h_modus_newpage'].'</h3>';
	//set submit button
	$submit_button = "<input type='submit' class='btn btn-save btn-block' name='save_the_page' value='$lang[save_new_page]'>";
	$delete_button = '';
	$previev_button = '';
}

echo $form_title;


if($_SESSION['acp_editpages'] != "allowed") {
	$arr_checked_admins = explode(",",$page_authorized_users);
	if(!in_array("$_SESSION[user_nick]", $arr_checked_admins)) {
		$show_form = "false";
		echo '<p>'.$lang['drm_no_access'].'</p>';
	}
}


if($show_form == "true") {
	include 'pages.edit_form.php';
}


/* Attach the preview */

if(!empty($_POST['preview_the_page'])) {

	echo '<hr><div class="alert alert-info alert-block">';
	echo '<iframe src="../index.php?preview='.$editpage.'" width="100%" height="600">';
	echo '<a href="../index.php?preview='.$editpage.'" target="_blank">../index.php?preview='.$editpage.'</a>';
	echo '</iframe>';
	echo '</div>';

}



/* show older versions of the current page */

if($show_form == "true" AND $sub != "new") {

	
	$max = 25;
	if($prefs_nbr_page_versions != '') {
		$max = $prefs_nbr_page_versions;
	}
	
	$cnt_all_sql = "SELECT COUNT(*) AS 'nbr' FROM fc_pages_cache WHERE page_id_original = $editpage AND page_cache_type = 'history' ";
	$cnt_all = $db_content->query($cnt_all_sql)->fetch(PDO::FETCH_ASSOC);
	$delete_nbr = $cnt_all['nbr']-$max;

	
	$cache_result = $db_content->select("fc_pages_cache",
		[
		"page_id", "page_linkname", "page_title", "page_lastedit", "page_lastedit_from", "page_version"
		],[ 
		"AND" => [
			"page_id_original" => $editpage,
			"page_cache_type" => "history"
			],
		"ORDER" => ["page_id" => "DESC"]
	 ]);	
	
	$cnt_result = count($cache_result);
	
	echo '<hr>';
	echo '<div class="well well-sm">';
	echo '<div class="accordion" id="versionsToggle">';
	echo '<div class="accordion-group">';
	echo '<div class="accordion-heading">';
	echo '<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#showVersions">Versions ('.$cnt_result.')</a>';
	echo '</div>';
	
	echo '<div id="showVersions" class="accordion-body collapse in">';
	echo '<div class="accordion-inner">';
	echo '<div class="scroll-container">';
	echo '<table class="table table-condensed table-hover">';

	for($i=0;$i<$cnt_result;$i++) {
	
		$nbr = $i+1;
		$page_id = $cache_result[$i]['page_id'];

		
		if($i >= $max) {
			
			$del_sql = "DELETE FROM fc_pages_cache
									WHERE page_id IN (
										SELECT page_id
										FROM fc_pages_cache
										WHERE page_id_original = '$editpage'
										ORDER BY page_lastedit ASC
										LIMIT 1)";
			
			$db_content->query("$del_sql");
			continue;

		}
		
		
		
		$date = date("d.m.Y",$cache_result[$i]['page_lastedit']);
		$time = date("H:i:s",$cache_result[$i]['page_lastedit']);
		$yesterday = date('d.m.Y', time()-(60*60*24));
		$today = date('d.m.Y', time());
			
		if($date == "$today") {
			$setdate = $lang['date_today'];
		} elseif($date == "$yesterday") {
			$setdate = $lang['date_yesterday'];
		} else {
			$setdate = $date;
		}
		
		$edit_button = '<button class="btn btn-sm btn-fc w-100" name="editpage" value="'.$editpage.'" title="'.$lang['edit'].'">'.$icon['edit'].' '.$lang['edit'].'</button>';
			
		echo '<tr>';
		echo '<td>' . $cache_result[$i]['page_version'] . '</td>';
		echo '<td width="100">'.$setdate.'</td>';
		echo '<td width="100">'.$time.'</td>';
		echo '<td>' . $cache_result[$i]['page_title'] . '</td>';
		echo '<td>' . $cache_result[$i]['page_lastedit_from'] . '</td>';
		echo '<td width="150" align="right">';
		echo '<form action="?tn=pages&sub=edit" method="POST">';
		echo $edit_button;
		echo '<input type="hidden" name="restore_id" value="'.$page_id.'">';
		echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
		echo '</form>';
		echo '</td>';
		echo '</tr>';
	}
	
	echo '</div>';
	echo '</table>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</fieldset>';

}

?>