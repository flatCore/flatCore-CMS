<?php
//error_reporting(E_ALL ^E_NOTICE);
//prohibit unauthorized access
require 'core/access.php';
$system_snippets_str = "'footer_text','extra_content_text','agreement_text','account_confirm','account_confirm_mail','no_access'";
$system_snippets = explode(',',str_replace("'",'',$system_snippets_str));
$modus = 'new';


if(isset($_REQUEST['suggest_name'])) {
	$textlib_name = clean_filename($_REQUEST['suggest_name']);
}

if(isset($_REQUEST['type'])) {
	if($_REQUEST['type'] == '1') {
		$_SESSION['type'] = 'all';
	} else if($_REQUEST['type'] == '2') {
		$_SESSION['type'] = 'system';
	} else if($_REQUEST['type'] == '3') {
		$_SESSION['type'] = 'own';
	}
}

if(empty($_SESSION['type'])) {
	$_SESSION['type'] = 'all';
}

${'active_'.$_SESSION['type']} = 'active';


/**
 * delete snippet
 */

if(isset($_POST['delete_snippet'])) {

	$delete_snip_id = (int) $_POST['snip_id'];
	
	$cnt_changes=$db_content->delete("fc_textlib",[
			"textlib_id" => $delete_snip_id
		]);

	if(($cnt_changes->rowCount()) > 0){
		$sys_message = '{OKAY} '. $lang['db_changed'];
		record_log($_SESSION['user_nick'],"deleted snippet id: $delete_snip_id","10");
		$modus = 'new';
	} else {
		$sys_message = '{ERROR} ' . $lang['db_not_changed'];
	}
	
	print_sysmsg("$sys_message");

}


/* Save Textsnippet */
if(isset($_POST['save_snippet'])) {

	$snippet_name = clean_filename($_POST['snippet_name']);
	$timestamp =  time();
	

	$snippet_themes = explode('<|-|>', $_POST['select_template']);
	$snippet_theme = $snippet_themes[0];
	$snippet_template = $snippet_themes[1];
	
	if(count($_POST['picker1_images']) > 1) {
		
		$snippet_thumbnail = implode("<->", array_unique($_POST['picker1_images']));
	} else {
		$st = $_POST['picker1_images'];
		$snippet_thumbnail = $st[0].'<->';
	}
	
	if($snippet_name == '') {
		$snippet_name = date("Y_m_d_h_i",time());
	}
	
	/* labels */
	$arr_labels = $_POST['snippet_labels'];
	if(is_array($arr_labels)) {
		sort($arr_labels);
		$string_labels = implode(",", $arr_labels);
	} else {
		$string_labels = "";
	}	

	
	if($_POST['modus'] == 'update') {
		
		$snip_id = (int) $_POST['snip_id'];
		
		$data = $db_content->update("fc_textlib", [
			"textlib_content" =>  $_POST['textlib_content'],
			"textlib_name" => $snippet_name,
			"textlib_lang" => $_POST['sel_language'],
			"textlib_notes" => $_POST['textlib_notes'],
			"textlib_groups" => $_POST['snippet_groups'],
			"textlib_title" => $_POST['snippet_title'],
			"textlib_keywords" => $_POST['snippet_keywords'],
			"textlib_priority" => $_POST['snippet_priority'],
			"textlib_lastedit" => $timestamp,
			"textlib_lastedit_from" => $_SESSION['user_nick'],
			"textlib_template" => $snippet_template,
			"textlib_theme" => $snippet_theme,
			"textlib_images" => $snippet_thumbnail,
			"textlib_labels" => $string_labels,
			"textlib_classes" => $_POST['snippet_classes'],
			"textlib_permalink" => $_POST['snippet_permalink'],
			"textlib_permalink_title" => $_POST['snippet_permalink_title'],
			"textlib_permalink_name" => $_POST['snippet_permalink_name'],
			"textlib_permalink_classes" => $_POST['snippet_permalink_classes']
		], [
		"textlib_id" => $snip_id
		]);
	
	} else {
		
		
		$data = $db_content->insert("fc_textlib", [
			"textlib_content" =>  $_POST['textlib_content'],
			"textlib_name" => $snippet_name,
			"textlib_lang" => $_POST['sel_language'],
			"textlib_notes" => $_POST['textlib_notes'],
			"textlib_groups" => $_POST['snippet_groups'],
			"textlib_title" => $_POST['snippet_title'],
			"textlib_keywords" => $_POST['snippet_keywords'],
			"textlib_priority" => $_POST['snippet_priority'],
			"textlib_lastedit" => $timestamp,
			"textlib_lastedit_from" => $_SESSION['user_nick'],
			"textlib_template" => $snippet_template,
			"textlib_theme" => $snippet_theme,
			"textlib_images" => $snippet_thumbnail,
			"textlib_labels" => $string_labels,
			"textlib_classes" => $_POST['snippet_classes'],
			"textlib_permalink" => $_POST['snippet_permalink'],
			"textlib_permalink_title" => $_POST['snippet_permalink_title'],
			"textlib_permalink_name" => $_POST['snippet_permalink_name'],
			"textlib_permalink_classes" => $_POST['snippet_permalink_classes']
		]);
		
	}
	
	
	if($data->rowCount() > 0) {
		$sys_message = '{OKAY} '.$lang['db_changed'];
		record_log("$_SESSION[user_nick]","edit textlib <strong>$snippet_name</strong>","2");
	} else {
		$sys_message = '{ERROR} '.$lang['db_not_changed'];
	}
	
	print_sysmsg($sys_message);

} // eol save text



