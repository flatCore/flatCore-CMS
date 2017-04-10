<?php

//prohibit unauthorized access
require("core/access.php");

$system_snippets = array('footer_text', 'extra_content_text', 'agreement_text', 'account_confirm', 'account_confirm_mail', 'no_access');
$modus = 'new';


if(isset($_REQUEST['suggest_name'])) {
	$textlib_name = clean_filename($_REQUEST['suggest_name']);
}

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
	
	if($snippet_name == '') {
		$snippet_name = date("Y_m_d_h_i",time());
	}
	
	if($_POST['snip_id'] != '') {
		
		$snip_id = (int) $_POST['snip_id'];
	
		$sql = "UPDATE fc_textlib
						SET textlib_content = :textlib_content, textlib_notes = :textlib_notes, textlib_groups = :textlib_groups,
								textlib_name = :textlib_name, textlib_title = :textlib_title, textlib_keywords = :textlib_keywords,
								textlib_lang = :textlib_lang
						WHERE textlib_id = $snip_id";
	
	} else {
		
		$sql = "INSERT INTO fc_textlib (
							textlib_content, textlib_notes, textlib_groups, textlib_name, textlib_title, textlib_keywords, textlib_lang
						) VALUES (
							:textlib_content, :textlib_notes, :textlib_groups, :textlib_name, :textlib_title, :textlib_keywords, :textlib_lang
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
	$cnt_changes = $sth->execute();
	
	$db = null;
	
	if($cnt_changes == TRUE) {
		$sys_message = "{OKAY} $lang[db_changed]";
		record_log("$_SESSION[user_nick]","edit textlib <b>$snippet_title</b>","2");
	} else {
		$sys_message = "{ERROR} $lang[db_not_changed]";
	}
	
	print_sysmsg("$sys_message");

} // eol save text



/**
 * get all saved snippets
 */

$dbh = new PDO("sqlite:".CONTENT_DB);

$sql = "SELECT * FROM fc_textlib ORDER BY textlib_name ASC";

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

	if(!isset($snip_id)) {
		$snip_id = (int) $_REQUEST['snip_id'];
  }
    
	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "SELECT * FROM fc_textlib WHERE textlib_id = $snip_id ";

	$result = $dbh->query($sql);
	$result = $result->fetch(PDO::FETCH_ASSOC);

	$dbh = null;

	if(is_array($result)) {
		foreach($result as $k => $v) {
 				$$k = htmlspecialchars(stripslashes($v));
		}
	}
	$modus = 'update';
}

echo '<div class="app-container">';
echo '<h3>' . $lang['snippets'] . '</h3>';
echo '<div class="row">';
echo '<div class="col-md-3">';

echo '<div class="max-height-container">';
echo '<div class="scroll-box">';
echo '<div class="list-group">';

for($i=0;$i<$cnt_snippets;$i++) {
	$active_class = '';
	$get_snip_id = $snippets_list[$i]['textlib_id'];
	$get_snip_name = $snippets_list[$i]['textlib_name'];
	$get_snip_lang = $snippets_list[$i]['textlib_lang'];
	
	if(in_array($get_snip_name, $system_snippets)) {
		$show_snip_name = '<span class="glyphicon glyphicon-cog"></span> ' . $get_snip_name;
	} else {
		$show_snip_name = $get_snip_name;
	}
		
	unset($sel);
	if($snip_id == $get_snip_id) {
		$sel = "selected";
		$get_snip_name_editor = '[snippet]'.$get_snip_name.'[/snippet]';
		$get_snip_name_smarty = '{$fc_snippet_'.$get_snip_name.'}';
		$get_snip_name_smarty = str_replace('-', '_', $get_snip_name_smarty);
	}
	
	if($_REQUEST['snip_id'] == $get_snip_id) {
		$active_class = 'active';
	}
	
	echo '<a class="list-group-item '.$active_class.'" href="acp.php?tn=pages&sub=snippets&snip_id='.$get_snip_id.'">'.$show_snip_name.' <span class="badge">'.$get_snip_lang.'</span></a>';
}

echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';
echo '<div class="col-md-9">';

