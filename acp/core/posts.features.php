<?php
	
//prohibit unauthorized access
require 'core/access.php';



/**
 * write data 
 * update or new entry
 */
if(isset($_POST['send_data'])) {

	$lastedit = time();
	$feature_title = fc_return_clean_value($_POST['feature_title']);
	$feature_text = $_POST['feature_text'];
	$feature_priority = (int) $_POST['feature_priority'];
	$feature_lang = $_POST['feature_lang'];

	if(is_numeric($_POST['send_data'])) {
		$edit_id = (int) $_POST['send_data'];
		
		$db_content->update("fc_textlib",[
			"textlib_title" => $feature_title,
			"textlib_content" => $feature_text,
			"textlib_priority" => $feature_priority,
			"textlib_lastedit" => $lastedit,
			"textlib_lang" => $feature_lang
			],[
			"AND" => [
				"textlib_type" => "post_feature",
				"textlib_id" => "$edit_id"
			]
		]);		
	} else {
		
		$db_content->insert("fc_textlib", [
			"textlib_title" => $feature_title,
			"textlib_content" => $feature_text,
			"textlib_priority" => $feature_priority,
			"textlib_lastedit" => $lastedit,
			"textlib_lang" => $feature_lang,
			"textlib_type" => 'post_feature'
		]);
		
		$edit_id = $db_content->id();
		
	}
	
}


/* show the form */

$edit_form_tpl = file_get_contents('templates/post_features_form.tpl');
$show_form = false;

if(isset($_GET) && $_GET['edit'] == 'new') {
	$show_form = true;
	$mode = 'new';
	$btn_send = '<button class="btn btn-success" name="send_data" value="new">'.$lang['save'].'</button>';
}

if(isset($_POST['edit']) OR is_numeric($edit_id)) {
	$show_form = true;
	$mode = 'edit';
	
	if($edit_id == '') {
		$edit_id = (int) $_POST['edit'];
	}
	
	$snippet_data = $db_content->get("fc_textlib","*",[

		"AND" => [
			"textlib_type" => "post_feature",
			"textlib_id" => "$edit_id"
		]
	]);
	
	$feature_title = html_entity_decode($snippet_data['textlib_title']);
	$feature_text = $snippet_data['textlib_content'];
	$feature_priority = $snippet_data['textlib_priority'];
	$btn_send = '<button class="btn btn-success" name="send_data" value="'.$edit_id.'">'.$lang['update'].'</button>';
}

if($show_form == true) {

	$select_lang  = '<select name="feature_lang" class="custom-select form-control">';
	foreach($lang_codes as $lang_code) {
		$select_lang .= "<option value='$lang_code'".($feature_lang == "$lang_code" ? 'selected="selected"' :'').">$lang_code</option>";	
	}
	$select_lang .= '</select>';
	
	$edit_form_tpl = str_replace('{feature_title}',$feature_title, $edit_form_tpl);
	$edit_form_tpl = str_replace('{feature_text}',$feature_text, $edit_form_tpl);
	$edit_form_tpl = str_replace('{feature_priority}',$feature_priority, $edit_form_tpl);
	$edit_form_tpl = str_replace('{select_lang}',$select_lang, $edit_form_tpl);
	
	$edit_form_tpl = str_replace('{label_language}',$lang['label_language'], $edit_form_tpl);
	$edit_form_tpl = str_replace('{label_title}',$lang['label_title'], $edit_form_tpl);
	$edit_form_tpl = str_replace('{label_text}',$lang['label_text'], $edit_form_tpl);
	$edit_form_tpl = str_replace('{label_priority}',$lang['label_priority'], $edit_form_tpl);
	
	$edit_form_tpl = str_replace('{hidden_csrf}',$hidden_csrf_token, $edit_form_tpl);
	$edit_form_tpl = str_replace('{btn_send_form}',$btn_send, $edit_form_tpl);	
	echo $edit_form_tpl;
}



/**
 * list entries
 */

$posts_features = fc_get_posts_features();
$cnt_posts_features = count($posts_features);

//print_r($posts_features);

echo '<div class="row">';
echo '<div class="col-md-9">';

echo '<table class="table table-sm">';
echo '<tr>';
echo '<td>'.$lang['label_priority'].'</td>';
echo '<td>'.$lang['label_language'].'</td>';
echo '<td>'.$lang['label_text'].'</td>';
echo '<td></td>';
echo '</tr>';
for($i=0;$i<$cnt_posts_features;$i++) {
	echo '<tr>';
	echo '<td>'.$posts_features[$i]['textlib_priority'].'</td>';
	echo '<td>'.$posts_features[$i]['textlib_lang'].'</td>';
	echo '<td><strong>'.$posts_features[$i]['textlib_title'].'</strong><br>'.$posts_features[$i]['textlib_content'].'</td>';
	echo '<td class="text-end">';
	echo '<form action="?tn=posts&sub=features" method="POST">';
	echo '<button type="submit" class="btn btn-fc text-success" name="edit" value="'.$posts_features[$i]['textlib_id'].'">'.$icon['edit'].'</button>';
	echo '<button type="submit" class="btn btn-fc text-danger" name="delete" value="'.$posts_features[$i]['textlib_id'].'">'.$icon['trash_alt'].'</button>';
	echo $hidden_csrf_token;
	echo '</form>';
	echo'</td>';
	echo '</tr>';
}

echo '</table>';

echo '</div>';
echo '<div class="col-md-3">';

echo '<a href="?tn=posts&sub=features&edit=new" class="btn btn-success w-100">'.$lang['new'].'</a>';

echo '</div>';
echo '</div>';



?>