/**
 * get all saved snippets
 */


/* expand filter */
if(isset($_POST['snippet_filter']) && (trim($_POST['snippet_filter']) != '')) {
	$_SESSION['snippet_filter'] = $_SESSION['snippet_filter'] . ' ' . $_POST['snippet_filter'];
}

/* remove keyword from filter list */
if($_REQUEST['rm_keyword'] != "") {
	$all_snippet_filter = explode(" ", $_SESSION['snippet_filter']);
	unset($_SESSION['snippet_filter'],$f);
	foreach($all_snippet_filter as $f) {
		if($_REQUEST['rm_keyword'] == "$f") { continue; }
		if($f == "") { continue; }
		$_SESSION['snippet_filter'] .= "$f ";
	}
	unset($all_snippet_filter);
}

if($_SESSION['snippet_filter'] != "") {
	unset($all_snippet_filter);
	$btn_remove_keyword = '';
	$all_snippet_filter = explode(" ", $_SESSION['snippet_filter']);
	foreach($all_snippet_filter as $f) {
		if($_REQUEST['rm_keyword'] == "$f") { continue; }
		if($f == "") { continue; }
		$btn_remove_keyword .= '<a class="btn btn-fc btn-sm" href="acp.php?tn=pages&sub=snippets&rm_keyword='.$f.'">'.$icon['times_circle'].' '.$f.'</a> ';
		$set_snippet_keyword_filter .= "(textlib_name like '%$f%' OR textlib_title like '%$f%' OR textlib_keywords like '%$f%') AND";
	}
}
$set_snippet_keyword_filter = substr("$set_snippet_keyword_filter", 0, -4); // cut the last ' AND'

$snippet_lang_filter = "";
for($i=0;$i<count($arr_lang);$i++) {
	$lang_folder = $arr_lang[$i]['lang_folder'];
	if(strpos("$_SESSION[checked_lang_string]", "$lang_folder") !== false) {
		$snippet_lang_filter .= "textlib_lang = '$lang_folder' OR ";
	}
}
$snippet_lang_filter = substr("$snippet_lang_filter", 0, -3); // cut the last ' OR'


$snippet_label_filter = '';
$checked_labels_array = explode('-', $_SESSION['checked_label_str']);
for($i=0;$i<count($fc_labels);$i++) {
	$label = $fc_labels[$i]['label_id'];
	if(in_array($label, $checked_labels_array)) {
		$snippet_label_filter .= "textlib_labels LIKE '%,$label,%' OR textlib_labels LIKE '%,$label' OR textlib_labels LIKE '$label,%' OR textlib_labels = '$label' OR ";
	}
}
$snippet_label_filter = substr("$snippet_label_filter", 0, -3); // cut the last ' OR'

