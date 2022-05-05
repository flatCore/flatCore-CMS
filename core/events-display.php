<?php
error_reporting(E_ALL ^E_NOTICE);

$post_data = fc_get_post_data($get_event_id);
$hits = (int) $post_data['hits'];
fc_increase_posts_hits($get_event_id);


$post_teaser = htmlspecialchars_decode($post_data['post_teaser']);
$post_text = htmlspecialchars_decode($post_data['post_text']);

$post_images = explode("<->", $post_data['post_images']);

$post_releasedate = date("$prefs_dateformat $prefs_timeformat",$post_data['post_releasedate']);
$post_releasedate_year = date('Y',$post_data['post_releasedate']);
$post_releasedate_month = date('m',$post_data['post_releasedate']);
$post_releasedate_day = date('d',$post_data['post_releasedate']);
$post_releasedate_time = date('H:i:s',$post_data['post_releasedate']);

$post_lastedit = date('Y-m-d H:i',$post_data['lastedit']);
$post_lastedit_from = $post_data['post_lastedit_from'];

$event_start_day = date('d',$post_data['post_event_startdate']);
$event_start_month = date('m',$post_data['post_event_startdate']);
$event_start_month_text = $lang["m".$event_start_month];
$event_start_year = date('Y',$post_data['post_event_startdate']);
$event_end_day = date('d',$post_data['post_event_enddate']);
$event_end_month = date('m',$post_data['post_event_enddate']);
$event_end_year = date('Y',$post_data['post_event_enddate']);

$smarty->assign('event_start_day', $event_start_day);
$smarty->assign('event_start_month', $event_start_month);
$smarty->assign('event_start_month_text', $event_start_month_text);
$smarty->assign('event_start_year', $event_start_year);
$smarty->assign('event_end_day', $event_end_day);
$smarty->assign('event_end_month', $event_end_month);
$smarty->assign('event_end_year', $event_end_year);

/* entry date */
$entrydate_year = date('Y',$post_data['post_date']);


/* images */

if($post_images[1] != "") {
    $first_post_image = '/' . $img_path . '/' . str_replace('../content/images/','',$post_images[1]);
    $post_image_data = fc_get_images_data($first_post_image,'data=array');
} else if($fc_prefs['prefs_posts_default_banner'] == "without_image") {
    $first_post_image = '';
} else {
    $first_post_image = "/$img_path/" . $fc_prefs['prefs_posts_default_banner'];
}

/* show guestlist */
$show_guestlist = false;
if($post_data['post_event_guestlist'] == 2 OR $post_data['post_event_guestlist'] == 3) {
    $show_guestlist = true;

    if($post_data['post_event_guestlist'] == 2 AND $_SESSION['user_nick'] == '') {
        /* only registered user can confirm */
        $smarty->assign('disabled', "disabled");
    } else {
        $smarty->assign('disabled', "");
    }

    if($post_data['post_event_guestlist_limit'] != '') {
        $smarty->assign('label_nbr_total_available', $lang['guestlist_label_nbr_total_available']);
        $smarty->assign('nbr_available_total', $post_data['post_event_guestlist_limit']);
    } else {
        $smarty->assign('label_nbr_total_available', "");
        $smarty->assign('nbr_available_total', "");
    }

    if($post_data['post_event_guestlist_public_nbr'] == 2) {
        $cnt_commitments = fc_get_event_confirmation_data($post_data['post_id']);
        $guestlist = str_replace("{label_nbr_commitments}", $lang['guestlist_label_nbr_commitments'], $guestlist);
        $guestlist = str_replace("{nbr_commitments}", $cnt_commitments['evc'], $guestlist);
        $smarty->assign('label_nbr_commitments', $lang['guestlist_label_nbr_commitments']);
        $smarty->assign('nbr_commitments', $cnt_commitments['evc']);
    } else {
        $smarty->assign('label_nbr_commitments', "");
        $smarty->assign('nbr_commitments', "");
    }

    $smarty->assign('sign_guestlist', $lang['btn_guestlist_sign']);
    $smarty->assign('description_guestlist', $lang['guestlist_description']);
}
$smarty->assign('show_guestlist', $show_guestlist);



/* vote up or down this post */
if($post_data['post_votings'] == 2 || $post_data['post_votings'] == 3) {
    $show_voting = true;
    $voter_data = false;
    $voting_type = array("upv", "dnv");
    if ($post_data['post_votings'] == 2) {
        if ($_SESSION['user_nick'] == '') {
            $voter_data = false;
        } else {
            $voter_data = fc_check_user_legitimacy($post_data['post_id'], $_SESSION['user_nick'], $voting_type);
        }
    }

    if ($post_data['post_votings'] == 3) {
        if ($_SESSION['user_nick'] == '') {
            $voter_name = fc_generate_anonymous_voter();
            $voter_data = fc_check_user_legitimacy($post_data['post_id'], $voter_name, $voting_type);
        } else {
            $voter_data = fc_check_user_legitimacy($post_data['post_id'], $_SESSION['user_nick'], $voting_type);
        }
    }

    if ($voter_data == true) {
        // user can vote
        $post_data['votes_status_up'] = '';
        $post_data['votes_status_dn'] = '';
    } else {
        $post_data['votes_status_up'] = 'disabled';
        $post_data['votes_status_dn'] = 'disabled';
    }


    $votes = fc_get_voting_data('post', $post_data['post_id']);

    $post_data['votes_up'] = (int) $votes['upv'];
    $post_data['votes_dn'] = (int) $votes['dnv'];

} else {
    // display no votings
    $show_voting = false;
}


$form_action = '/'.$fct_slug.$mod_slug;
$this_entry = str_replace("{form_action}", $form_action, $this_entry);


if($post_data['post_product_textlib_content'] != 'no_snippet') {
    $textlib_content = get_textlib($post_data['post_product_textlib_content'],$languagePack);
    $smarty->assign('product_snippet_text', $textlib_content);
}



$form_action = '/'.$fct_slug.$mod_slug;


if($post_data['post_meta_title'] == '') {
    $post_data['post_meta_title'] = $post_data['post_title'];
}

if($post_data['post_meta_description'] == '') {
    $post_data['post_meta_description'] = substr(strip_tags($post_teaser),0,160);
}


$page_contents['page_thumbnail'] = $fc_base_url.$img_path.'/'.basename($first_post_image);

$smarty->assign('page_title', html_entity_decode($post_data['post_meta_title']));
$smarty->assign('page_meta_description', html_entity_decode($post_data['post_meta_description']));
$smarty->assign('page_meta_keywords', html_entity_decode($post_data['post_tags']));
$smarty->assign('page_thumbnail', $page_contents['page_thumbnail']);


$smarty->assign('votes_status_up', $post_data['votes_status_up']);
$smarty->assign('votes_status_dn', $post_data['votes_status_dn']);
$smarty->assign('votes_up', $post_data['votes_up']);
$smarty->assign('votes_dn', $post_data['votes_dn']);

$smarty->assign('show_voting', $show_voting);
$smarty->assign('event_img_src', $first_post_image);

$smarty->assign('event_id', $post_data['post_id']);
$smarty->assign('event_title', $post_data['post_title']);
$smarty->assign('event_teaser', $post_teaser);
$smarty->assign('event_text', $post_text);

$smarty->assign('event_price_note', html_entity_decode($post_data['post_event_price_note']));

$smarty->assign('form_action', $form_action);
$smarty->assign('btn_add_to_cart', $lang['btn_add_to_cart']);

$event_page = $smarty->fetch("events-display.tpl", $cache_id);
$smarty->assign('page_content', $event_page, true);