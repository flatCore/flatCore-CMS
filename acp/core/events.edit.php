<?php

//prohibit unauthorized access
require 'core/access.php';

/* set modus */

if((isset($_POST['post_id'])) && is_numeric($_POST['post_id'])) {

    $post_id = (int) $_POST['post_id'];
    $modus = 'update';
    $post_data = fc_get_post_data($post_id);
    $submit_btn = '<input type="submit" class="btn btn-save w-100" name="save_post" value="'.$lang['update'].'">';

} else {
    $post_id = '';
    $modus = 'new';
    $submit_btn = '<input type="submit" class="btn btn-save w-100" name="save_post" value="'.$lang['save'].'">';

}

/* save or update post data */

if(isset($_POST['save_post']) OR isset($_POST['del_tmb']) OR isset($_POST['sort_tmb'])) {

    foreach($_POST as $key => $val) {
        if(is_string($val)) {
            $$key = @htmlspecialchars($val, ENT_QUOTES);
        }
    }

    $post_releasedate = time();
    $post_lastedit = time();
    $post_lastedit_from = $_SESSION['user_nick'];
    $post_priority = (int) $_POST['post_priority'];

    if($_POST['post_date'] == "") {
        $post_date = time();
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

    $post_categories = '';
    if(is_array($_POST['post_categories'])) {
        $post_categories = implode("<->", $_POST['post_categories']);
    }

    $post_images = '';
    if(is_array($_POST['picker1_images'])) {
        $post_images_string = implode("<->", $_POST['picker1_images']);
        $post_images_string = "<->$post_images_string<->";
        $post_images = $post_images_string;
    }

    $product_price_net = str_replace('.', '', $_POST['post_product_price_net']);
    $product_price_net = str_replace(',', '.', $product_price_net);

    /* labels */
    $post_labels = '';
    if(is_array($_POST['post_labels'])) {
        $post_labels = implode(",", $_POST['post_labels']);
    }

    /* fix on top */

    if($_POST['post_fixed'] == 'fixed') {
        $post_fixed = 1;
    } else {
        $post_fixed = 2;
    }

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

    /* metas */
    if($_POST['post_meta_title'] == '') {
        $post_meta_title = $_POST['post_title'];
    } else {
        $post_meta_title = $_POST['post_meta_title'];
    }
    if($_POST['post_meta_description'] == '') {
        $post_meta_description = strip_tags($_POST['post_teaser']);
    } else {
        $post_meta_description = $_POST['post_meta_description'];
    }

    $post_meta_title = fc_return_clean_value($post_meta_title);
    $post_meta_description = fc_return_clean_value($post_meta_description);

    $post_product_features = json_encode($_POST['post_product_features'],JSON_FORCE_OBJECT);

    /* save or update data */

    /* get all $cols */
    require '../install/contents/fc_posts.php';
    // build sql string -> f.e. "post_releasedate" => $post_releasedate,
    foreach($cols as $k => $v) {
        if($k == 'post_id') {continue;}
        $value = $$k;
        $inputs[$k] = "$value";
    }

    if($modus == "update")	{
        $db_posts->update("fc_posts", $inputs, [
            "post_id" => $post_id
        ]);
    } else {
        $db_posts->insert("fc_posts", $inputs);
        $post_id = $db_posts->id();
        $modus = 'update';
        $submit_btn = '<input type="submit" class="btn btn-save w-100" name="save_post" value="'.$lang['update'].'">';
    }

    /* update the rss url */

    // get the posting-page by 'type_of_use' and $languagePack
    $target_page = $db_content->select("fc_pages", "page_permalink", [
        "AND" => [
            "page_type_of_use" => "display_post",
            "page_language" => $post_lang
        ]
    ]);

    // if we have no target page - find a blog page
    if($target_page[0] == '') {
        $target_page = $db_content->select("fc_pages", "page_permalink", [
            "AND" => [
                "page_posts_categories[!]" => "",
                "page_language" => $post_lang
            ]
        ]);
    }

    if($target_page[0] != '') {
        $rss_url = $fc_base_url.$target_page[0].$clean_title.'-'.$post_id.'.html';
        $db_posts->update("fc_posts", [
            "post_rss_url" => $rss_url
        ], [
            "post_id" => $post_id
        ]);

        /* send to rss feed */
        if($_POST['post_rss'] == 'on') {
            add_feed("$post_title",$_POST['post_teaser'],"$rss_url","post_$id","",$post_releasedate);
        }
    }


    /* re load the posts data */
    $post_data = fc_get_post_data($post_id);
}

/* language */

$post_lang = $post_data['post_lang'];

if($post_lang == '' && $default_lang_code != '') {
    $post_lang = $default_lang_code;
}

$select_lang  = '<select name="post_lang" class="custom-select form-control">';
foreach($lang_codes as $lang_code) {
    $select_lang .= "<option value='$lang_code'".($post_lang == "$lang_code" ? 'selected="selected"' :'').">$lang_code</option>";
}
$select_lang .= '</select>';



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
    $checkboxes_cat .= '<label class="form-check-label" for="cat'.$i.'">'.$category.' <small>('.$cats[$i]['cat_lang'].')</small></label>';
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
if($post_data['post_fixed'] == '1') {
    $checked_fixed = 'checked';
}
$checkbox_fixed  = '<div class="form-check">';
$checkbox_fixed .= '<input class="form-check-input" id="fix" type="checkbox" name="post_fixed" value="fixed" '.$checked_fixed.'>';
$checkbox_fixed .= '<label class="form-check-label" for="fix">'.$lang['label_fixed'].'</label>';
$checkbox_fixed .= '</div>';


/* image widget */
$images = fc_get_all_media_data('image');
$images = fc_unique_multi_array($images,'media_file');
$array_images = explode("<->", $post_data['post_images']);
$choose_images = fc_select_img_widget($images,$array_images,$prefs_posts_images_prefix,1);

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

/* comments yes/no */

if($post_data['post_comments'] == 1) {
    $sel_comments_yes = 'selected';
    $sel_comments_no = '';
} else {
    $sel_comments_no = 'selected';
    $sel_comments_yes = '';
}

$select_comments  = '<select id="select_comments" name="post_comments"  class="custom-select form-control">';
$select_comments .= '<option value="1" '.$sel_comments_yes.'>'.$lang['yes'].'</option>';
$select_comments .= '<option value="2" '.$sel_comments_no.'>'.$lang['no'].'</option>';
$select_comments .= '</select>';

/* votings/reactions no, yes for registered users, yes for all */

if($post_data['post_votings'] == '') {
    $post_data['post_votings'] = $prefs_posts_default_votings;
}

if($post_data['post_votings'] == 1 OR $post_data['post_votings'] == '') {
    $sel_votings_1 = 'selected';
    $sel_votings_2 = '';
    $sel_votings_3 = '';
} else if($post_data['post_votings'] == 2) {
    $sel_votings_1 = '';
    $sel_votings_2 = 'selected';
    $sel_votings_3 = '';
} else {
    $sel_votings_1 = '';
    $sel_votings_2 = '';
    $sel_votings_3 = 'selected';
}

$select_votings  = '<select id="select_votings" name="post_votings"  class="custom-select form-control">';
$select_votings .= '<option value="1" '.$sel_votings_1.'>'.$lang['label_votings_off'].'</option>';
$select_votings .= '<option value="2" '.$sel_votings_2.'>'.$lang['label_votings_on_registered'].'</option>';
$select_votings .= '<option value="3" '.$sel_votings_3.'>'.$lang['label_votings_on_global'].'</option>';
$select_votings .= '</select>';


/* autor */

if($post_data['post_author'] == '') {
    $post_data['post_author'] = $_SESSION['user_firstname'] .' '. $_SESSION['user_lastname'];
}

if($post_data['post_author'] == "" && $prefs_default_publisher != '') {
    $post_data['post_author'] = $prefs_default_publisher;
}

if($prefs_publisher_mode == 'overwrite') {
    $post_data['post_author'] = $prefs_default_publisher;
}


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


$form_tpl = file_get_contents('templates/post_event.tpl');
$post_data['post_type'] = 'e';

/* replace all entries from $lang */
foreach($lang as $k => $v) {
    $form_tpl = str_replace('{'.$k.'}', $lang[$k], $form_tpl);
}

/* labels */

$arr_checked_labels = explode(",", $post_data['post_labels']);

for($i=0;$i<$cnt_labels;$i++) {
    $label_title = $fc_labels[$i]['label_title'];
    $label_id = $fc_labels[$i]['label_id'];
    $label_color = $fc_labels[$i]['label_color'];

    if(in_array("$label_id", $arr_checked_labels)) {
        $checked_label = "checked";
    } else {
        $checked_label = "";
    }

    $checkbox_set_labels .= '<div class="form-check form-check-inline" style="border-bottom: 1px solid '.$label_color.'">';
    $checkbox_set_labels .= '<input class="form-check-input" id="label'.$label_id.'" type="checkbox" '.$checked_label.' name="post_labels[]" value="'.$label_id.'">';
    $checkbox_set_labels .= '<label class="form-check-label" for="label'.$label_id.'">'.$label_title.'</label>';
    $checkbox_set_labels .= '</div>';
}

$form_tpl = str_replace('{post_labels}', $checkbox_set_labels, $form_tpl);


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

$form_tpl = str_replace('{post_meta_title}', $post_data['post_meta_title'], $form_tpl);
$form_tpl = str_replace('{post_meta_description}', $post_data['post_meta_description'], $form_tpl);

$form_tpl = str_replace('{checkboxes_lang}', $select_lang, $form_tpl);
$form_tpl = str_replace('{checkbox_categories}', $checkboxes_cat, $form_tpl);
$form_tpl = str_replace('{post_releasedate}', $post_releasedate, $form_tpl);
$form_tpl = str_replace('{widget_images}', $choose_images, $form_tpl);


$form_tpl = str_replace('{select_priority}', $select_priority, $form_tpl);
$form_tpl = str_replace('{checkbox_fixed}', $checkbox_fixed, $form_tpl);
$form_tpl = str_replace('{select_status}', $select_status, $form_tpl);
$form_tpl = str_replace('{select_comments}', $select_comments, $form_tpl);
$form_tpl = str_replace('{select_votings}', $select_votings, $form_tpl);

/* video */
$form_tpl = str_replace('{post_video_url}', $post_data['post_video_url'], $form_tpl);

/* links */
$form_tpl = str_replace('{post_link}', $post_data['post_link'], $form_tpl);

/* files */
$form_tpl = str_replace('{post_file_attachment_external}', $post_data['post_file_attachment_external'], $form_tpl);
$form_tpl = str_replace('{post_file_license}', $post_data['post_file_license'], $form_tpl);
$form_tpl = str_replace('{post_file_version}', $post_data['post_file_version'], $form_tpl);
$form_tpl = str_replace('{select_file}', $select_file, $form_tpl);

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
$form_tpl = str_replace('{post_event_guestlist_limit}', $post_data['post_event_guestlist_limit'], $form_tpl);

/* guest list */

$sel_gl_type1 = '';
$sel_gl_type2 = '';
$sel_gl_type3 = '';

if($post_data['post_event_guestlist'] == '') {
    $post_data['post_event_guestlist'] = $prefs_posts_default_guestlist;
}

if($post_data['post_event_guestlist'] == '1') {
    $sel_gl_type1 = 'selected';
} else if($post_data['post_event_guestlist'] == '2') {
    $sel_gl_type2 = 'selected';
} else if($post_data['post_event_guestlist'] == '3') {
    $sel_gl_type3 = 'selected';
}

$select_guestlist = '<select class="form-control custom-select" name="post_event_guestlist">';

$select_guestlist .= '<option value="1" '.$sel_gl_type1.'>'.$lang['label_guestlist_deactivate'].'</option>';
$select_guestlist .= '<option value="2" '.$sel_gl_type2.'>'.$lang['label_guestlist_for_registered'].'</option>';
$select_guestlist .= '<option value="3" '.$sel_gl_type3.'>'.$lang['label_guestlist_for_everybody'].'</option>';

$select_guestlist .= '</select>';
$form_tpl = str_replace('{select_guestlist}', $select_guestlist, $form_tpl);

if($post_data['post_event_guestlist_public_nbr'] == '1') {
    $form_tpl = str_replace('{checked_gl_public_nbr_1}', 'checked', $form_tpl);
    $form_tpl = str_replace('{checked_gl_public_nbr_2}', '', $form_tpl);
} else {
    $form_tpl = str_replace('{checked_gl_public_nbr_1}', '', $form_tpl);
    $form_tpl = str_replace('{checked_gl_public_nbr_2}', 'checked', $form_tpl);
}

$form_tpl = str_replace('{checked_guestlist}', $checked_guestlist, $form_tpl);



/* form modes */

$form_tpl = str_replace('{post_type}', $post_data['post_type'], $form_tpl);
$form_tpl = str_replace('{post_id}', $post_data['post_id'], $form_tpl);
$form_tpl = str_replace('{post_date}', $post_data['post_date'], $form_tpl);
$form_tpl = str_replace('{post_year}', date('Y',$post_data['post_date']), $form_tpl);
$form_tpl = str_replace('{modus}', $modus, $form_tpl);
$form_tpl = str_replace('{token}', $_SESSION['token'], $form_tpl);
$form_tpl = str_replace('{formaction}', '?tn=events&sub=edit', $form_tpl);
$form_tpl = str_replace('{submit_button}', $submit_btn, $form_tpl);


echo $form_tpl;
