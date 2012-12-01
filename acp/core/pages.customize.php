<?php

/**
 * Add and remove custom fields to the to the table "fc_pages"
 *
 * @author	Patrick Konstandin
 * @since		29.05.2012
 * @todo		add workaround for the missing SQLite-Feature DROP COLUMN
 * @todo		add custom columns to the cache-versions of pages
 */

//prohibit unauthorized access
require("core/access.php");


/**
 * Delete Custom Fields
 * NO SQLITE SUPPORT FOR THE MOMENT
 */
 
if($_POST[delete_field]) {
	$del_field = strip_tags($_POST[del_field]);
	
	if(substr($del_field,0,7) == "custom_") {
				
		$dbh = new PDO("sqlite:".CONTENT_DB);
		$sql = "ALTER TABLE fc_pages DROP COLUMN $del_field";
		$cnt_changes = $dbh->exec($sql);
		$dbh = null;
		
		if($cnt_changes > 0) {
			$sys_message = "{OKAY} Feld wurde gelöscht";
			record_log("$_SESSION[user_nick]","delete column $del_field","0");
		} else {
			$sys_message = "{error} Feld $del_field wurde nicht gelöscht";
		}
		
		print_sysmsg("$sys_message");
		
	}
	
	
	
}



/**
 * Add new Custom Column
 */

if($_POST[add_field]) {
	
	$col = clean_vars($_POST[field_name]);
	
	if($col == "") {
		/* if there is no name given, we use the timestamp */
		$col = time();
	}
	
	
	switch($_POST[field_type]) {
		case 'one':
			$type = "one";
			break;
		
		case 'text':
			$type = "text";
			break;
			
		case 'wysiwyg':
			$type = "wysiwyg";
			break;
		
		default:
			$type = "one"; 
	}
	



	$new_col = "custom_" . $type . "_" . $col;
	
	$dbh = new PDO("sqlite:".CONTENT_DB);
	
	$sql = "SELECT * FROM fc_pages";
	
	$result = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
	
	/* if not exists, create column */
	if(!array_key_exists("$new_col", $result)) {
	   	$sql = "ALTER TABLE fc_pages ADD $new_col TEXT";
	   	$dbh->exec($sql);
	   	
	   	$sql = "ALTER TABLE fc_pages_cache ADD $new_col TEXT";
	   	$dbh->exec($sql);
	   	
	   	record_log("$_SESSION[user_nick]","add custom column <i>$new_col</i>","0");  	
   }
	
	$dbh = null;
}



echo '<fieldset>';
echo '<legend>' . $lang[add_custom_field] . '</legend>';


echo '<form action="acp.php?tn=pages&sub=customize" method="POST">';

echo"<div class='form-line form-line-last'>
			<label>$lang[custom_field_name]</label>";

echo"<div class='form-controls'>
		<input type='text' class='input300' name='field_name' value='$field_name'>
		<ul class='unstyled'>
			<li><input type='radio' $sel1 name='field_type' value='one'> Einzeilig</li>
			<li><input type='radio' $sel2 name='field_type' value='text'> Mehrzeilig</li>
			<li><input type='radio' $sel3 name='field_type' value='wysiwyg'> Mehrzeilig (WYSIWYG)</li>
		</ul>
		</div>";
		
echo"</div>";


echo"<div class='formfooter'>";
echo"<input type='submit' class='btn btn-success' name='add_field' value='$lang[save]'>";
echo"</div>";

echo '</form>';

echo '</fieldset>';







/**
 * Show custom columns
 */


echo '<fieldset>';
echo '<legend>' . $lang[delete_custom_field] . '</legend>';

echo '<p>' . $lang[delete_custom_field_desc] . '</p>';

echo '<form action="acp.php?tn=pages&sub=customize" method="POST">';


$result = get_custom_fields();
$cnt_result = count($result);


if($cnt_result < 1) {
	echo '<p>' . $lang[no_custom_fields] . '</p>';
} else {


echo"<div class='form-line'>
			<label>$lang[custom_field_name]</label>";
echo"<div class='form-controls'>";

	echo '<select name="del_field">';
	for($i=0;$i<$cnt_result;$i++) {
		if(substr($result[$i],0,7) == "custom_") {
			echo "<option value='$result[$i]'>" . $result[$i] . "</option>";
		}
	}
	echo '</select>';


echo '</div>';

//submit form to save data
echo"<div class='formfooter'>";
echo"<input type='submit' class='btn btn-danger' name='delete_field' value='$lang[delete]'>";
echo"</div>";


}


echo '</form>';

echo '</fieldset>';





?>