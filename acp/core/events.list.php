<?php
error_reporting(E_ALL ^E_NOTICE);
//prohibit unauthorized access
require __DIR__.'/access.php';


/* delete event */

if((isset($_POST['delete_id'])) && is_numeric($_POST['delete_id'])) {

    $del_id = (int) $_POST['delete_id'];
    $delete = $db_posts->delete("fc_posts", [
        "post_id" => $del_id
    ]);

    if($delete->rowCount() > 0) {
        echo '<div class="alert alert-success">'.$lang['msg_post_deleted'].'</div>';
    }
}

/* change priority */

if(isset($_POST['post_priority'])) {
    $change_id = (int) $_POST['prio_id'];
    $db_posts->update("fc_posts", [
        "post_priority" => $_POST['post_priority']
    ],[
        "post_id" => $change_id
    ]);
}

/* remove fixed */

if(is_numeric($_POST['rfixed'])) {

    $change_id = (int) $_POST['rfixed'];
    $db_posts->update("fc_posts", [
        "post_fixed" => "2"
    ],[
        "post_id" => $change_id
    ]);
}

/* set fixed */

if(is_numeric($_POST['sfixed'])) {

    $change_id = (int) $_POST['sfixed'];
    $db_posts->update("fc_posts", [
        "post_fixed" => "1"
    ],[
        "post_id" => $change_id
    ]);

}


// defaults
$events_start = 0;
$events_limit = 25;
$events_order = 'id';
$events_direction = 'DESC';
$events_filter = array();

$arr_status = array('2','1');
$arr_lang = get_all_languages();
$arr_categories = fc_get_categories();

/* default: check all languages */
if(!isset($_SESSION['checked_lang_string'])) {
    foreach($arr_lang as $langstring) {
        $checked_lang_string .= "$langstring[lang_folder]-";
    }
    $_SESSION['checked_lang_string'] = "$checked_lang_string";
}

/* change status of $_GET['switchLang'] */
if($_GET['switchLang']) {
    if(strpos("$_SESSION[checked_lang_string]", "$_GET[switchLang]") !== false) {
        $checked_lang_string = str_replace("$_GET[switchLang]-", '', $_SESSION['checked_lang_string']);
    } else {
        $checked_lang_string = $_SESSION['checked_lang_string'] . "$_GET[switchLang]-";
    }
    $_SESSION['checked_lang_string'] = "$checked_lang_string";
}

/* filter buttons for languages */
$lang_btn_group = '<div class="btn-group">';
foreach($lang_codes as $lang_code) {
    $this_btn_status = '';
    if(strpos("$_SESSION[checked_lang_string]", "$lang_code") !== false) {
        $this_btn_status = 'active';
    }
    $lang_btn_group .= '<a href="acp.php?tn=posts&switchLang='.$lang_code.'" class="btn btn-sm btn-fc '.$this_btn_status.'">'.$lang_code.'</a>';
}
$lang_btn_group .= '</div>';

/* default: check all status types */
if(!isset($_SESSION['checked_status_string'])) {
    $_SESSION['checked_status_string'] = '1-2';
}

/* change status types */
if($_GET['status']) {
    if(strpos("$_SESSION[checked_status_string]", "$_GET[status]") !== false) {
        $checked_status_string = str_replace("$_GET[status]", '', $_SESSION['checked_status_string']);
    } else {
        $checked_status_string = $_SESSION['checked_status_string'] . '-' . $_GET['status'];
    }
    $checked_status_string = str_replace('--', '-', $checked_status_string);
    $_SESSION['checked_status_string'] = "$checked_status_string";
}

/* default: check all categories */
if(!isset($_SESSION['checked_cat_string'])) {
    $_SESSION['checked_cat_string'] = 'all';
}
/* filter by categories */
if($_GET['cat']) {
    $_SESSION['checked_cat_string'] = $_GET['cat'];
}

$cat_all_active = '';
$icon_all_toggle = $icon['circle_alt'];
if($_SESSION['checked_cat_string'] == 'all') {
    $cat_all_active = 'active';
    $icon_all_toggle = $icon['check_circle'];
}

$cat_btn_group = '<div class="card">';
$cat_btn_group .= '<div class="list-group list-group-flush">';
$cat_btn_group .= '<a href="acp.php?tn=events&cat=all" class="list-group-item list-group-item-ghost p-1 px-2 '.$cat_all_active.'">'.$icon_all_toggle.' '.$lang['btn_all_categories'].'</a>';
foreach($arr_categories as $c) {
    $cat_active = '';
    $icon_toggle = $icon['circle_alt'];
    if(strpos($_SESSION['checked_cat_string'], $c['cat_id']) !== false) {
        $icon_toggle = $icon['check_circle'];
        $cat_active = 'active';
    }

    $cat_btn_group .= '<a href="acp.php?tn=events&cat='.$c['cat_id'].'" class="list-group-item list-group-item-ghost p-1 px-2 '.$cat_active.'">'.$icon_toggle.' '.$c['cat_name'].'</a>';
}

