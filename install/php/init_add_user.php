<?php

if(!defined('INSTALLER')) {
	header("location:../login.php");
	die("PERMISSION DENIED!");
}

$checked_user_data = array();

if(isset($_POST['check_user_data'])) {
	
	if($_POST['username'] != '') {
		$_SESSION['temp_username'] = $_POST['username'];
	} else {
		$checked_user_data[] = 'error';
	}
	if($_POST['mail'] != '') {
		$_SESSION['temp_usermail'] = $_POST['mail'];
	} else {
		$checked_user_data[] = 'error';
	}
	if(strlen($_POST['psw']) >= 8) {
		$_SESSION['temp_userpsw'] = $_POST['psw'];
	} else {
		$checked_user_data[] = 'error';
	}
	
}

echo '<fieldset>';

echo '<legend>'.$lang['label_add_user'].'</legend>';
echo '<p class="lead">'.$lang['description_add_user'].'</p>';



echo '<form action="index.php" method="POST">';
echo '<div class="form-group">';
echo '<label>'.$lang['username'].' <small>(A-Za-z0-9)</small></label>';
echo '<input type="text" class="form-control" name="username" value="'.$_SESSION['temp_username'].'">';
echo '</div>';

echo '<div class="form-group">';
echo '<label>'.$lang['email'].'</label>';
echo '<input type="mail" class="form-control" name="mail" value="'.$_SESSION['temp_usermail'].'">';
echo '</div>';

echo '<div class="form-group">';
echo '<label>'.$lang['password'].'</label>';
echo '<input type="password" class="form-control" name="psw" value="'.$_SESSION['temp_userpsw'].'">';
echo '<small class="help-text">'.$lang['password_help_text'].'</small>';
echo '</div>';

echo '<hr>';

if((in_array('error', $checked_user_data) OR $_SESSION['temp_username'] == '')) {
	echo '<input type="submit" class="btn btn-info" name="check_user_data" value="'.$lang['btn_enter_user'].'">';
} else {
	echo '<input type="submit" class="btn btn-success" name="step3" value="'.$lang['next_step'].'">';
}



echo '</form>';



echo '</fieldset>';

?>