<?php

//prohibit unauthorized access
require("core/access.php");

/* save upload preferences */
if(isset($_POST['update_posts'])) {
	
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


echo '<form action="?tn=system&sub=posts" method="POST">';

echo'<fieldset>';
echo'<legend>'.$lang['label_entries'].'</legend>';
echo '<div class="form-group">';
echo '<label>'.$lang['label_entries_per_page'].'</label>';
echo '<input type="text" class="form-control" name="prefs_posts_entries_per_page" value="'.$prefs_posts_entries_per_page.'">';
echo '</div>';
echo '</fieldset>';


echo'<fieldset>';
echo'<legend>'.$lang['label_images'].'</legend>';
echo '<div class="form-group">';
echo '<label>'.$lang['label_images_prefix'].'</label>
			<input type="text" class="form-control" name="prefs_posts_images_prefix" value="'.$prefs_posts_images_prefix.'">
			</div>';
$all_images = fc_get_all_images();
echo '<div class="form-group">';
echo '<label>'.$lang['label_default_image'].'</label>';
				
echo '<select class="form-control custom-select" name="prefs_posts_default_banner">';
echo '<option value="use_standard">'.$lang['use_standard'].'</option>';

if($prefs_posts_default_banner == 'without_image') { $sel_without_image = 'selected'; }
echo '<option value="without_image" '.$sel_without_image.'>'.$lang['dont_use_an_image'].'</option>';
foreach ($all_images as $img) {
	unset($sel);
	if($prefs_posts_default_banner == $img) {
		$sel = "selected";
	}
	echo "<option $sel value='$img'>$img</option>";
}
				
echo '</select>';
				
echo '</div>';
echo '</fieldset>';




/* URL and Permalinks */

echo '<fieldset>';
echo '<legend>URL</legend>';

if($prefs_posts_url_pattern == "by_date") {
	$select_modus_date = "checked";
} else {
	$select_modus_title = "checked";
}

echo '<div class="form-check">
				<input class="form-check-input" type="radio" name="prefs_posts_url_pattern" value="by_date" id="pattern_date" '.$select_modus_date.'>
				<label for="pattern_date">' . $lang['url_by_date'] . '</label>
	 		</div>';
echo '<div class="form-check">
				<input class="form-check-input" type="radio" name="prefs_posts_url_pattern" value="by_filename" id="pattern_title" '.$select_modus_title.'>
				<label for="pattern_title">' . $lang['url_by_title'] . '</label>
	 		</div>';

echo '</fieldset>';



/* events */

echo '<fieldset>';
echo '<legend>'.$lang['post_type_event'].'</legend>';
echo '<div class="form-group">
				<label>' . $lang['label_event_time_offset'] . '</label>
				<input type="text" class="form-control" name="prefs_posts_event_time_offset" value="'.$prefs_posts_event_time_offset.'">
				<small class="form-text text-muted">'.$lang['event_time_offset_help_text'].'</small>
			</div>';


$sel_guestlist1 = '';
$sel_guestlist2 = '';
$sel_guestlist3 = '';

if($prefs_posts_default_guestlist == 1 OR $prefs_posts_default_guestlist == '') {
	$sel_guestlist1 = 'selected';
} else if($prefs_posts_default_guestlist == 2) {
	$sel_guestlist2 = 'selected';
} else if($prefs_posts_default_guestlist == 3) {
	$sel_guestlist3 = 'selected';
}

echo '<div class="form-group">';
echo '<label>' . $lang['label_guestlist'] . '</label>';
echo '<select class="form-control custom-select" name="prefs_posts_default_guestlist">';
echo '<option value="1" '.$sel_guestlist1.'>'.$lang['label_guestlist_deactivate'].'</option>';
echo '<option value="2" '.$sel_guestlist2.'>'.$lang['label_guestlist_for_registered'].'</option>';
echo '<option value="3" '.$sel_guestlist3.'>'.$lang['label_guestlist_for_everybody'].'</option>';
echo '</select>';
echo '</div>';		
	
echo'</fieldset>';

/* votings */
$sel_votings1 = '';
$sel_votings2 = '';
$sel_votings3 = '';

if($prefs_posts_default_votings == 1 OR $prefs_posts_default_votings == '') {
	$sel_votings1 = 'selected';
} else if($prefs_posts_default_votings == 2) {
	$sel_votings2 = 'selected';
} else if($prefs_posts_default_votings == 3) {
	$sel_votings3 = 'selected';
}

echo '<fieldset>';
echo '<legend>'.$lang['label_votings'].'</legend>';
echo '<select class="form-control custom-select" name="prefs_posts_default_votings">';
echo '<option value="1" '.$sel_votings1.'>'.$lang['label_votings_off'].'</option>';
echo '<option value="2" '.$sel_votings2.'>'.$lang['label_votings_on_registered'].'</option>';
echo '<option value="3" '.$sel_votings3.'>'.$lang['label_votings_on_global'].'</option>';
echo '</select>';
echo'</fieldset>';




echo '<input type="submit" class="btn btn-save" name="update_posts" value="'.$lang['update'].'">';
echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';

echo '</form>';


?>