$cat_btn_group .= '</div>';
$cat_btn_group .= '</div>';

/* filter buttons for labels */

if(!isset($_SESSION['checked_label_str'])) {
    $_SESSION['checked_label_str'] = '';
}

$a_checked_labels = explode('-', $_SESSION['checked_label_str']);

if(isset($_GET['switchLabel'])) {

    if(in_array($_GET['switchLabel'], $a_checked_labels)) {
        /* remove label*/
        if(($key = array_search($_GET['switchLabel'], $a_checked_labels)) !== false) {
            unset($a_checked_labels[$key]);
        }
    } else {
        /* add label */
        $a_checked_labels[] = $_GET['switchLabel'];
    }

    $_SESSION['checked_label_str'] = implode('-', $a_checked_labels);
}

$a_checked_labels = explode('-', $_SESSION['checked_label_str']);

$label_filter_box  = '<div class="card mt-2">';
$label_filter_box .= '<div class="card-header p-1 px-2">'.$lang['labels'].'</div>';
$label_filter_box .= '<div class="card-body">';
$this_btn_status = '';
foreach($fc_labels as $label) {

    if(in_array($label['label_id'], $a_checked_labels)) {
        $this_btn_status = 'active';
    } else {
        $this_btn_status = '';
    }

    $label_title = '<span class="label-dot" style="background-color:'.$label['label_color'].';"></span> '.$label['label_title'];
    $label_filter_box .= '<a href="acp.php?tn=events&sub=list&switchLabel='.$label['label_id'].'" class="btn btn-fc btn-sm m-1 '.$this_btn_status.'">'.$label_title.'</a>';

}
$label_filter_box .= '</div>';
$label_filter_box .= '</div>'; // card



if((isset($_GET['events_start'])) && is_numeric($_GET['events_start'])) {
    $events_start = (int) $_GET['events_start'];
}

if((isset($_POST['setPage'])) && is_numeric($_POST['setPage'])) {
    $events_start = (int) $_POST['setPage'];
}

$events_filter['languages'] = $_SESSION['checked_lang_string'];
$events_filter['status'] = $_SESSION['checked_status_string'];
$events_filter['categories'] = $_SESSION['checked_cat_string'];
$events_filter['labels'] = $_SESSION['checked_label_str'];

$get_events = fc_get_event_entries($events_start,$events_limit,$events_filter);

$cnt_filter_events = $get_events[0]['cnt_events'];
$cnt_get_events = count($get_events);
$cnt_posts = fc_cnt_post_entries();

$nextPage = $events_start+$events_limit;
$prevPage = $events_start-$events_limit;
$cnt_pages = ceil($cnt_filter_events / $events_limit);

echo '<div class="subHeader">';
echo '<h3>' . sprintf($lang['label_show_entries'], $cnt_filter_events, $cnt_posts['All']) .'</h3>';
echo '</div>';


echo '<div class="row">';
echo '<div class="col-md-9">';

echo '<div class="card p-3">';


