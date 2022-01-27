<?php

//prohibit unauthorized access
require 'core/access.php';




/**
 * Delete Custom Fields from fc_user
 * NO SQLITE SUPPORT FOR THE MOMENT
 */
 
if(isset($_POST['delete_field_user'])) {
		
	$del_field = strip_tags($_POST['del_field']);
	if(substr($del_field,0,7) == "custom_") {
		
		$sql = "ALTER TABLE fc_user DROP COLUMN $del_field";
		$cnt_changes = $db_user->query($sql);
				
		if($cnt_changes > 0) {
			$sys_message = "{OKAY} $lang[db_changed]";
			record_log("$_SESSION[user_nick]","delete column $del_field","0");
		} else {
			$sys_message = "{error} $lang[db_not_changed]";
		}
		print_sysmsg("$sys_message");
	}
}



/**
 * Delete Custom Fields from fc_pages
 * NO SQLITE SUPPORT FOR THE MOMENT
 */
 
if(isset($_POST['delete_field_pages'])) {
	$del_field = strip_tags($_POST['del_field']);
	
	if(substr($del_field,0,7) == "custom_") {

		$sql = "ALTER TABLE fc_pages DROP COLUMN $del_field";
		$db_content->query($sql);

		$sql = "ALTER TABLE fc_pages_cache DROP COLUMN $del_field";
		$db_content->query($sql);		
		
		if($cnt_changes > 0) {
			$sys_message = "{OKAY} $lang[db_changed]";
			record_log("$_SESSION[user_nick]","delete column $del_field","0");
		} else {
			$sys_message = "{error} $lang[db_not_changed]";
		}
		print_sysmsg("$sys_message");
	}
}




/**
 * Add new Custom Column
 * to table fc_pages
 */

