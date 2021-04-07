<?php

//prohibit unauthorized access
require 'core/access.php';

$array_group_user = array();

$submit_button = '<input type="submit" class="btn btn-save" name="saveGroup" value="'.$lang['save'].'">';
$delete_button = '';


/**
 * Update existing group
 */

if($_POST['updateGroup']) {
	
	$arr_update_incUser = $_POST['incUser'];
	@sort($arr_update_incUser);
	$update_incUser = implode(" ", $arr_update_incUser);
	
	$group_name = filter_var($_POST['group_name'], FILTER_SANITIZE_STRING);
	
	$update_group = $db_user->update("fc_groups", [
		"group_name" => $group_name,
		"group_description" => $_POST['group_description'],
		"group_user" => $update_incUser
	],[
		"group_id" => $_POST['editgroup']
	]);
	
	if(($update_group->rowCount()) > 0) {
		$success_message = $lang['db_changed'];
		record_log($_SESSION['user_nick'],"updated usergroup: $group_name","10");
	} else {
		$error_message = $lang['db_not_changed'];
	}

}


/**
 * save new group
 */

if($_POST['saveGroup']) {

	$arr_new_incUser = $_POST['incUser'];
	
	if(is_array($arr_new_incUser)) {
		sort($arr_new_incUser);
		$new_incUser = implode(" ", $arr_new_incUser);
	} else {
		$new_incUser = "";
	}
	
	$group_name = filter_var($_POST['group_name'], FILTER_SANITIZE_STRING);
		
	$db_user->insert("fc_groups", [
		"group_name" => $group_name,
		"group_description" => $_POST['group_description'],
		"group_user" => $new_incUser
	]);
	
	$group_id = $db_user->id();
	
	if($group_id > 0) {
		$success_message = $lang['db_changed'];
		record_log($_SESSION['user_nick'],"created usergroup: $group_name","10");
	} else {
		$error_message = $lang['db_not_changed'];
	}

}



/**
 * delete the selected group
 */

if($_POST['deleteGroup']) {
	
	$delete_group = $db_user->delete("fc_groups",[
		"group_id" => $_POST['editgroup']
	]);
	
	$show_data = false;
	
	if(($delete_group->rowCount()) > 0) {
		$success_message = $lang['db_changed'];
		record_log($_SESSION['user_nick'],"deleted usergroup id: $editgroup","10");
	} else {
		$error_message = $lang['db_not_changed'];
	}

}



//print message

if($success_message != ""){
	echo '<div class="alert alert-success"><p>'.$success_message.'</p></div>';
}

if($error_message != ""){
	echo '<div class="alert alert-danger"><p>'.$error_message.'</p></div>';
}



/**
 * choose the group
 * <select>
 */

$user_groups = $db_user->select("fc_groups","*");
$cnt_user_groups = count($user_groups);

$editgroup = (int) $_POST['editgroup'];


echo '<fieldset>';
echo '<legend>'.$lang['legend_choose_group'].'</legend>';
echo '<form action="acp.php?tn=user&sub=groups" method="POST">';

echo '<div class="row">';
echo '<div class="col-md-5">';

echo '<div class="form-group">';
echo '<select name="editgroup" class="form-control custom-select">';

for($i=0;$i<$cnt_user_groups;$i++) {

	$group_id = $user_groups[$i]['group_id'];
	$group_name = $user_groups[$i]['group_name'];
	
	if($editgroup == $group_id) { $sel[$i] = "selected"; }
	
	echo "<option $sel[$i] value='$group_id'>$group_name</option>";

}

echo '</select>';
echo '</div>';
echo '</div>';
echo '<div class="col-md-3">';
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '<input type="submit" class="btn btn-fc w-100" name="select_group" value="'.$lang['edit'].'">';
echo '</div>';
echo '</div>';
echo '</form>';
echo '</fieldset>';


/**
 * show data of the selected group
 */

if(($editgroup) && ($show_data !== false)) {

	$user_group = $db_user->get("fc_groups","*",["group_id" => $editgroup]);
		
	foreach($user_group as $k => $v) {
	  $$k = stripslashes($v);
	}

	$array_group_user = explode(" ", $group_user);

	$submit_button = '<input type="submit" class="btn btn-fc text-success" name="updateGroup" value="'.$lang['update'].'">';
	$delete_button = '<input type="submit" class="btn btn-fc text-danger" name="deleteGroup" value="'.$lang['delete'].'" onclick="return confirm(\''.$lang['confirm_delete_file'].'\')">';
	$hidden_field = '<input type="hidden" name="editgroup" value="'.$editgroup.'">';

} else {
	// no group is selected
	unset($group_name,$group_description,$hidden_field);
}




/**
 * FORM // EDIT GROUPS
 */

echo '<fieldset>';
echo '<legend>'.$lang['legend_groups_data'].'</legend>';

echo '<form action="acp.php?tn=user&sub=groups" method="POST">';

echo '<div class="row">';
echo '<div class="col-md-8">';

echo '<label class="">'.$lang['label_group_name'].'</label>';
echo '<input type="text" class="form-control" name="group_name" value="'.$group_name.'">';

echo '<label>'.$lang['label_group_description'].'</label>';
echo '<textarea class="mceEditor_small" rows="4" name="group_description">'.$group_description.'</textarea>';

echo '</div>';
echo '<div class="col-md-4">';

echo '<label>'.$lang['label_group_add_user'].'</label>';

echo '<div id="userlist">';
echo '<div class="scroll-container">';


$user = $db_user->select("fc_user","*");
$cnt_user = count($user);

echo '<table class="table table-hover table-sm">';

for($i=0;$i<$cnt_user;$i++) {

	if($result[$i]['user_class'] == "deleted") {
		continue;
	}
	
	$user_id = $user[$i]['user_id'];
	$user_nick = $user[$i]['user_nick'];
	$user_firstname = $user[$i]['user_firstname'];
	$user_lastname = $user[$i]['user_lastname'];
		
	if (in_array("$user_id", $array_group_user)) {
	   $checked = "checked";
	} else {
		$checked = "";
	}
	
	echo '<tr>';
	echo '<td>';
	echo tpl_checkbox('incUser[]',$user_id,"check_$user_nick",$user_nick,$checked);
	echo '</td>';
	echo '<td>'.$user_firstname.' '.$user_lastname.'</td>';
	echo '</tr>';
}

echo '</table>';

echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="well well-sm mt-3">';
echo "$hidden_field $delete_button $submit_button";
echo '<input  type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</div>';
echo '</form>';

echo '</fieldset>';

?>