<?php

error_reporting(E_ALL ^E_ALL);
	
//prohibit unauthorized access
require 'core/access.php';

/* set modus */

if((isset($_REQUEST['post_id'])) && is_numeric($_REQUEST['post_id'])) {
	
	$post_id = (int) $_REQUEST['post_id'];
	$modus = 'update';
	$post_data = fc_get_post_data($post_id);
	$submit_btn = '<input type="submit" class="btn btn-save btn-block" name="save_post" value="'.$lang['update'].'">';
	
} else {
	$post_id = '';
	$modus = 'new';
	$submit_btn = '<input type="submit" class="btn btn-save btn-block" name="save_post" value="'.$lang['save'].'">';

}


/* save or update post data */

if(isset($_POST['save_post']) OR isset($_POST['del_tmb']) OR isset($_POST['sort_tmb'])) {
	
	foreach($_POST as $key => $val) {
		$$key = @htmlspecialchars($val, ENT_QUOTES); 
	}
	
	$post_releasedate = time();
	$post_lastedit = time();
	$post_lastedit_from = $_SESSION['user_nick'];
	$post_priority = (int) $_POST['post_priority'];
	
	if($_POST['post_date'] == "") {
		$_POST['post_date'] = time();
	}
		
	if($_POST['post_releasedate'] != "") {
		$post_releasedate = strtotime($_POST['post_releasedate']);
	}
	
	if($_POST['event_start'] != "") {
		$event_start = strtotime($_POST['event_start']);
	}
	
	if($_POST['event_end'] != "") {
		$event_end = strtotime($_POST['event_end']);
		if($event_end < $event_start) {
			$event_end = $event_start;
		}
	}
	
	$post_event_startdate = $event_start;
	$post_event_enddate = $event_end;
	
	$clean_title = clean_filename($_POST['post_title']);
	$post_date_year = date("Y",$post_releasedate);
	$post_date_month = date("m",$post_releasedate);
	$post_date_day = date("d",$post_releasedate);


	if($_POST['post_slug'] == "") {
		$post_slug = "$post_date_year/$post_date_month/$post_date_day/$clean_title/";
	}

	$post_lang = @implode("<->", $_POST['post_languages']);
	$post_categories = @implode("<->", $_POST['post_categories']);
	
	$post_images_string = @implode("<->", $_POST['post_images']);
	$post_images_string = "<->$post_images_string<->";
	$post_images = $post_images_string;
	
	$product_price_net = str_replace('.', '', $_POST['post_product_price_net']);
	$product_price_net = str_replace(',', '.', $product_price_net);
	
	/* gallery thumbnails */
	if($_POST['del_tmb'] != '') {
		$del_tmb = $_POST['del_tmb'];
		$del_img = str_replace('_tmb','_img',$del_tmb);
		unlink($del_tmb);
		unlink($del_img);
	}
	
	if($_POST['sort_tmb'] != '') {
		fc_rename_gallery_image($_POST['sort_tmb']);
	}
	
	/* todo: we have to build the rss url */
	

	/* save or update data */
	
	/* get all $cols */
	require '../install/contents/fc_posts.php';
	// build sql string -> f.e. "post_releasedate" => $post_releasedate,
	foreach($cols as $k => $v) {
		if($k == 'post_id') {continue;}
  	$inputs[$k] = $$k;
	}
	
	if($modus == "update")	{
		$db_posts->update("fc_posts", $inputs, [
			"post_id" => $post_id
		]);
	} else {
		$db_posts->insert("fc_posts", $inputs);
		$post_id = $db_posts->id();
		$modus = 'update';
		$submit_btn = '<input type="submit" class="btn btn-save btn-block" name="save_post" value="'.$lang['update'].'">';
	}
	$post_data = fc_get_post_data($post_id);
}




/* language */
$arr_lang = get_all_languages();
for($i=0;$i<count($arr_lang);$i++) {
	$lang_folder = $arr_lang[$i]['lang_folder'];
	
	if(strpos($post_data['post_lang'], "$lang_folder") !== false) {
		$checked_lang = "checked";
	} else {
		$checked_lang = "";
	}
	
	if($post_data['post_lang'] == "" AND $lang_folder == "$_SESSION[lang]") {
		$checked_lang = "checked";
	}
	
	$checkboxes_lang .= '<div class="form-check form-check-inline">';
	$checkboxes_lang .= '<input class="form-check-input" id="'.$lang_folder.'" type="checkbox" name="post_languages[]" value="'.$lang_folder.'" '.$checked_lang.'>';
	$checkboxes_lang .= '<label class="form-check-label" for="'.$lang_folder.'">'.$lang_folder.'</label>';
	$checkboxes_lang .= '</div>';
}

/* categories */

