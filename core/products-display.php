<?php
error_reporting(E_ALL ^E_NOTICE);

$post_data = fc_get_post_data($get_product_id);

$hits = (int) $post_data['hits'];
fc_increase_posts_hits($get_product_id);


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

if($post_data['post_product_tax'] == '1') {
    $tax = $fc_prefs['prefs_posts_products_default_tax'];
} else if($post_data['post_product_tax'] == '2') {
    $tax = $fc_prefs['prefs_posts_products_tax_alt1'];
} else {
    $tax = $fc_prefs['prefs_posts_products_tax_alt2'];
}

$post_product_price_addition = $post_data['post_product_price_addition'];
if($post_product_price_addition == '') {
    $post_product_price_addition = 0;
}

$post_prices = fc_posts_calc_price($post_data['post_product_price_net'],$post_product_price_addition,$tax);
$post_price_net = $post_prices['net'];
$post_price_gross = $post_prices['gross'];

$smarty->assign('product_price_gross', $post_price_gross);
$smarty->assign('product_price_net', $post_price_net_calculated);
$smarty->assign('product_price_tax', $tax);
$smarty->assign('product_currency', $post_data['post_product_currency']);
$smarty->assign('product_unit', $post_data['post_product_unit']);
$smarty->assign('product_amount', $post_data['post_product_amount']);
$smarty->assign('product_price_label', $post_data['post_product_price_label']);
$smarty->assign('product_price_tag_label_gross', $lang['price_tag_label_gross']);
$smarty->assign('product_price_tag_label_net', $lang['price_tag_label_net']);

if($post_data['post_product_textlib_content'] != 'no_snippet') {
    $textlib_content = get_textlib($post_data['post_product_textlib_content'],$languagePack);
    $smarty->assign('product_snippet_text', $textlib_content);
}

if($post_data['post_product_textlib_price'] != 'no_snippet') {
    $textlib_price = get_textlib($post_data['post_product_textlib_price'],$languagePack);
    $smarty->assign('label_prices_snippet', $lang['label_prices_snippet']);
    $smarty->assign('product_snippet_price', $textlib_price);
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
$smarty->assign('product_img_src', $first_post_image);

$smarty->assign('product_id', $post_data['post_id']);
$smarty->assign('product_title', $post_data['post_title']);
$smarty->assign('product_teaser', $post_teaser);
$smarty->assign('product_text', $post_text);

$smarty->assign('form_action', $form_action);
$smarty->assign('btn_add_to_cart', $lang['btn_add_to_cart']);

$products_page = $smarty->fetch("products-display.tpl", $cache_id);
$smarty->assign('page_content', $products_page, true);