<?php

//prohibit unauthorized access
require 'core/access.php';
$system_snippets_str = "'footer_text', 'extra_content_text', 'agreement_text', 'account_confirm', 'account_confirm_mail', 'no_access'";
$system_snippets = explode(',',$system_snippets_str);
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

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "DELETE FROM fc_textlib WHERE textlib_id = $delete_snip_id";
	$cnt_changes = $dbh->exec($sql);

	if($cnt_changes > 0){
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

	// connect to database
	$db = new PDO("sqlite:".CONTENT_DB);
	
	$snippet_name = clean_filename($_POST['snippet_name']);
	$timestamp =  time();
	

	$snippet_themes = explode('<|-|>', $_POST['select_template']);
	$snippet_theme = $snippet_themes[0];
	$snippet_template = $snippet_themes[1];
	
	if(count($_POST['snippet_thumbnail']) > 1) {
		$snippet_thumbnail = implode("<->", $_POST['snippet_thumbnail']);
	} else {
		$st = $_POST['snippet_thumbnail'];
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
	
	if($_POST['snip_id'] != '') {
		
		$snip_id = (int) $_POST['snip_id'];
	
		$sql = "UPDATE fc_textlib
						SET textlib_content = :textlib_content, textlib_notes = :textlib_notes, textlib_groups = :textlib_groups,
								textlib_name = :textlib_name, textlib_title = :textlib_title, textlib_keywords = :textlib_keywords,
								textlib_lang = :textlib_lang, textlib_priority = :textlib_priority,
								textlib_lastedit = :textlib_lastedit, textlib_lastedit_from = :textlib_lastedit_from,
								textlib_template = :textlib_template, textlib_theme = :textlib_theme, textlib_images = :textlib_images,
								textlib_labels = :textlib_labels, textlib_classes = :textlib_classes, textlib_permalink = :textlib_permalink
						WHERE textlib_id = $snip_id";
	
	} else {
		
		$sql = "INSERT INTO fc_textlib (
							textlib_content, textlib_notes, textlib_groups, textlib_name, textlib_title, textlib_keywords, textlib_lang, textlib_priority,
							textlib_lastedit, textlib_lastedit_from, textlib_template, textlib_theme, textlib_images, textlib_labels,
							textlib_classes, textlib_permalink
						) VALUES (
							:textlib_content, :textlib_notes, :textlib_groups, :textlib_name, :textlib_title, :textlib_keywords, :textlib_lang, :textlib_priority,
							:textlib_lastedit, :textlib_lastedit_from, :textlib_template, :textlib_theme, :textlib_images, :textlib_labels,
							:textlib_classes, :textlib_permalink
						)";		
	}
	
	$sth = $db->prepare($sql);
	$sth->bindParam(':textlib_content', $_POST['textlib_content'], PDO::PARAM_STR);
	$sth->bindParam(':textlib_name', $snippet_name, PDO::PARAM_STR);
	$sth->bindParam(':textlib_lang', $_POST['sel_language'], PDO::PARAM_STR);
	$sth->bindParam(':textlib_notes', $_POST['textlib_notes'], PDO::PARAM_STR);
	$sth->bindParam(':textlib_keywords', $_POST['snippet_keywords'], PDO::PARAM_STR);
	$sth->bindParam(':textlib_title', $_POST['snippet_title'], PDO::PARAM_STR);
	$sth->bindParam(':textlib_groups', $_POST['snippet_groups'], PDO::PARAM_STR);
	$sth->bindParam(':textlib_classes', $_POST['snippet_classes'], PDO::PARAM_STR);
	$sth->bindParam(':textlib_permalink', $_POST['snippet_permalink'], PDO::PARAM_STR);
	$sth->bindParam(':textlib_template', $snippet_template, PDO::PARAM_STR);
	$sth->bindParam(':textlib_theme', $snippet_theme, PDO::PARAM_STR);
	$sth->bindParam(':textlib_images', $snippet_thumbnail, PDO::PARAM_STR);
	$sth->bindParam(':textlib_lastedit', $timestamp, PDO::PARAM_STR);
	$sth->bindParam(':textlib_lastedit_from', $_SESSION['user_nick'], PDO::PARAM_STR);
	$sth->bindParam(':textlib_labels', $string_labels, PDO::PARAM_STR);
	$sth->bindParam(':textlib_priority', $_POST['snippet_priority'], PDO::PARAM_INT);
	$cnt_changes = $sth->execute();
	
	$db = null;
	
	if($cnt_changes == TRUE) {
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

$sql_where = "WHERE textlib_id > 0";

if($_SESSION['type'] == 'all') {
	$sql_where = '';
} else if($_SESSION['type'] == 'system') {
	$sql_where = "WHERE textlib_name IN($system_snippets_str)";
} else if($_SESSION['type'] == 'own') {
	$sql_where = "WHERE textlib_name NOT IN($system_snippets_str)";
}

$dbh = new PDO("sqlite:".CONTENT_DB);

$sql = "SELECT * FROM fc_textlib $sql_where ORDER BY textlib_name ASC";

foreach($system_snippets as $snippet) {
	$snippet_exception[] = " textlib_name != '$snippet' ";
}

foreach ($dbh->query($sql) as $row) {
	$snippets_list[] = $row;
}

$dbh = null;

$cnt_snippets = count($snippets_list);


/**
 * open snippet
 */

if(((isset($_REQUEST['snip_id'])) OR ($modus == 'update')) AND (!isset($delete_snip_id)))  {

	include 'pages.snippets_form.php';
	
} else {
	
	/* list snippets */
	
	echo '<div class="app-container">';
	echo '<h3>' . $lang['snippets'] . '</h3>';
	
	echo '<div class="well well-sm">';
	echo '<div class="btn-group" role="group">';
	echo '<a class="btn btn-dark '.$active_all.'" href="?tn=pages&sub=snippets&type=1">Alle</a>';
	echo '<a class="btn btn-dark '.$active_system.'" href="?tn=pages&sub=snippets&type=2">System</a>';
	echo '<a class="btn btn-dark '.$active_own.'" href="?tn=pages&sub=snippets&type=3">Eigene</a>';
	echo '</div>';
	
	echo '<a href="?tn=pages&sub=snippets&snip_id=n" class="btn btn-dark text-success float-right">'.$icon['plus'].' '.$lang['new'].'</a>';
	
	echo '</div>';
	
	
	echo '<div class="max-height-container">';
	echo '<div class="scroll-box">';
	
	echo '<table class="table table-hover table-striped table-sm">';
	
	for($i=0;$i<$cnt_snippets;$i++) {
		$active_class = '';
		$get_snip_id = $snippets_list[$i]['textlib_id'];
		$get_snip_name = $snippets_list[$i]['textlib_name'];
		$get_snip_lang = $snippets_list[$i]['textlib_lang'];
		$get_snip_title = $snippets_list[$i]['textlib_title'];
		$get_snip_lastedit = $snippets_list[$i]['textlib_lastedit'];
		$get_snip_lastedit_from = $snippets_list[$i]['textlib_lastedit_from'];
		$get_snip_keywords = $snippets_list[$i]['textlib_keywords'];	
		$get_snip_labels = explode(',',$snippets_list[$i]['textlib_labels']);
		
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
		
		
		if(in_array($get_snip_name, $system_snippets)) {
			$show_snip_name = $icon['cogs']. ' ' . $get_snip_name;
			$data_groups = '"system"';
		} else {
			$show_snip_name = $get_snip_name;
			$data_groups = '';
		}
			
		unset($sel);
		if($snip_id == $get_snip_id) {
			$sel = "selected";
			$get_snip_name_editor = '[snippet]'.$get_snip_name.'[/snippet]';
			$get_snip_name_smarty = '{$fc_snippet_'.$get_snip_name.'}';
			$get_snip_name_smarty = str_replace('-', '_', $get_snip_name_smarty);
		}

		$lang_thumb = '<img src="/lib/lang/'.$get_snip_lang.'/flag.png" width="20">';
		
		echo '<tr>';
		echo '<td>'.$lang_thumb.'</td>';
		echo '<td>'.$show_snip_name.'</td>';
		echo '<td>'.$get_snip_title.'</td>';
		echo '<td><small>'.date('Y.m.d. H:i:s',$get_snip_lastedit).' '.$get_snip_lastedit_from.'</small></td>';
		echo '<td>'.$label.'</td>';
		echo '<td class="text-right"><a href="acp.php?tn=pages&sub=snippets&snip_id='.$get_snip_id.'" class="btn btn-dark btn-sm">'.$lang['edit'].'</a></td>';	
		echo '</tr>';
		
	}
	
	
	echo '</table>';
	
	echo '</div>';
	echo '</div>';
	
	
	
	echo '</div>';
	echo '</div>';
	echo '</div>'; // .app-container

	
}




?>