$cats = fc_get_categories();
for($i=0;$i<count($cats);$i++) {
	$category = $cats[$i]['cat_name'];
	$array_categories = explode("<->", $post_data['post_categories']);
	$checked = "";
	if(in_array($cats[$i]['cat_id'], $array_categories)) {
	    $checked = "checked";
	}
	$checkboxes_cat .= '<div class="form-check">';
	$checkboxes_cat .= '<input class="form-check-input" id="cat'.$i.'" type="checkbox" name="post_categories[]" value="'.$cats[$i]['cat_id'].'" '.$checked.'>';
	$checkboxes_cat .= '<label class="form-check-label" for="cat'.$i.'">'.$category.'</label>';
	$checkboxes_cat .= '</div>';
}


/* release date */
if($post_data['post_releasedate'] > 0) {
	$post_releasedate = date('Y-m-d H:i:s', $post_data['post_releasedate']);
} else {
	$post_releasedate = date('Y-m-d H:i:s', time());
}


/* event dates */
if($post_data['post_event_startdate'] > 0) {
	$post_event_startdate = date('Y-m-d H:i:s', $post_data['post_event_startdate']);
} else {
	$post_event_startdate = date('Y-m-d H:i:s', time());
}

if($post_data['post_event_enddate'] > 0) {
	$post_event_enddate = date('Y-m-d H:i:s', $post_data['post_event_enddate']);
} else {
	$post_event_enddate = date('Y-m-d H:i:s', time());
}


/* priority */
$select_priority = "<select name='post_priority' class='form-control custom-select'>";
for($i=1;$i<11;$i++) {
	$option_add = '';
	$sel_prio = '';
	if($i == 1) {
		$option_add = ' ('.$lang['label_priority_bottom'].')';
	}
	if($i == 10) {
		$option_add = ' ('.$lang['label_priority_top'].')';
	}
	if($post_data['post_priority'] == $i) {
		$sel_prio = 'selected';
	}
	$select_priority .= '<option value="'.$i.'" '.$sel_prio.'>'.$i.' '.$option_add.'</option>';
}
$select_priority .= '</select>';


/* fix post on top */
if($post_data['post_fixed'] == 'fixed') {
	$checked_fixed = 'checked';
}
$checkbox_fixed  = '<div class="form-check">';
$checkbox_fixed .= '<input class="form-check-input" id="fix" type="checkbox" name="post_fixed" value="fixed" '.$checked_fixed.'>';
$checkbox_fixed .= '<label class="form-check-label" for="fix">'.$lang['label_fixed'].'</label>';
$checkbox_fixed .= '</div>';


/* image widget */

$images = fc_scandir_rec('../'.FC_CONTENT_DIR.'/images');

foreach($images as $img) {
	$filemtime = date ("Y", filemtime("$img"));
	$all_images[] = array('name' => $img, 'dateY' => $filemtime);
}

foreach ($all_images as $key => $row) {
	$date[$key]  = $row['dateY'];
  $name[$key] = $row['name'];
}

/* we sort the images from new to old and from a to z */
array_multisort($date, SORT_DESC, $name, SORT_ASC, $all_images);
$array_images = explode("<->", $post_data['post_images']);
$choose_images = fc_select_img_widget($all_images,$array_images,$prefs_posts_images_prefix);

/* status | draft or published */
if($post_data['post_status'] == "draft") {
	$sel_status_draft = "selected";
} else {
	$sel_status_published = "selected";
}
$select_status = "<select name='post_status' class='form-control custom-select'>";
if($_SESSION['drm_can_publish'] == "true") {
	$select_status .= '<option value="2" '.$sel_status_draft.'>'.$lang['status_draft'].'</option>';
	$select_status .= '<option value="1" '.$sel_status_published.'>'.$lang['status_public'].'</option>';
} else {
	/* user can not publish */
	$select_status .= '<option value="draft" selected>'.$lang['status_draft'].'</option>';
}
$select_status .= '</select>';




/* RSS */
if($post_data['post_rss'] == "on") {
	$sel1 = "selected";
} else {
	$sel2 = "selected";
}
$select_rss = "<select name='post_rss' class='form-control custom-select'>";
$select_rss .= '<option value="on" '.$sel1.'>'.$lang['yes'].'</option>';
$select_rss .= '<option value="off" '.$sel2.'>'.$lang['no'].'</option>';
$select_rss .=	'</select>';



/* products */

/* select tax */

$get_tax = 0;
if($post_data['post_product_tax'] == '1') {
	$sel_tax_1 = 'selected';
	$get_tax = $fc_preferences['posts_products_default_tax'];
} else if($post_data['post_product_tax'] == '2') {
	$sel_tax_2 = 'selected';
	$get_tax = $fc_preferences['posts_products_tax_alt1'];
} else if($post_data['post_product_tax'] == '3') {
	$sel_tax_3 = 'selected';
	$get_tax = $fc_preferences['posts_products_tax_alt2'];
}