$filter_string = "WHERE textlib_id IS NOT NULL AND textlib_type NOT IN('shortcode')";

if($_SESSION['type'] == 'all') {
	$filter_type = '';
} else if($_SESSION['type'] == 'system') {
	$filter_type = "textlib_name IN($system_snippets_str)";
} else if($_SESSION['type'] == 'own') {
	$filter_type = "textlib_name NOT IN($system_snippets_str)";
}

if($filter_type != "") {
	$filter_string .= " AND ($filter_type) ";
}

if($set_snippet_keyword_filter != "") {
	$filter_string .= " AND $set_snippet_keyword_filter";
}

if($snippet_label_filter != "") {
	$filter_string .= " AND ($snippet_label_filter)";
}

if($snippet_lang_filter != "") {
	$filter_string .= " AND ($snippet_lang_filter)";
}


$sql_cnt = "SELECT count(*) AS 'cnt_all_snippets',
(SELECT count(*) FROM fc_textlib WHERE textlib_type NOT IN('shortcode') ) AS 'cnt_snippets',
(SELECT count(*) FROM fc_textlib WHERE textlib_name IN($system_snippets_str) ) AS 'cnt_system_snippets',
(SELECT count(*) FROM fc_textlib WHERE textlib_name NOT IN($system_snippets_str) AND (textlib_type NOT IN('shortcode')) ) AS 'cnt_custom_snippets',
(SELECT count(*) FROM fc_textlib $filter_string ) AS 'cnt_filter_snippets'
FROM fc_textlib";

$cnt = $db_content->query($sql_cnt)->fetch(PDO::FETCH_ASSOC);

$files_per_page = 50;
$show_numbers = 6;
$start = 0;
$disable_next_start = '';
$disable_prev_start = '';

if(isset($_GET['start'])) {
	$start = (int) $_GET['start'];
}

if($start<0) {
	$start = 0;
}

$next_start = $start+$files_per_page;
$prev_start = $start-$files_per_page;

if($start>($cnt['cnt_filter_snippets']-$files_per_page)) {
	$next_start = $start;
	$disable_next_start = 'disabled';
}

if($start < 1) {
	$disable_prev_start = 'disabled';
}


$sql = "SELECT * FROM fc_textlib $filter_string ORDER BY textlib_name ASC LIMIT $start,$files_per_page";

foreach($system_snippets as $snippet) {
	$snippet_exception[] = " textlib_name != '$snippet' ";
}

$snippets_list = $db_content->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$cnt_pages = ceil($cnt['cnt_filter_snippets']/$files_per_page);

$cnt_snippets = count($snippets_list);

$pag_backlink = '<a class="btn btn-fc '.$disable_prev_start.'" href="acp.php?tn=pages&sub=snippets&start='.$prev_start.'">'.$icon['angle_double_left'].'</a>';
$pag_forwardlink = '<a class="btn btn-fc '.$disable_next_start.'" href="acp.php?tn=pages&sub=snippets&start='.$next_start.'">'.$icon['angle_double_right'].'</a>';

unset($pag_string);
for($x=0;$x<$cnt_pages;$x++) {

	$aclass = "btn btn-fc";
	$page_start = $x*$files_per_page;
	$page_nbr = $x+1;
	
	if($page_start == $start) {
		$aclass = "btn btn-fc active";
		
		$pag_start = 	$x-($show_numbers/2);
		
		if($pag_start < 0) {
			$pag_start = 0;
		}
		
		$pag_end = 		$pag_start+$show_numbers;
		if($pag_end > $cnt_pages) {
			$pag_end = $cnt_pages;
		}
	}
	
	$a_pag_string[] = "<a class='$aclass' href='acp.php?tn=pages&sub=snippets&start=$page_start'>$page_nbr</a> ";

}

/**
 * open snippet
 */

