<?php

//prohibit unauthorized access
require("core/access.php");

foreach($_POST as $key => $val) {
	$$key = @htmlspecialchars($val, ENT_QUOTES); 
}


/* save upload preferences */
if(isset($_POST['update_comments'])) {
	
	foreach($_POST as $key => $val) {
		$data[htmlentities($key)] = htmlentities($val);
	}
	fc_write_option($data,'fc');
}


if(isset($_POST)) {
	/* read the preferences again */
	$fc_get_preferences = fc_get_preferences();
	
	foreach($fc_get_preferences as $k => $v) {
		$key = $fc_get_preferences[$k]['option_key'];
		$value = $fc_get_preferences[$k]['option_value'];
		$fc_preferences[$key] = $value;
	}
	
	foreach($fc_preferences as $k => $v) {
	   $$k = stripslashes($v);
	}
}


echo '<form action="?tn=system&sub=comments" method="POST">';




/* mode */

echo '<fieldset>';
echo '<legend>'.$lang['label_comment_mode'].'</legend>';

if($prefs_comments_mode == 1) {
	$select_mode_1 = "checked";
} else if($prefs_comments_mode == 2) {
	$select_mode_2 = "checked";
} else {
	$select_mode_3 = "checked";
}

echo '<div class="form-check">
				<input class="form-check-input" type="radio" name="prefs_comments_mode" value="1" id="mode_1" '.$select_mode_1.'>
				<label for="mode_1">' . $lang['prefs_comments_mode_1'] . '</label>
	 		</div>';
echo '<div class="form-check">
				<input class="form-check-input" type="radio" name="prefs_comments_mode" value="2" id="mode_2" '.$select_mode_2.'>
				<label for="mode_2">' . $lang['prefs_comments_mode_2'] . '</label>
	 		</div>';
echo '<div class="form-check">
				<input class="form-check-input" type="radio" name="prefs_comments_mode" value="3" id="mode_3" '.$select_mode_3.'>
				<label for="mode_3">' . $lang['prefs_comments_mode_3'] . '</label>
	 		</div>';

echo '</fieldset>';


/* prefs_comments_authorization */

echo '<fieldset>';
echo '<legend>'.$lang['label_comment_auth'].'</legend>';

if($prefs_comments_authorization == 1) {
	$select_auth_1 = "checked";
} else if($prefs_comments_authorization == 2) {
	$select_auth_2 = "checked";
} else {
	$select_auth_3 = "checked";
}

echo '<div class="form-check">
				<input class="form-check-input" type="radio" name="prefs_comments_authorization" value="1" id="auth_1" '.$select_auth_1.'>
				<label for="auth_1">' . $lang['prefs_comments_auth_1'] . '</label>
	 		</div>';
echo '<div class="form-check">
				<input class="form-check-input" type="radio" name="prefs_comments_authorization" value="2" id="auth_2" '.$select_auth_2.'>
				<label for="auth_2">' . $lang['prefs_comments_auth_2'] . '</label>
	 		</div>';
echo '<div class="form-check">
				<input class="form-check-input" type="radio" name="prefs_comments_authorization" value="3" id="auth_3" '.$select_auth_3.'>
				<label for="auth_3">' . $lang['prefs_comments_auth_3'] . '</label>
	 		</div>';

echo '</fieldset>';


echo'<fieldset>';

echo'<legend>'.$lang['label_comment_auto'].'</legend>';
echo '<div class="form-group">';
echo '<label>'.$lang['prefs_comments_autoclose_time'].'</label>';
echo '<input type="text" class="form-control" name="prefs_comments_autoclose" value="'.$prefs_comments_autoclose.'">';
echo '</div>';

echo '<div class="form-group">';
echo '<label>'.$lang['label_comments_max_entries'].'</label>';
echo '<input type="text" class="form-control" name="prefs_comments_max_entries" value="'.$prefs_comments_max_entries.'">';
echo '</div>';

echo '<div class="form-group">';
echo '<label>'.$lang['label_comments_max_level'].'</label>';
echo '<input type="text" class="form-control" name="prefs_comments_max_level" value="'.$prefs_comments_max_level.'">';
echo '</div>';

echo '</fieldset>';



echo '<input type="submit" class="btn btn-save" name="update_comments" value="'.$lang['update'].'">';
echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';

echo '</form>';


?>