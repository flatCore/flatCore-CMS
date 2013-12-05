<?php

//prohibit unauthorized access
require("core/access.php");

$array_group_user = array();

$submit_button = "<input type='submit' class='btn btn-success' name='saveGroup' value='$lang[save]'>";
$delete_button = "";


/*
Update existing group
*/

if($_POST[updateGroup]) {

	
	$arr_update_incUser = $_POST[incUser];
	@sort($arr_update_incUser);
	$update_incUser = implode(" ", $arr_update_incUser);
	
	
	$dbh = new PDO("sqlite:".USER_DB);
	
	$sql = "UPDATE fc_groups
			SET group_name = :update_group_name,
				group_description = :update_group_description,
				group_user = :update_incUser
			WHERE group_id = :editgroup ";
			
	$sth = $dbh->prepare($sql);
	
	$sth->bindParam(':update_group_name', $_POST[group_name], PDO::PARAM_STR);
	$sth->bindParam(':update_group_description', $_POST[group_description], PDO::PARAM_STR);
	$sth->bindParam(':update_incUser', $update_incUser, PDO::PARAM_STR);
	$sth->bindParam(':editgroup', $_POST[editgroup], PDO::PARAM_INT);
			
	$cnt_changes = $sth->execute();
	
	$dbh = null;
	
	if($cnt_changes == TRUE) {
		$success_message = "$lang[db_changed]";
	} else {
		$error_message = "$lang[db_not_changed]";
	}


}


/*
save new group
*/

if($_POST[saveGroup]) {

	
	$arr_new_incUser = $_POST[incUser];
	
	if(is_array($arr_new_incUser)) {
		sort($arr_new_incUser);
		$new_incUser = implode(" ", $arr_new_incUser);
	} else {
		$new_incUser = "";
	}
	
	
	$dbh = new PDO("sqlite:".USER_DB);
	
	$sql = "INSERT INTO fc_groups (
				group_id , group_name , group_description , group_user
			) VALUES (
				NULL, :new_group_name, :new_group_description, :new_incUser ) ";
				
	$sth = $dbh->prepare($sql);
	
	$sth->bindParam(':new_group_name', $_POST[group_name], PDO::PARAM_STR);
	$sth->bindParam(':new_group_description', $_POST[group_description], PDO::PARAM_STR);
	$sth->bindParam(':new_incUser', $new_incUser, PDO::PARAM_STR);	
	
	$cnt_changes = $sth->execute();
	
	$dbh = null;
	
	if($cnt_changes == TRUE) {
		$success_message = "$lang[db_changed]";
	} else {
		$error_message = "$lang[db_not_changed]";
	}

}



/*
delete the selected group
*/


if($_POST[deleteGroup]) {

	$editgroup = (int) $_POST[editgroup];
	
	$dbh = new PDO("sqlite:".USER_DB);
	$sql = "DELETE FROM fc_groups WHERE group_id = $editgroup";
	$cnt_changes = $dbh->exec($sql);
	
	$show_data = false;
	
	if($cnt_changes > 0) {
		$success_message = "$lang[db_changed]";
	} else {
		$error_message = "$lang[db_not_changed]";
	}

}






//print message

if($success_message != ""){
	echo"<div class='alert alert-success'><p>$success_message</p></div>";
}

if($error_message != ""){
	echo"<div class='alert alert-error'><p>$error_message</p></div>";
}






/*
choose the group
<select>
*/

// connect to database
$dbh = new PDO("sqlite:".USER_DB);
$sql = "SELECT * FROM fc_groups ORDER BY group_id ASC";

$result = $dbh->query($sql);
$result= $result->fetchAll(PDO::FETCH_ASSOC);


$editgroup = (int) $_POST[editgroup];


echo"<fieldset>";
echo"<legend>$lang[choose_usergroup]</legend>";
echo"<form action='$_SERVER[PHP_SELF]?tn=user&sub=groups' class='form-inline' method='POST'>";
echo '<div class="form-group">';
echo"<select name='editgroup' class='form-control'>";

for($i=0;$i<count($result);$i++) {

	$group_id = $result[$i][group_id];
	$group_name = $result[$i][group_name];
	
	if($editgroup == $group_id) { $sel[$i] = "selected"; }
	
	echo"<option $sel[$i] value='$group_id'>$group_name</option>";

}

echo"</select> ";
echo '</div>';
echo" <input type='submit' class='btn' name='select_group' value='$lang[edit]'>";
echo"</form>";
echo"</fieldset>";


/*
show data of the selected group
*/

if(($editgroup) && ($show_data !== false)) {


$dbh = new PDO("sqlite:".USER_DB);

$sql =  "SELECT * FROM fc_groups WHERE group_id = $editgroup ";


$result = $dbh->query($sql);
$result= $result->fetch(PDO::FETCH_ASSOC);

foreach($result as $k => $v) {
   $$k = stripslashes($v);
}


$array_group_user = explode(" ", $group_user);

$submit_button = "<input type='submit' class='btn btn-success' name='updateGroup' value='$lang[update]'>";
$delete_button = "<input type='submit' class='btn btn-danger' name='deleteGroup' value='$lang[delete]' onclick=\"return confirm('$lang[confirm_delete_usergroup]')\">";
$hidden_field = "<input type='hidden' name='editgroup' value='$editgroup'>";

} else {
// no group is selected
unset($group_name,$group_description,$hidden_field);
}




/*
FORM // EDIT GROUPS
*/

echo"<fieldset>";
echo"<legend>Benutzergruppe bearbeiten</legend>";

echo"<form action='$_SERVER[PHP_SELF]?tn=user&sub=groups' method='POST' class=''>";


echo'<div class="row">';

echo"<div class='col-md-8'>";


echo '<label class="">Gruppenname</label>';
echo "<input type='text' class='form-control' name='group_name' value='$group_name'></p>";


echo"<label>Beschreibung</label>";
echo"<textarea class='mceEditor_small' rows='4' name='group_description'>$group_description</textarea></p>";

echo"</div>";
echo"<div class='col-md-4'>";

echo"<label>Benutzer hinzufügen/entfernen</label>";

echo"<div id='userlist'>";

$dbh = new PDO("sqlite:".USER_DB);

$sql = "SELECT * FROM fc_user ORDER BY user_nick ASC";

unset($result);
   foreach ($dbh->query($sql) as $row) {
     $result[] = $row;
   }


echo"<table class='table table-hover table-condensed'>";

for($i=0;$i<count($result);$i++) {

	if($result[$i][user_class] == "deleted") {
	continue;
	}
	
		$user_id = $result[$i][user_id];
		$user_nick = $result[$i][user_nick];
		$user_firstname = $result[$i][user_firstname];
		$user_lastname = $result[$i][user_lastname];
		
	if (in_array("$user_id", $array_group_user)) {
	    $checked = "checked";
	} else {
		$checked = "";
	}
		
	
	echo"<tr>";
	echo"	<td><label class='checkbox'><input type='checkbox' $checked name='incUser[]' value='$user_id'> $user_nick </label></td>
			<td>$user_firstname $user_lastname</td>";
	echo"</tr>\n";
} //eol $i

echo"</table>";


echo"</div>";

echo"</div>";

echo'</div>';

echo"<div class='formfooter clear'>";
echo"$hidden_field $delete_button $submit_button";
echo"</div>";
echo"</form>";

echo"</fieldset>";





?>