if(((isset($_REQUEST['snip_id'])) OR ($modus == 'update')) AND (!isset($delete_snip_id)))  {

	include 'pages.snippets_form.php';
	
} else {
	
	/* list snippets */
	
	echo '<div class="app-container">';
	echo '<nav class="navbar navbar-expand-sm navbar-fc">';

	echo '<ul class="navbar-nav">';
	echo '<li class="nav-item"><a class="nav-link '.$active_all.'" href="?tn=pages&sub=snippets&type=1">Alle ('.$cnt['cnt_snippets'].')</a></li>';
	echo '<li class="nav-item"><a class="nav-link '.$active_system.'" href="?tn=pages&sub=snippets&type=2">System ('.$cnt['cnt_system_snippets'].')</a></li>';
	echo '<li class="nav-item mr-3"><a class="nav-link '.$active_own.'" href="?tn=pages&sub=snippets&type=3">Eigene ('.$cnt['cnt_custom_snippets'].')</a></li>';
	
	echo $lang_dropdown;
	echo $label_dropdown;
	
	echo '</ul>';

	
	echo '<a href="?tn=pages&sub=snippets&snip_id=n" class="nav-link text-success">'.$icon['plus'].' '.$lang['new'].'</a>';

	echo '<form action="acp.php?tn=pages&sub=snippets" method="POST" class="form-inline ms-auto dirtyignore">';
	echo '<div class="input-group">';
	echo '<span class="input-group-text">'.$icon['filter'].'</span>';
	echo '<input class="form-control" type="text" name="snippet_filter" value="" placeholder="Filter">';
	echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
	echo '</div>';
	echo '</form>';
	

	
	echo '</nav>';
	
		if($btn_remove_keyword != '') {
			echo '<div class="float-end">';
		echo '<p style="padding-top:5px;">'.$btn_remove_keyword.'</p>';
			echo '</div>';
	}
		
	echo '<div class="max-height-container">';
	echo '<div class="scroll-box">';
	
	echo '<table class="table table-hover table-striped table-sm mt-3">';
	
	echo '<thead><tr>';
	echo '<th>'.$lang['f_page_language'].'</th>';
	echo '<th>'.$lang['filename'].'</th>';
	echo '<th>'.$lang['label_title'].'/'.$lang['label_content'].'</th>';
	echo '<th>'.$lang['label_classes'].'</th>';
	echo '<th>'.$lang['labels'].'</th>';
	echo '<th>'.$lang['images'].'</th>';
	echo '<th>URL</th>';
	echo '<th>'.$lang['date_of_change'].'</th>';
	echo '<th></th>';
	echo '</tr></thead>';
	
	for($i=0;$i<$cnt_snippets;$i++) {
		$active_class = '';
		$get_snip_id = $snippets_list[$i]['textlib_id'];
		$get_snip_name = $snippets_list[$i]['textlib_name'];
		$get_snip_lang = $snippets_list[$i]['textlib_lang'];
		$get_snip_title = $snippets_list[$i]['textlib_title'];
		$get_snip_content = $snippets_list[$i]['textlib_content'];
		$get_snip_lastedit = $snippets_list[$i]['textlib_lastedit'];
		$get_snip_lastedit_from = $snippets_list[$i]['textlib_lastedit_from'];
		$get_snip_keywords = $snippets_list[$i]['textlib_keywords'];	
		$get_snip_labels = explode(',',$snippets_list[$i]['textlib_labels']);
		$get_snip_url = $snippets_list[$i]['textlib_permalink'];
		$get_snip_url_title = $snippets_list[$i]['textlib_permalink_title'];
		$get_snip_url_name = $snippets_list[$i]['textlib_permalink_name'];
		$get_snip_url_classes = $snippets_list[$i]['textlib_permalink_classes'];
		$get_snip_images = $snippets_list[$i]['textlib_images'];
		
		$get_snip_content = strip_tags($get_snip_content);
		if(strlen($get_snip_content) > 150) {
			$get_snip_content = substr($get_snip_content, 0, 100) . ' <small><i>(...)</i></small>';
		}
		
		$label = '';
		if($snippets_list[$i]['textlib_labels'] != '') {
			foreach($get_snip_labels as $snippet_label) {
				
				foreach($fc_labels as $l) {
					if($snippet_label == $l['label_id']) {
						$label_color = $l['label_color'];
						$label_title = $l['label_title'];
					}
				}
				
				$label .= '<span class="label-dot" style="background-color:'.$label_color.';" title="'.$label_title.'"></span>';
			}
		}
		
		$snippet_classes = explode(' ',$snippets_list[$i]['textlib_classes']);
		$class_badge = '';
		foreach($snippet_classes as $class) {
			$class_badge .= '<span class="badge badge-secondary">'.$class.'</span> ';
		}
		
		$kw_string = '';
		$snippet_keywords = explode(',',$get_snip_keywords);
		if(count($snippet_keywords) > 0) {
			$kw_string = '<form action="acp.php?tn=pages&sub=snippets" method="POST" class="form-inline">';
			foreach($snippet_keywords as $kw) {
				if($kw == '') {
					continue;
				}
				$kw_string .= ' <button type="submit" class="btn btn-fc btn-xs mr-1" name="snippet_filter" value="'.$kw.'">'.$kw.'</button> ';
			}
			$kw_string .= '</form>';
		}
		
		if(in_array($get_snip_name, $system_snippets)) {
			$show_snip_name = '<span>' . $get_snip_name.'</span>'.' <sup>'.$icon['cog'].'</sup>';
			$data_groups = '"system"';
		} else {
			$show_snip_name = '<span>'.$get_snip_name.'</span>';
			$data_groups = '';
		}
		
		$lang_thumb = '<img src="/lib/lang/'.$get_snip_lang.'/flag.png" width="20">';
		
		$snippet_images = explode('<->',$get_snip_images);
		
		echo '<tr>';
		echo '<td>'.$lang_thumb.'</td>';
		echo '<td nowrap>'.$show_snip_name.'</td>';
		echo '<td><strong>'.$get_snip_title.'</strong><br><small>'.$get_snip_content.'</small><br>'.$kw_string.'</td>';
		echo '<td>'.$class_badge.'</td>';
		echo '<td>'.$label.'</td>';
		echo '<td>';
		if(count($snippet_images) > 1) {
			$x=0;
			foreach($snippet_images as $img) {
				if(is_file("$img")) {
					$x++;
					echo '<a data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="<img src=\''.$img.'\'>">'.$icon['images'].'</a> ';
				}
				if($x>2) {
					echo '<small>(...)</small>';
					break;
				}
			}
		}
		echo '</td>';
		echo '<td>';
		if($get_snip_url != '') {
			echo '<a data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" title="'.$get_snip_url_title.'" data-bs-content="URL: '.$get_snip_url.'<br>Name: '.$get_snip_url_name.'<br>'.$lang['label_classes'].': '.$get_snip_url_classes.'">'.$icon['link'].'</a>';
		}
		echo '</td>';
		echo '<td nowrap><small>'.$icon['clock']. ' '.date('Y.m.d H:i:s',$get_snip_lastedit).'<br>'.$icon['user'].' '.$get_snip_lastedit_from.'</small></td>';
		echo '<td class="text-right">';
		echo '<div class="btn-group" role="group">';
		echo '<a href="acp.php?tn=pages&sub=snippets&snip_id='.$get_snip_id.'" class="btn btn-fc btn-sm text-success">'.$lang['edit'].'</a>';
		echo '<a href="acp.php?tn=pages&sub=snippets&snip_id='.$get_snip_id.'&duplicate=1" class="btn btn-fc btn-sm">'.$icon['copy'].'</a>';
		echo '</div>';
		echo '</td>';	
		echo '</tr>';
		
	}
	
	
	echo '</table>';
	
	echo '<div class="well well-sm text-center">';
	echo $pag_backlink .' ';
	foreach(range($pag_start, $pag_end) as $number) {
    echo $a_pag_string[$number];
	}
	echo ' '. $pag_forwardlink;
	echo '</div>'; //EOL PAGINATION
	
	echo '</div>';
	echo '</div>';

	echo '</div>'; // .app-container

	
}
?>