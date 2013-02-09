<?php

//prohibit unauthorized access
require("core/access.php");


$modus = "new";

/* delete snippet
---------------------------------------*/

if($_POST[delete_snippet]) {

	$delete_snip_id = (int) $_POST[snip_id];

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = "DELETE FROM fc_textlib
			   WHERE textlib_id = $delete_snip_id";
  $cnt_changes = $dbh->exec($sql);

	if($cnt_changes > 0){
		$sys_message = "{OKAY} $lang[db_changed]";
		record_log("$_SESSION[user_nick]","deleted snippet id: $delete_snip_id","0");
		$modus = "new";
	} else {
		$sys_message = "{ERROR} $lang[db_not_changed]";
	}
	
	print_sysmsg("$sys_message");

}



/**
 * save new snippet
 */
 
if($_POST[save_snippet]) {

	$pdo_fields = array(
		'textlib_id' => 'NULL',
		'textlib_name' => 'STR',
		'textlib_content' => 'STR'
	);

	$snippet_title = clean_filename($_POST[snippet_title]);

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_insert_str($pdo_fields,"fc_textlib");
	$sth = $dbh->prepare($sql);
	
	generate_bindParam_str($pdo_fields,$sth);
	$sth->bindParam(':textlib_name', $snippet_title, PDO::PARAM_STR);
	
	$cnt_changes = $sth->execute();

	$dbh = null;

	if($cnt_changes == TRUE) {
		$sys_message = "{OKAY} $lang[db_changed]";
		record_log("$_SESSION[user_nick]","new snippet <i>$snippet_title</i>","0");
		$modus = "update";
	} else {
		$sys_message = "{ERROR} $lang[db_not_changed]";
	}
	
	print_sysmsg("$sys_message");

}





/**
 * update snippet
 */

if($_POST[update_snippet]) {

	$pdo_fields = array(
		'textlib_name' => 'STR',
		'textlib_content' => 'STR'
	);
	
	$snip_id = (int) $_POST[snip_id];
	$snippet_title = clean_filename($_POST[snippet_title]);

	$dbh = new PDO("sqlite:".CONTENT_DB);
	$sql = generate_sql_update_str($pdo_fields,"fc_textlib","WHERE textlib_id = $snip_id");
	$sth = $dbh->prepare($sql);

	$sth->bindParam(':textlib_name', $snippet_title, PDO::PARAM_STR);
	$sth->bindParam(':textlib_content', $_POST[textlib_content], PDO::PARAM_STR);
	
	$cnt_changes = $sth->execute();

	$dbh = null;
	

	if($cnt_changes == TRUE){
		$sys_message = "{OKAY} $lang[db_changed]";
		record_log("$_SESSION[user_nick]","edit snippets <i>$snippet_title</i>","0");
		$modus = "update";
	} else {
		$sys_message = "{ERROR} $lang[db_not_changed] (ID: $_POST[snip_id])";
	}
	
	print_sysmsg("$sys_message");


} // eo update snippet







/**
 * get all saved snippets with id > 7
 * the first 7 snippets are reserved by the system
 */

$dbh = new PDO("sqlite:".CONTENT_DB);

$sql = "SELECT textlib_id, textlib_name FROM fc_textlib WHERE textlib_id > '6' ";

foreach ($dbh->query($sql) as $row) {
     $snippets_list[] = $row;
   }

$dbh = null;

$cnt_snippets = count($snippets_list);

/* eo get all saved snippets */




/* open snippet
---------------------------------------*/

if((($_POST[snip_id]) OR ($modus == "update")) AND (!isset($delete_snip_id)))  {

	if(!isset($snip_id)) {
		$snip_id = (int) $_POST[snip_id];
    }
    
		$dbh = new PDO("sqlite:".CONTENT_DB);
		$sql = "SELECT * FROM fc_textlib WHERE textlib_id = $snip_id ";

		$result = $dbh->query($sql);
		$result = $result->fetch(PDO::FETCH_ASSOC);

		$dbh = null;

		if(is_array($result)) {
			foreach($result as $k => $v) {
   				$$k = stripslashes($v);
			}
		}
	$modus = "update";
}

/* EO open snippets */


show_editor_switch($tn,$sub);

echo '<div class="row-fluid"><div class="span12">';

echo '<fieldset>';
echo "<legend>$lang[snippets]</legend>";
echo "<form action='$_SERVER[PHP_SELF]?tn=pages&sub=snippets' method='POST' name='sel_snippet' class='form-horizontal'>";

echo '<select name="snip_id">';

for($i=0;$i<$cnt_snippets;$i++) {
	$get_snip_id = $snippets_list[$i][textlib_id];
	$get_snip_name = $snippets_list[$i][textlib_name];
	
		unset($sel);
		if($snip_id == "$get_snip_id") {
			$sel = "selected";
		}
	
	echo "<option value='$get_snip_id' $sel>$get_snip_name</option>";
} // eo $i


echo '</select> ';

echo "<input type='submit' class='btn' name='sel_snippet' value='$lang[edit]'>";

echo '</form>';
echo '</fieldset>';



echo '<fieldset>';
echo "<legend>$lang[tab_content]</legend>";
echo "<form action='$_SERVER[PHP_SELF]?tn=pages&sub=snippets' class='form-horizontal' method='POST'>";

echo '<div class="control-group">';
echo "<label class='control-label'>$lang[filename]</label>";
echo "<div class='controls'><input class='span5' type='text' name='snippet_title' value='$textlib_name'></div>";
echo '</div>';

echo '<div class="control-group">';
echo "<label class='control-label'>$lang[tab_content]</label>";
echo '<div class="controls">';
echo "<textarea class='$editor_class span12' id='textEditor' name='textlib_content'>$textlib_content</textarea>";
echo "<input type='hidden' name='text' value='$text'>";
echo '</div>';
echo '</div>';

if($modus == "new") {
	echo "<div class='formfooter'><input type='submit' name='save_snippet' class='btn btn-success' value='$lang[save]'></div>";
} else {
	echo "<input type='hidden' name='snip_id' value='$snip_id'>";
	echo '<div class="formfooter">';
	echo "<input type='submit' name='delete_snippet' class='btn btn-danger' value='$lang[delete]'> ";
	echo "<input type='submit' name='update_snippet' class='btn btn-success' value='$lang[update]'>";
	echo '</div>';
}

echo"</form>";
echo"</fieldset>";

echo '</div></div>';


?>