if($cnt_get_events > 0) {

    echo '<table class="table table-sm table-hover">';

    echo '<thead><tr>';
    echo '<th>#</th>';
    echo '<th>' . $icon['star'] . '</th>';
    echo '<th>' . $lang['label_priority'] . '</th>';
    echo '<th nowrap>' . $lang['label_date'] . '</th>';
    echo '<th></th>';
    echo '<th>' . $lang['label_post_title'] . '</th>';
    echo '<th></th>';
    echo '</tr></thead>';

    for($i=0;$i<$cnt_get_events;$i++) {

        $icon_fixed = '';
        $draft_class = '';

        $icon_fixed_form = '<form action="?tn=events" method="POST" class="form-inline">';
        if($get_events[$i]['post_fixed'] == '1') {
            $icon_fixed_form .= '<button type="submit" class="btn btn-link" name="rfixed" value="'.$get_events[$i]['post_id'].'">'.$icon['star'].'</button>';
        } else {
            $icon_fixed_form .= '<button type="submit" class="btn btn-link" name="sfixed" value="'.$get_events[$i]['post_id'].'">'.$icon['star_outline'].'</button>';
        }
        $icon_fixed_form .= $hidden_csrf_token;
        $icon_fixed_form .= '</form>';

        if($get_events[$i]['post_status'] == '2') {
            $draft_class = 'item_is_draft';
        }

        /* trim teaser to $trim chars */
        $trim = 150;
        $teaser = strip_tags(htmlspecialchars_decode($get_events[$i]['post_teaser']));
        if(strlen($teaser) > $trim) {
            $ellipses = ' <small><i>(...)</i></small>';
            $last_space = strrpos(substr($teaser, 0, $trim), ' ');
            if($last_space !== false) {
                $trimmed_teaser = substr($teaser, 0, $last_space);
            } else {
                $trimmed_teaser = substr($teaser, 0, $trim);
            }
            $trimmed_teaser = $trimmed_teaser.$ellipses;
        } else {
            $trimmed_teaser = $teaser;
        }


        $post_image = explode("<->", $get_events[$i]['post_images']);
        $show_thumb = '';
        if($post_image[1] != "") {
            $image_src = $post_image[1];
            /* older version of flatNews stored only basename of images */
            if(stripos($post_image[1],'/content/') === FALSE) {
                $image_src = "/$img_path/" . $post_image[1];
            }

            $show_thumb  = '<a data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="<img src=\''.$image_src.'\'>">';
            $show_thumb .= '<div class="show-thumb" style="background-image: url('.$image_src.');">';
            $show_thumb .= '</div>';
        }

        /* labels */
        $get_labels = explode(',',$get_events[$i]['post_labels']);
        $label = '';
        if($get_events[$i]['post_labels'] != '') {
            foreach($get_labels as $labels) {

                foreach($fc_labels as $l) {
                    if($labels == $l['label_id']) {
                        $label_color = $l['label_color'];
                        $label_title = $l['label_title'];
                    }
                }

                $label .= '<span class="label-dot" style="background-color:'.$label_color.';" title="'.$label_title.'"></span>';
            }
        }

        /* categories */
        $get_post_categories = explode('<->',$get_events[$i]['post_categories']);
        $categories = '';
        if($get_events[$i]['post_categories'] != '') {
            foreach($get_post_categories as $cats) {

                foreach($arr_categories as $cat) {
                    if($cats == $cat['cat_id']) {
                        $cat_title = $cat['cat_name'];
                        $cat_description = $cat['cat_description'];
                    }
                }
                $categories .= '<span class="badge bg-secondary" title="'.$cat_description.'">'.$cat_title.'</span> ';
            }
        }


        $select_priority = '<select name="post_priority" class="form-control custom-select" onchange="this.form.submit()">';
        for($x=1;$x<11;$x++) {
            $option_add = '';
            $sel_prio = '';
            if($get_events[$i]['post_priority'] == $x) {
                $sel_prio = 'selected';
            }
            $select_priority .= '<option value="'.$x.'" '.$sel_prio.'>'.$x.'</option>';
        }
        $select_priority .= '</select>';


        $prio_form  = '<form action="acp.php?tn=events&a=start" method="POST">';
        $prio_form .= $select_priority;
        $prio_form .= '<input type="hidden" name="prio_id" value="'.$get_events[$i]['post_id'].'">';
        $prio_form .= $hidden_csrf_token;
        $prio_form .= '</form>';

        $published_date = '<span class="badge bg-secondary" title="Published">'.$icon['file'].' '.fc_format_datetime($get_events[$i]['post_date']).'</span>';
        $release_date = '<span class="badge bg-secondary" title="Released">'.$icon['clock'].' '.fc_format_datetime($get_events[$i]['post_releasedate']).'</span>';
        $lastedit_date = '';
        if($get_events[$i]['post_lastedit'] != '') {
            $lastedit_date = '<span class="badge bg-secondary" title="Last edit">'.$icon['edit'].' '.fc_format_datetime($get_events[$i]['post_lastedit']).'</span>';
        }

        $show_events_date = '';

        $show_events_date = '<div class="float-end small well well-sm">';
        $show_events_date .= date('Y-m-d',$get_events[$i]['post_event_startdate']);
        $show_events_date .= '<br>';
        $show_events_date .= date('Y-m-d',$get_events[$i]['post_event_enddate']);
        $show_events_date .= '</div>';


        echo '<tr class="'.$draft_class.'">';
        echo '<td>'.$get_events[$i]['post_id'].'</td>';
        echo '<td>'.$icon_fixed_form.'</td>';
        echo '<td>'.$prio_form.'</td>';
        echo '<td nowrap><small>'.$published_date.'<br>'.$release_date.'<br>'.$lastedit_date.'</small></td>';
        echo '<td>'.$show_type.'</td>';
        echo '<td>'.$show_thumb.'</td>';
        echo '<td>'.$show_events_date.$show_items_price.$show_items_downloads.$show_items_redirects.'<h5 class="mb-0">'.$get_events[$i]['post_title'].'</h5><small>'.$trimmed_teaser.'</small><br>'.$categories.'<br>'.$label.'</td>';
        echo '<td style="min-width: 150px;">';
        echo '<nav class="nav justify-content-end">';
        echo '<form class="form-inline mr-1" action="?tn=events&sub=edit" method="POST">';
        echo '<button class="btn btn-fc btn-sm text-success" type="submit" name="post_id" value="'.$get_events[$i]['post_id'].'">'.$lang['edit'].'</button>';
        echo $hidden_csrf_token;
        echo '</form> ';
        echo '<form class="form-inline" action="acp.php?tn=events" method="POST">';
        echo '<button class="btn btn-danger btn-sm" type="submit" name="delete_id" value="'.$get_events[$i]['post_id'].'">'.$icon['trash_alt'].'</button>';
        echo $hidden_csrf_token;
        echo '</form>';
        echo '</nav>';
        echo '</td>';
        echo '</tr>';

    }

    echo '</table>';

} else {
    echo '<div class="alert alert-info">'.$lang['msg_no_posts_to_show'].'</div>';
}