$select_tax = "<select name='post_product_tax' class='form-control custom-select' id='tax'>";
$select_tax .= '<option value="1" '.$sel_tax_1.'>'.$fc_preferences['posts_products_default_tax'].'</option>';
$select_tax .= '<option value="2" '.$sel_tax_2.'>'.$fc_preferences['posts_products_tax_alt1'].'</option>';
$select_tax .= '<option value="3" '.$sel_tax_3.'>'.$fc_preferences['posts_products_tax_alt2'].'</option>';
$select_tax .= '</select>';

/* add text snippet to prices */

$snippet_select_pricelist = '<select class="form-control custom-select" name="post_product_textlib_price">';
$snippet_select_pricelist .= '<option value="no_snippet">'.$lang['product_no_snippet'].'</option>';

$snippets_price_list = $db_content->select("fc_textlib", "*", [
	"textlib_name[~]" => "%post_price%"
]);

foreach($snippets_price_list as $snippet) {
	$selected = "";
	if($snippet['textlib_name'] == $post_data['product_textlib_price']) {
		$selected = 'selected';
	}
	$snippet_select_pricelist .= '<option '.$selected.' value='.$snippet['textlib_name'].'>'.$snippet['textlib_name']. ' - ' .$snippet['textlib_title'].'</option>';
}
$snippet_select_pricelist .= '</select>';


/* add text snippet to text */

$snippet_select_text = '<select class="form-control custom-select" name="post_product_textlib_content">';
$snippet_select_text .= '<option value="no_snippet">'.$lang['product_no_snippet'].'</option>';
$snippets_text_list = $db_content->select("fc_textlib", "*", [
	"textlib_name[~]" => "%post_text%"
]);
foreach($snippets_text_list as $snippet) {
	$selected = "";
	if($snippet['textlib_name'] == $post_data['post_product_textlib_content']) {
		$selected = 'selected';
	}
	$snippet_select_text .= '<option '.$selected.' value='.$snippet['textlib_name'].'>'.$snippet['textlib_name']. ' - ' .$snippet['textlib_title'].'</option>';
}
$snippet_select_text .= '</select>';





/* print the form */

if($_GET['new'] == 'm' OR $post_data['post_type'] == 'm') {
	$form_tpl = file_get_contents('templates/post_message.tpl');
	$post_data['post_type'] = 'm';
} else if ($_GET['new'] == 'v' OR $post_data['post_type'] == 'v') {
	$form_tpl = file_get_contents('templates/post_video.tpl');
	$post_data['post_type'] = 'v';
} else if ($_GET['new'] == 'i' OR $post_data['post_type'] == 'i') {
	$form_tpl = file_get_contents('templates/post_image.tpl');
	$post_data['post_type'] = 'i';
} else if ($_GET['new'] == 'l' OR $post_data['post_type'] == 'l') {
	$form_tpl = file_get_contents('templates/post_link.tpl');
	$post_data['post_type'] = 'l';
} else if ($_GET['new'] == 'e' OR $post_data['post_type'] == 'e') {
	$form_tpl = file_get_contents('templates/post_event.tpl');
	$post_data['post_type'] = 'e';
} else if ($_GET['new'] == 'p' OR $post_data['post_type'] == 'p') {
	$form_tpl = file_get_contents('templates/post_product.tpl');
	$post_data['post_type'] = 'p';
} else if ($_GET['new'] == 'g' OR $post_data['post_type'] == 'g') {
	$form_tpl = file_get_contents('templates/post_gallery.tpl');
	
	
	$form_upload_tpl = file_get_contents('templates/gallery_upload_form.tpl');
	$form_upload_tpl = str_replace('{token}',$_SESSION['token'], $form_upload_tpl);
	$form_upload_tpl = str_replace('{post_id}',$post_data['post_id'], $form_upload_tpl);
	$form_upload_tpl = str_replace('{disabled_upload_btn}','disabled', $form_upload_tpl);
	
	$form_sort_tpl = file_get_contents('templates/gallery_sort_form.tpl');
	
	$tmb_list = fc_list_gallery_thumbs($post_data['post_id']);
	$form_sort_tpl = str_replace('{thumbnail_list}',$tmb_list, $form_sort_tpl);
	$form_sort_tpl = str_replace('{post_id}',$post_data['post_id'], $form_sort_tpl);
	
	$post_data['post_type'] = 'g';
}

foreach($lang as $k => $v) {
	$form_tpl = str_replace('{'.$k.'}', $lang[$k], $form_tpl);
}

/* user inputs */

