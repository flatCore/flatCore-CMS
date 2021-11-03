<?php

//prohibit unauthorized access
require 'core/access.php';

$arr_lang = get_all_languages();

foreach($_POST as $key => $val) {
	$$key = @htmlspecialchars($val, ENT_QUOTES); 
}

/* update category */

if(isset($_POST['update_category'])) {
	
	$cat_name_clean = clean_filename($cat_name);

	$data = $db_content->update("fc_categories", [
			"cat_name" =>  $cat_name,
			"cat_lang" =>  $cat_lang,
			"cat_name_clean" =>  $cat_name_clean,
			"cat_sort" =>  $cat_sort,
			"cat_description" =>  $cat_description,
			"cat_thumbnail" =>  $cat_thumbnail
		], [
		"cat_id" => $editcat
		]);
		
	
}

/* new category */

if(isset($_POST['new_category'])) {
	
	$cat_name_clean = clean_filename($cat_name);
	
	$data = $db_content->insert("fc_categories", [
			"cat_name" =>  $cat_name,
			"cat_lang" =>  $cat_lang,
			"cat_name_clean" =>  $cat_name_clean,
			"cat_sort" =>  $cat_sort,
			"cat_description" =>  $cat_description,
			"cat_thumbnail" =>  $cat_thumbnail,
		]);
	
}

/* delete category */

if(isset($_POST['delete_category'])) {

	$delete_id = (int) $_POST['editcat'];

	$data = $db_content->delete("fc_categories", [
		"cat_id" => $delete_id
		]);
	
	unset($_REQUEST['editcat'],$cat_name,$cat_sort,$cat_description,$cat_thumbnail);
}


$submit_button = '<input type="submit" class="btn btn-save" name="new_category" value="'.$lang['save'].'">';
$delete_button = "";


if(isset($_POST['editcat']) && ($_POST['editcat'] != '')) {
	
	$editcat = (int) $_POST['editcat'];
	
	$submit_button = '<input type="submit" class="btn btn-save" name="update_category" value="'.$lang['update'].'">';
	$delete_button = "<input type='submit' class='btn btn-fc text-danger' name='delete_category' value='$lang[delete]' onclick=\"return confirm('$lang[confirm_delete_data]')\">";
	$hidden_field = "<input type='hidden' name='editcat' value='$editcat'>";
	
	$get_category = $db_content->get("fc_categories","*",[
		"AND" => [
			"cat_id" => "$editcat"
		]
	]);
	
	$cat_name = $get_category['cat_name'];
	$cat_sort = $get_category['cat_sort'];
	$cat_lang = $get_category['cat_lang'];
	$cat_thumbnail = $get_category['cat_thumbnail'];
	$cat_description = $get_category['cat_description'];
}


echo '<fieldset>';
echo '<legend>'.$lang['categories'].'</legend>';


echo '<div class="row">';
echo '<div class="col-md-8">';
// categories form

echo '<form action="?tn=system&sub=categories" method="POST">';

echo '<div class="row">';
echo '<div class="col-md-9">';

echo '<div class="form-group">';
echo '<label>'.$lang['category_name'].'</label>';
echo '<input type="text" class="form-control" name="cat_name" value="'.$cat_name.'">';
echo '</div>';

echo '</div>';
echo '<div class="col-md-3">';

echo '<div class="form-group">';
echo '<label>'.$lang['category_priority'].'</label>';
echo '<input type="text" class="form-control" name="cat_sort" value="'.$cat_sort.'">';
echo '</div>';

echo '</div>';
echo '</div>';


$images = fc_scandir_rec('../content/images');

/* avatar */
$choose_tmb = '<select class="form-control choose-thumb custom-select" name="cat_thumbnail">';
$choose_tmb .= '<option value="">'.$lang['no_image'].'</option>';
foreach($images as $img) {
	$img = str_replace('../content/', '/content/', $img);
	$selected = '';
	if($cat_thumbnail == $img) {$selected = 'selected';}
	$choose_tmb .= '<option '.$selected.' value='.$img.'>'.$img.'</option>';
}
$choose_tmb .= '</select>';

echo '<div class="row">';
echo '<div class="col-md-9">';

echo '<div class="form-group">';
echo '<label>'.$lang['category_thumbnail'].'</label>';
echo $choose_tmb;
echo '</div>';

echo '</div>';
echo '<div class="col-md-3">';

if($cat_lang == '' && $prefs_default_language != '') {
	$cat_lang = $prefs_default_language;
}

$select_cat_language  = '<select name="cat_lang" class="custom-select form-control">';
for($i=0;$i<count($arr_lang);$i++) {
	$lang_sign = $arr_lang[$i]['lang_sign'];
	$lang_desc = $arr_lang[$i]['lang_desc'];
	$lang_folder = $arr_lang[$i]['lang_folder'];
	$select_cat_language .= "<option value='$lang_folder'".($cat_lang == "$lang_folder" ? 'selected="selected"' :'').">$lang_sign</option>";	
}
$select_cat_language .= '</select>';


echo '<div class="form-group">';
echo '<label>'.$lang['f_page_language'].'</label>';
echo $select_cat_language;
echo '</div>';

echo '</div>';
echo '</div>';


echo '<div class="form-group">';
echo '<label>'.$lang['category_description'].'</label>';
echo "<textarea class='form-control' rows='8' name='cat_description'>$cat_description</textarea>";
echo '</div>';



echo"<div class='formfooter'>";
echo"$hidden_field $delete_button $submit_button";
echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo"</div>";

echo"</form>";



echo '</div>';
echo '<div class="col-md-4">';
// list categories

$all_categories = fc_get_categories();
$cnt_categories = count($all_categories);

foreach($all_categories as $cats) {
	
	echo '<div class="card mb-1 p-1">';
	echo '<div class="row no-gutters">';
	echo '<div class="col-md-3">';
	
	if($cats['cat_thumbnail'] != '') {
		echo '<img src="'.$cats['cat_thumbnail'].'" class="card-img">';
	} else {
		echo '<img src="images/no-image.png" class="card-img">';
	}
	echo '</div>';
	echo '<div class="col-md-9">';
	echo '<div class="card-body">';
	echo '<h5 class="card-title">'.$cats['cat_name'].' ('.$cats['cat_lang'].')</h5>';
	echo '<p class="card-text">'.$cats['cat_description'].'</p>';
	echo '<form action="?tn=system&sub=categories" method="POST">';
	echo '<button name="editcat" value='.$cats['cat_id'].'" class="btn btn-fc">'.$icon['edit'].' '.$lang['edit'].'</button>';
	echo $hidden_csrf_token;
	echo '</form>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';

}


//print_r($all_categories);

echo '</div>';
echo '</div>';

echo '</fieldset>';
?>