echo '</div>'; // card


echo '</div>';
echo '<div class="col-md-3">';


/* sidebar */
echo '<div class="card p-2">';

echo '<a class="btn w-100 btn-success" href="?tn=events&sub=edit&new=e"><span class="color-event">'.$icon['plus'].'</span> '.$lang['label_new_post'].'</a>';


echo '<hr>';

echo '<div class="row">';
echo '<div class="col-md-2">';
if($prevPage < 0) {
    echo '<a class="btn btn-fc w-100 disabled" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>';
} else {
    echo '<a class="btn btn-fc w-100" href="acp.php?tn=posts&posts_start='.$prevPage.'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>';
}

echo '</div>';
echo '<div class="col-md-8">';
echo '<form action="acp.php?tn=posts" method="POST">';
echo '<select class="form-control custom-select" name="setPage" onchange="this.form.submit()">';
for($i=0;$i<$cnt_pages;$i++) {
    $x = $i+1;
    $thisPage = ($x*$posts_limit)-$posts_limit;
    $sel = '';
    if($thisPage == $posts_start) {
        $sel = 'selected';
    }
    echo '<option value="'.$thisPage.'" '.$sel.'>'.$x.' ('.$thisPage.')</option>';
}
echo '</select>';
echo '</form>';
echo '</div>';
echo '<div class="col-md-2">';
if($nextPage < ($cnt_filter_posts-$posts_limit)+$posts_limit) {
    echo '<a class="btn btn-fc w-100" href="acp.php?tn=posts&posts_start='.$nextPage.'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>';
} else {
    echo '<a class="btn btn-fc w-100 disabled" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>';
}
echo '</div>';
echo '</div>';

echo '<fieldset class="mt-4">';
echo '<legend>'.$icon['filter'].' Filter</legend>';

/* Filter Options */
echo '<div class="card mt-1">';
echo '<div class="card-header p-1 px-2">'.$lang['label_language'].'</div>';
echo '<div class="list-group list-group-flush">';
echo $lang_btn_group;
echo '</div>';
echo '</div>';



echo '<div class="card mt-2">';
echo '<div class="card-header p-1 px-2">'.$lang['label_status'].'</div>';

/* status filter */
echo '<div class="btn-group d-flex">';
if(strpos("$_SESSION[checked_status_string]", "2") !== false) {
    $icon_toggle = $icon['check_circle'];
    echo '<a href="acp.php?tn=posts&status=2" class="btn btn-sm btn-fc active w-100">'.$icon_toggle.' '.$lang['status_draft'].'</a>';
} else {
    $icon_toggle = $icon['circle_alt'];
    echo '<a href="acp.php?tn=posts&status=2" class="btn btn-sm btn-fc w-100">'.$icon_toggle.' '.$lang['status_draft'].'</a>';
}
if(strpos("$_SESSION[checked_status_string]", "1") !== false) {
    $icon_toggle = $icon['check_circle'];
    echo '<a href="acp.php?tn=posts&status=1" class="btn btn-sm btn-fc active w-100">'.$icon_toggle.' '.$lang['status_public'].'</a>';
} else {
    $icon_toggle = $icon['circle_alt'];
    echo '<a href="acp.php?tn=posts&status=1" class="btn btn-sm btn-fc w-100">'.$icon_toggle.' '.$lang['status_public'].'</a>';
}
echo '</div>';


echo '</div>';

echo '<div class="card mt-2">';
echo '<div class="card-header p-1 px-2">'.$lang['label_categories'].'</div>';

echo $cat_btn_group;

echo '</div>';

echo $label_filter_box;

echo '</fieldset>';

echo '</div>'; // card



echo '</div>';
echo '</div>';