$form_tpl = str_replace('{post_title}', $post_data['post_title'], $form_tpl);
$form_tpl = str_replace('{post_teaser}', $post_data['post_teaser'], $form_tpl);
$form_tpl = str_replace('{post_text}', $post_data['post_text'], $form_tpl);
$form_tpl = str_replace('{post_author}', $post_data['post_author'], $form_tpl);
$form_tpl = str_replace('{post_source}', $post_data['post_source'], $form_tpl);
$form_tpl = str_replace('{post_slug}', $post_data['post_slug'], $form_tpl);
$form_tpl = str_replace('{post_tags}', $post_data['post_tags'], $form_tpl);
$form_tpl = str_replace('{post_rss_url}', $post_data['post_rss_url'], $form_tpl);
$form_tpl = str_replace('{select_rss}', $select_rss, $form_tpl);
$form_tpl = str_replace('{select_status}', $select_status, $form_tpl);

$form_tpl = str_replace('{checkboxes_lang}', $checkboxes_lang, $form_tpl);
$form_tpl = str_replace('{checkbox_categories}', $checkboxes_cat, $form_tpl);
$form_tpl = str_replace('{post_releasedate}', $post_releasedate, $form_tpl);
$form_tpl = str_replace('{widget_images}', $choose_images, $form_tpl);


$form_tpl = str_replace('{select_priority}', $select_priority, $form_tpl);
$form_tpl = str_replace('{checkbox_fixed}', $checkbox_fixed, $form_tpl);
$form_tpl = str_replace('{select_status}', $select_status, $form_tpl);

/* video */
$form_tpl = str_replace('{post_video_url}', $post_data['post_video_url'], $form_tpl);

/* links */
$form_tpl = str_replace('{post_link}', $post_data['post_link'], $form_tpl);

/* events */
$form_tpl = str_replace('{event_start}', $post_event_startdate, $form_tpl);
$form_tpl = str_replace('{event_end}', $post_event_enddate, $form_tpl);
$form_tpl = str_replace('{post_event_street}', $post_data['post_event_street'], $form_tpl);
$form_tpl = str_replace('{post_event_street}', $post_data['post_event_street'], $form_tpl);
$form_tpl = str_replace('{post_event_street_nbr}', $post_data['post_event_street_nbr'], $form_tpl);
$form_tpl = str_replace('{post_event_zip}', $post_data['post_event_zip'], $form_tpl);
$form_tpl = str_replace('{post_event_city}', $post_data['post_event_city'], $form_tpl);
$form_tpl = str_replace('{post_event_street}', $post_data['post_event_street'], $form_tpl);
$form_tpl = str_replace('{post_event_price_note}', $post_data['post_event_price_note'], $form_tpl);

/* product */
$form_tpl = str_replace('{post_product_number}', $post_data['post_product_number'], $form_tpl);
$form_tpl = str_replace('{post_product_manufacturer}', $post_data['post_product_manufacturer'], $form_tpl);
$form_tpl = str_replace('{post_product_supplier}', $post_data['post_product_supplier'], $form_tpl);
$form_tpl = str_replace('{post_product_currency}', $post_data['post_product_currency'], $form_tpl);
$form_tpl = str_replace('{post_product_price_label}', $post_data['post_product_price_label'], $form_tpl);
$form_tpl = str_replace('{post_product_amount}', $post_data['post_product_amount'], $form_tpl);
$form_tpl = str_replace('{post_product_unit}', $post_data['post_product_unit'], $form_tpl);
$form_tpl = str_replace('{post_product_price_net}', $post_data['post_product_price_net'], $form_tpl);
$form_tpl = str_replace('{post_product_price_gross}', $post_product_price_gross, $form_tpl);
$form_tpl = str_replace('{select_tax}', $select_tax, $form_tpl);
$form_tpl = str_replace('{snippet_select_pricelist}', $snippet_select_pricelist, $form_tpl);
$form_tpl = str_replace('{snippet_select_text}', $snippet_select_text, $form_tpl);

/* galleries */

$form_tpl = str_replace('{modal_upload_form}', $form_upload_tpl, $form_tpl);
$form_tpl = str_replace('{thumbnail_list_form}', $form_sort_tpl, $form_tpl);

/* form modes */

$form_tpl = str_replace('{post_type}', $post_data['post_type'], $form_tpl);
$form_tpl = str_replace('{post_id}', $post_data['post_id'], $form_tpl);
$form_tpl = str_replace('{post_date}', $post_data['post_date'], $form_tpl);
$form_tpl = str_replace('{modus}', $modus, $form_tpl);
$form_tpl = str_replace('{token}', $_SESSION['token'], $form_tpl);
$form_tpl = str_replace('{formaction}', '?tn=posts&sub=edit', $form_tpl);
$form_tpl = str_replace('{submit_button}', $submit_btn, $form_tpl);


echo $form_tpl;

?>