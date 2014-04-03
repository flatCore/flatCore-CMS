<?php

//prohibit unauthorized access
require("core/access.php");

$system_snippets = array('footer_text', 'extra_content_text', 'agreement_text', 'account_confirm', 'account_confirm_mail', 'no_access');
$modus = 'new';


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



/**
 * save new snippet
 */
 
if(isset($_POST['save_snippet'])) {

	$pdo_fields = array(
		'textlib_id' => 'NULL',
		'textlib_name' => 'STR',
		'textlib_content' => 'STR'
	);

	$snippet_title = clean_filename($_POST['snippet_title']);

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_insert_str($pdo_fields,"fc_textlib");
	$sth = $dbh->prepare($sql);
	
	generate_bindParam_str($pdo_fields,$sth);
	$sth->bindParam(':textlib_name', $snippet_title, PDO::PARAM_STR);
	
	$cnt_changes = $sth->execute();

	$dbh = null;

	if($cnt_changes == TRUE) {
		$sys_message = '{OKAY} ' . $lang['db_changed'];
		record_log($_SESSION['user_nick'],"new snippet <i>$snippet_title</i>","2");
		$modus = 'update';
	} else {
		$sys_message = '{ERROR} ' . $lang['db_not_changed'];
	}
	
	print_sysmsg("$sys_message");

}


/**
 * update snippet
 */

if(isset($_POST['update_snippet'])) {

	$pdo_fields = array(
		'textlib_name' => 'STR',
		'textlib_content' => 'STR'
	);
	
	$snip_id = (int) $_POST['snip_id'];
	$snippet_title = clean_filename($_POST['snippet_title']);

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_update_str($pdo_fields,"fc_textlib","WHERE textlib_id = $snip_id");
	$sth = $dbh->prepare($sql);

	$sth->bindParam(':textlib_name', $snippet_title, PDO::PARAM_STR);
	$sth->bindParam(':textlib_content', $_POST[textlib_content], PDO::PARAM_STR);
	
	$cnt_changes = $sth->execute();

	$dbh = null;
	

	if($cnt_changes == TRUE){
		$sys_message = '{OKAY} ' . $lang['db_changed'];
		record_log($_SESSION['user_nick'],"edit snippets <i>$snippet_title</i>","2");
		$modus = 'update';
	} else {
		$sys_message = '{ERROR} ' . $lang['db_not_changed'] . " (ID: $snip_id)";
	}
	
	print_sysmsg("$sys_message");


} // eo update snippet



/**
 * get all saved snippets except $system_snippets
 */

$dbh = new PDO("sqlite:".CONTENT_DB);

$sql = "SELECT * FROM fc_textlib";

foreach($system_snippets as $snippet) {
	$snippet_exception[] = " textlib_name != '$snippet' ";
}

$sql .= ' WHERE ' . implode(' AND ', $snippet_exception);


foreach ($dbh->query($sql) as $row) {
     $snippets_list[] = $row;
   }

$dbh = null;

$cnt_snippets = count($snippets_list);


/**
 * open snippet
 */

if(((isset($_POST['snip_id'])) OR ($modus == 'update')) AND (!isset($delete_snip_id)))  {

	if(!isset($snip_id)) {
		$snip_id = (int) $_POST['snip_id'];
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


show_editor_switch($tn,$sub);

echo '<fieldset>';
echo '<legend>' . $lang['snippets'] . '</legend>';
echo "<form action='$_SERVER[PHP_SELF]?tn=pages&sub=snippets' method='POST' name='sel_snippet' class='form-inline'>";

echo '<div class="form-group">';
echo '<select name="snip_id" class="form-control">';

for($i=0;$i<$cnt_snippets;$i++) {
	$get_snip_id = $snippets_list[$i]['textlib_id'];
	$get_snip_name = $snippets_list[$i]['textlib_name'];
		
	unset($sel);
	if($snip_id == $get_snip_id) {
		$sel = "selected";
		$get_snip_name_editor = '[snippet]'.$get_snip_name.'[/snippet]';
		$get_snip_name_smarty = '{$fc_snippet_'.$get_snip_name.'}';
		$get_snip_name_smarty = str_replace('-', '_', $get_snip_name_smarty);
	}
	
	echo "<option value='$get_snip_id' $sel>$get_snip_name</option>";
}

echo '</select> ';
echo '</div>';

echo ' <input type="submit" class="btn btn-default" name="sel_snippet" value="'.$lang['edit'].'">';

echo '</form>';
echo '</fieldset>';


echo '<fieldset>';
echo '<legend>'.$lang['tab_content'].'</legend>';
echo "<form action='$_SERVER[PHP_SELF]?tn=pages&sub=snippets' method='POST'>";

echo '<div class="row">';
echo '<div class="col-md-6">';

echo '<div class="form-group">';
echo '<label>'.$lang['filename'].'</label>';
echo '<input class="form-control" type="text" name="snippet_title" value="'.$textlib_name.'">';
echo '</div>';

echo '</div>';

if(isset($_POST['snip_id'])) {
	echo '<div class="col-md-6">';
	echo '<ul class="list-group">';
	echo '<li class="list-group-item"><span class="badge">Editor</span>'.$get_snip_name_editor.'</li>';
	echo '<li class="list-group-item"><span class="badge">Smarty</span>'.$get_snip_name_smarty.'</li>';
	echo '</ul>';
	echo '</div>';
}

echo '</div>';

echo '<div class="form-group">';
echo '<label>'.$lang['tab_content'].'</label>';
echo '<textarea class="'.$editor_class.' form-control" id="textEditor" name="textlib_content">'.$textlib_content.'</textarea>';
echo '<input type="hidden" name="text" value="'.$text.'">';
echo '</div>';

echo '<div class="formfooter">';
if($modus == 'new') {
	echo '<input type="submit" name="save_snippet" class="btn btn-success" value="'.$lang['save'].'">';
} else {
	echo '<input type="hidden" name="snip_id" value="'.$snip_id.'">';
	echo '<input type="submit" name="delete_snippet" class="btn btn-danger" value="'.$lang['delete'].'" onclick="return confirm(\''.$lang['confirm_delete_data'].'\')"> ';
	echo '<input type="submit" name="update_snippet" class="btn btn-success" value="'.$lang['update'].'">';
}

echo '</div>';

echo '</form>';
echo '</fieldset>';

?>