echo "<form action='$_SERVER[PHP_SELF]?tn=pages&sub=snippets' method='POST'>";




echo '<div class="row">';
echo '<div class="col-md-9">';

echo '<textarea class="form-control mceEditor switchEditor" id="textEditor" name="textlib_content">'.$textlib_content.'</textarea>';
echo '<input type="hidden" name="text" value="'.$text.'">';

if($get_snip_name_editor != '') {
	echo '<hr><div class="form-group">';
	echo '<label>Snippet</label>';
	echo '<input type="text" class="form-control" placeholder="[snippet]...[/snippet]" value="'.$get_snip_name_editor.'" readonly>';
	echo '</div>';
}

echo '</div>';
echo '<div class="col-md-3">';
/* info col */
echo '<div class="well well-sm">';
echo '<div class="form-group">';
echo '<div class="btn-group btn-group-justified" data-toggle="buttons">';
echo '<label class="btn btn-sm btn-default"><input type="radio" name="optEditor" value="optE1"> WYSIWYG</label>';
echo '<label class="btn btn-sm btn-default"><input type="radio" name="optEditor" value="optE2"> Text</label>';
echo '<label class="btn btn-sm btn-default"><input type="radio" name="optEditor" value="optE3"> Code</label>';
echo '</div>';
echo '</div>';

echo '<div class="form-group">';
echo '<label>'.$lang['filename'].' <small>(a-z,0-9)</small></label>';
echo '<input class="form-control" type="text" name="snippet_name" value="'.$textlib_name.'">';
echo '</div>';


$select_textlib_language  = '<select name="sel_language" class="form-control">';
for($i=0;$i<count($arr_lang);$i++) {
	$lang_sign = $arr_lang[$i]['lang_sign'];
	$lang_desc = $arr_lang[$i]['lang_desc'];
	$lang_folder = $arr_lang[$i]['lang_folder'];
	$select_textlib_language .= "<option value='$lang_folder'".($textlib_lang == "$lang_folder" ? 'selected="selected"' :'').">$lang_sign</option>";	
}
$select_textlib_language .= '</select>';

echo '<div class="form-group">';
echo '<label>'.$lang['f_page_language'].'</label>';
echo $select_textlib_language;
echo '</div>';

echo '<div class="form-group">';
echo '<label>'.$lang['label_title'].'</label>';
echo '<input class="form-control" type="text" name="snippet_title" value="'.$textlib_title.'">';
echo '</div>';

echo '<div class="form-group">';
echo '<label>'.$lang['label_keywords'].'</label>';
echo '<input class="form-control" type="text" name="snippet_keywords" value="'.$textlib_keywords.'" data-role="tagsinput" />';
echo '</div>';

echo '<div class="form-group">';
echo '<label>'.$lang['label_groups'].'</label>';
echo '<input class="form-control" type="text" name="snippet_groups" value="'.$textlib_groups.'" />';
echo '</div>';

echo '<div class="alert alert-info" style="padding:2px 3px;">';
echo '<strong>'.$lang['label_notes'].':</strong>';
echo '<textarea class="masked-textarea" name="textlib_notes" rows="5">'.$textlib_notes.'</textarea>';
echo '</div>';

echo '<div class="formfooter">';
if($modus == 'new') {
	echo '<input type="submit" name="save_snippet" class="btn btn-success btn-block" value="'.$lang['save'].'">';
} else {
	echo '<input type="hidden" name="snip_id" value="'.$snip_id.'">';
	echo '<input type="submit" name="save_snippet" class="btn btn-success btn-block" value="'.$lang['update'].'"> ';
	echo '<a class="btn btn-default btn-block" href="acp.php?tn=pages&sub=snippets">'.$lang['discard_changes'].'</a>';
	echo '<input type="submit" name="delete_snippet" class="btn btn-danger btn-sm btn-block" value="'.$lang['delete'].'" onclick="return confirm(\''.$lang['confirm_delete_data'].'\')">';
}
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</div>';


echo '</div>';
/* end info col */
echo '</div>';
echo '</div>';


echo '</form>';


echo '</div>';
echo '</div>';
echo '</div>'; // .app-container

?>