if($_POST['add_field_pages']) {
	
	$col = clean_vars($_POST['field_name_pages']);
	if($col == "") {
		/* if there is no name given, we use the timestamp */
		$col = time();
	}
	
	switch($_POST['field_type_pages']) {
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

	$result = $db_content->select("fc_pages", "*");
	
	/* if not exists, create column */
	if(!array_key_exists("$new_col", $result)) {
	   	$sql = "ALTER TABLE fc_pages ADD $new_col LONGTEXT";
	   	$db_content->query($sql);
	   	$sql = "ALTER TABLE fc_pages_cache ADD $new_col LONGTEXT";
	   	$db_content->query($sql);
	   	
	   	record_log($_SESSION['user_nick'],"add custom column <i>$new_col</i>","0");
	   	print_sysmsg("{OKAY} $lang[db_changed]"); 	
   }
}



/**
 * Add new Custom Column
 * to table fc_user
 */

if($_POST['add_field_user']) {
	
	$col = clean_vars($_POST['field_name_user']);
	if($col == "") {
		/* if there is no name given, we use the timestamp */
		$col = time();
	}
	
	switch($_POST['field_type_user']) {
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
	
	$result = $db_user->select("fc_user", "*");
	
	/* if not exists, create column */
	if(!array_key_exists("$new_col", $result)) {

	   	$sql = "ALTER TABLE fc_user ADD $new_col LONGTEXT";
	   	$db_user->query($sql);
	   	
	   	record_log($_SESSION['user_nick'],"add custom column @fc_user <i>$new_col</i>","0");
	   	print_sysmsg("{OKAY} $lang[db_changed]"); 	
   }
	
	$dbh = null;
}


echo '<div class="alert alert-danger">' . $lang['delete_custom_field_desc'] . '</div>';


echo '<div class="row">';
echo '<div class="col-6">';

/* form - add col to fc_pages */

echo '<fieldset>';
echo '<legend><strong>'.$lang['page_customize'].'</strong> - '.$lang['add_custom_field'].'</legend>';

echo '<form action="acp.php?tn=system&sub=customize" method="POST">';

echo tpl_form_control_group('',$lang['custom_field_name'],"<input type='text' class='form-control' name='field_name_pages' value='$field_name'>");

$radio_field_type = "
			<label class='radio inline'><input type='radio' $sel1 name='field_type_pages' value='one'> &lt;input type=&quot;text&quot; ... </label>
			<label class='radio inline'><input type='radio' $sel2 name='field_type_pages' value='text'> &lt;textarea ... </label>
			<label class='radio inline'><input type='radio' $sel3 name='field_type_pages' value='wysiwyg'> &lt;textarea ... (WYSIWYG)</label>";

echo tpl_form_control_group('','',$radio_field_type);

echo '<hr>';

echo"<input type='submit' class='btn btn-save' name='add_field_pages' value='$lang[save]'>";
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';


echo '</form>';
echo '</fieldset>';


echo '</div>';
echo '<div class="col-6">';

/* show custom columns from fc_pages */

echo '<fieldset>';
echo '<legend>' . $lang['delete_custom_field'] . '</legend>';

$result = get_custom_fields();
$cnt_result = count($result);


if($cnt_result < 1) {
	echo '<div class="alert alert-info">' . $lang['no_custom_fields'] . '</div>';
} else {
	
	echo '<div class="scroll-container">';
	echo '<table class="table table-condensed">';
	
	for($i=0;$i<$cnt_result;$i++) {
		if(substr($result[$i],0,7) == "custom_") {
			
			$this_name = $result[$i];
			$this_name_smarty = '{$'.$this_name.'}';
			
			echo '<tr>';
			echo '<td>'.$this_name.'</td>';
			echo '<td><code>'.$this_name_smarty.'</code></td>';
			echo '<td>';
			echo '<form action="acp.php?tn=system&sub=customize" class="form-inline" method="POST">';
			echo '<input type="hidden" name="del_field" value="'.$result[$i].'">';
			echo '<button type="submit" class="btn btn-sm btn-fc w-100 text-danger" name="delete_field_pages">'.$icon['trash_alt'].'</button>';
			echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
			echo '</form>';
			echo '</td>';
			echo '</tr>';
		}
	}
	
	echo '</table>';
	echo '</div>';
	
}

echo '</fieldset>';

echo '</div>';
echo '</div>';


echo '<hr>';


echo '<div class="row">';
echo '<div class="col-6">';


/* form - add col to fc_user */

echo '<fieldset>';
echo '<legend><strong>'.$lang['customize_user'].'</strong> - '.$lang['add_custom_field'].'</legend>';

echo '<form action="acp.php?tn=system&sub=customize" method="POST" class="form-horizontal">';

echo tpl_form_control_group('',$lang['custom_field_name'],"<input type='text' class='form-control' name='field_name_user' value='$field_name'>");

$radio_field_type = "
			<label class='radio inline'><input type='radio' $sel1 name='field_type_user' value='one'> &lt;input type=&quot;text&quot; ... </label>
			<label class='radio inline'><input type='radio' $sel2 name='field_type_user' value='text'> &lt;textarea ... </label>
			<label class='radio inline'><input type='radio' $sel3 name='field_type_user' value='wysiwyg'> &lt;textarea ... (WYSIWYG)</label>";

echo tpl_form_control_group('','',$radio_field_type);

echo '<hr>';
echo '<input type="submit" class="btn btn-save" name="add_field_user" value="'.$lang['save'].'">';

echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';
echo '</fieldset>';


echo '</div>';
echo '<div class="col-6">';

/* show custom columns from fc_user */

echo '<fieldset>';
echo '<legend>' . $lang['delete_custom_field'] . '</legend>';

$result = get_custom_user_fields();
$cnt_result = count($result);

if($cnt_result < 1) {
	echo '<div class="alert alert-info">' . $lang['no_custom_fields'] . '</div>';
} else {

	echo '<div class="scroll-container">';
	echo '<table class="table table-condensed">';
	
	for($i=0;$i<$cnt_result;$i++) {
		if(substr($result[$i],0,7) == "custom_") {
			
			$this_name = $result[$i];
			$this_name_smarty = '{$'.$this_name.'}';
			
			echo '<tr>';
			echo '<td>'.$this_name.'</td>';
			echo '<td><code>'.$this_name_smarty.'</code></td>';
			echo '<td>';
			echo '<form action="acp.php?tn=system&sub=customize" class="form-inline" method="POST">';
			echo '<input type="hidden" name="del_field" value="'.$result[$i].'">';
			echo '<button type="submit" class="btn btn-sm btn-fc w-100 text-danger" name="delete_field_user">'.$icon['trash_alt'].'</button>';
			echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
			echo '</form>';
			echo '</td>';
			echo '</tr>';
		}
	}
	
	echo '</table>';
	echo '</div>';
}

echo '</fieldset>';



echo '</div>';
echo '</div>';



?>