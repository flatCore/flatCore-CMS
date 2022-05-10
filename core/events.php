<?php
//error_reporting(E_ALL ^E_NOTICE);

$time_string_now = time();
$display_mode = 'list_events';

/* defaults */
$events_start = 0;
$events_limit = (int) $fc_prefs['prefs_posts_entries_per_page'];
if($events_limit == '') {
    $events_limit = 10;
}

$events_filter = array();

$str_status = '1';
if($_SESSION['user_class'] == 'administrator') {
    $str_status = '1-2';
}

$events_filter['types'] = 'e';
$events_filter['languages'] = $page_contents['page_language'];
$events_filter['status'] = $str_status;
$events_filter['categories'] = $page_contents['page_posts_categories'];


if(substr("$mod_slug", -5) == '.html') {
    $get_event_id = (int) basename(end(explode("-", $mod_slug)));
    $display_mode = 'show_event';
}

$all_categories = fc_get_categories();
$array_mod_slug = explode("/", $mod_slug);

foreach($all_categories as $cats) {

    $this_nav_cat_item = $tpl_nav_cats_item;
    $show_category_title = $cats['cat_description'];
    $show_category_name = $cats['cat_name'];
    $cat_href = '/'.$fct_slug.$cats['cat_name_clean'].'/';

    /* show only categories that match the language */
    if($page_contents['page_language'] !== $cats['cat_lang']) {
        continue;
    }
    $cat_class = '';
    if($cats['cat_name_clean'] == $array_mod_slug[0]) {
        $cat_class = 'active';
    }

    $categories[] = array(
        "cat_href" => $cat_href,
        "cat_title" => $show_category_title,
        "cat_name" => $show_category_name,
        "cat_class" => $cat_class
    );


    if($cats['cat_name_clean'] == $array_mod_slug[0]) {
        // show only posts from this category
        $posts_filter['categories'] = $cats['cat_id'];
        $display_mode = 'list_posts_category';

        if($array_mod_slug[1] == 'p') {
            if(is_numeric($array_mod_slug[2])) {
                $posts_start = $array_mod_slug[2];
            } else {
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: /$fct_slug");
                header("Connection: close");
            }
        }
    }
}

/* pagination f.e. /p/2/ or /p/3/ .... */
if($array_mod_slug[0] == 'p') {

    if(is_numeric($array_mod_slug[1])) {
        $events_start = $array_mod_slug[1];
    } else {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: /$fct_slug");
        header("Connection: close");	}
}

/* we are on the event display page, but we have no post id
 * get an event page and redirect */

if($page_contents['page_type_of_use'] == 'display_event' AND $get_event_id == '') {

    $target_page = $db_content->get("fc_pages", "page_permalink", [
        "AND" => [
            "page_posts_types" => "e",
            "page_language" => $page_contents['page_language']
        ]
    ]);

    header("HTTP/1.1 301 Moved Permanently");
    header("Location: /$target_page");
    header("Connection: close");
}


switch ($display_mode) {
    case "list_events_category":
    case "list_events":
        include 'events-list.php';
        break;
    case "show_event":
        include 'events-display.php';
        break;
    default:
        include 'events-list.php';
}
