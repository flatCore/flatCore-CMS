<?php
//error_reporting(E_ALL ^E_NOTICE);

// get the posting-page by 'type_of_use' and $languagePack
$target_page = $db_content->select("fc_pages", "page_permalink", [
    "AND" => [
        "page_type_of_use" => "display_product",
        "page_language" => $page_contents['page_language']
    ]
]);

if($target_page[0] == '') {
    $target_page[0] = $fct_slug;
}


/**
 * check if we show the shopping cart
 */
$tpl_btn_add_to_cart = '';
if($fc_prefs['prefs_posts_products_cart'] == 2 OR $fc_prefs['prefs_posts_products_cart'] == 3) {
    $tpl_btn_add_to_cart = fc_load_posts_tpl($fc_template,'btn-add-to-cart.tpl');
    $tpl_btn_add_to_cart = str_replace('{btn_add_to_cart}', $lang['btn_add_to_cart'], $tpl_btn_add_to_cart);

}

/**
 * template files
 * check if the page template $fc_template hast the posts tpl files
 * if not, load files from the default directory
 */

$tpl_list_index = fc_load_posts_tpl($fc_template,'post-list-index.tpl');
$tpl_list_p = fc_load_posts_tpl($fc_template,'post-list-p.tpl');


$tpl_pagination = fc_load_posts_tpl($fc_template,'pagination.tpl');
$tpl_pagagination_list = fc_load_posts_tpl($fc_template,'pagination_list.tpl');

$tpl_category_link = fc_load_posts_tpl($fc_template,'link-categories.tpl');


$sql_start = ($products_start*$products_limit)-$products_limit;
if($sql_start < 0) {
    $sql_start = 0;
}

$get_products = fc_get_post_entries($sql_start,$products_limit,$products_filter);
$cnt_filter_products = $get_products[0]['cnt_posts'];
$cnt_get_products = count($get_products);

$nextPage = $products_start+$products_limit;
$prevPage = $products_start-$products_limit;
$cnt_pages = ceil($cnt_filter_products / $products_limit);

if($cnt_pages > 1) {
    $pag_list = '';
    $arr_pag = array();

    for($i=0;$i<$cnt_pages;$i++) {

        $active_class = '';
        $set_start = $i+1;

        if($i == 0 && $products_start < 1) {
            $set_start = 1;
            $active_class = 'active';
        }

        if($set_start == $products_start) {
            $active_class = 'active';
            $current_page = $set_start;
        }

        $pagination_link = fc_set_pagination_query($display_mode,$set_start);

        $pag_list_item = $tpl_pagagination_list;
        $pag_list_item = str_replace("{pag_href}", $pagination_link, $pag_list_item);
        $pag_list_item = str_replace("{pag_nbr}", $set_start, $pag_list_item);
        $pag_list_item = str_replace("{pag_active_class}", $active_class, $pag_list_item);
        $arr_pag[] = $pag_list_item;

    }

    $pag_start = $current_page-4;

    if($pag_start < 0) { $pag_start = 0; }
    $arr_pag = array_slice($arr_pag, $pag_start, 5);

    foreach($arr_pag as $pag) {
        $pag_list .= $pag;
    }

    $nextstart = $products_start+1;
    $prevstart = $products_start-1;

    $older_link_query = fc_set_pagination_query($display_mode,$nextstart);
    $newer_link_query = fc_set_pagination_query($display_mode,$prevstart);

    if($prevstart < 1) {
        $prevstart = 1;
        $newer_link_query = '#';
    }

    if($nextstart > $cnt_pages) {
        $older_link_query = '#';
    }


    $tpl_pagination = str_replace("{pag_prev_href}", $newer_link_query, $tpl_pagination);
    $tpl_pagination = str_replace("{pag_next_href}", $older_link_query, $tpl_pagination);
    $tpl_pagination = str_replace("{pagination_list}", $pag_list, $tpl_pagination);
} else {
    $tpl_pagination = '';
}



$show_start = $sql_start+1;
$show_end = $show_start+($products_limit-1);

if($show_end > $cnt_filter_products) {
    $show_end = $cnt_filter_products;
}

//eol pagination

$posts_list = '';
foreach($get_products as $k => $post) {

    $post_releasedate = date($prefs_dateformat,$get_products[$k]['post_releasedate']);
    $post_releasedate_year = date('Y',$get_products[$k]['post_releasedate']);
    $post_releasedate_month = date('m',$get_products[$k]['post_releasedate']);
    $post_releasedate_day = date('d',$get_products[$k]['post_releasedate']);
    $post_releasedate_time = date($prefs_timeformat,$get_products[$k]['post_releasedate']);

    /* entry date */
    $entrydate_year = date('Y',$get_products[$k]['post_date']);

    /* post images */
    $first_post_image = '';
    $post_images = explode("<->", $get_products[$k]['post_images']);
    if($post_images[1] != "") {
        $first_post_image = '/' . $img_path . '/' . str_replace('../content/images/','',$post_images[1]);
    } else if($fc_prefs['prefs_posts_default_banner'] == "without_image") {
        $first_post_image = '';
    } else {
        $first_post_image = "/$img_path/" . $fc_prefs['prefs_posts_default_banner'];
    }



    $this_entry = $tpl_list_p;

    $post_filename = basename($get_products[$k]['post_slug']);
    $post_href = FC_INC_DIR . "/".$target_page[0]."$post_filename-".$get_products[$k]['post_id'].".html";

    $post_teaser = htmlspecialchars_decode($get_products[$k]['post_teaser']);
    $post_text = htmlspecialchars_decode($get_products[$k]['post_text']);

    $post_categories = explode('<->',$get_products[$k]['post_categories']);
    $cat_str = '';
    foreach($all_categories as $cats) {

        $link = $tpl_category_link;

        if(in_array($cats['cat_id'], $post_categories)) {
            $cat_href = '/'.$fct_slug.$cats['cat_name_clean'].'/';
            $link = str_replace('{cat_href}', $cat_href, $link);
            $link = str_replace('{cat_name}', $cats['cat_name'], $link);
            $cat_str .= $link;
        }


    }


    /* vote up or down this post */
    if($get_products[$k]['post_votings'] == 2 || $get_products[$k]['post_votings'] == 3) {

        $voter_data = false;
        $voting_buttons = fc_load_comments_tpl($fc_template,'vote.tpl');
        $voting_type = array("upv","dnv");
        if($get_products[$k]['post_votings'] == 2) {
            if($_SESSION['user_nick'] == '') {
                $voter_data = false;
            } else {
                $voter_data = fc_check_user_legitimacy($get_products[$k]['post_id'],$_SESSION['user_nick'],$voting_type);
            }
        }

        if($get_products[$k]['post_votings'] == 3) {
            if($_SESSION['user_nick'] == '') {
                $voter_name = fc_generate_anonymous_voter();
                $voter_data = fc_check_user_legitimacy($get_products[$k]['post_id'],$voter_name,$voting_type);
            } else {
                $voter_data = fc_check_user_legitimacy($get_products[$k]['post_id'],$_SESSION['user_nick'],$voting_type);
            }
        }

        if($voter_data == true) {
            // user can vote
            $voting_buttons = str_replace('{status_upv}', '', $voting_buttons);
            $voting_buttons = str_replace('{status_dnv}', '', $voting_buttons);
        } else {
            $voting_buttons = str_replace('{status_upv}', 'disabled', $voting_buttons);
            $voting_buttons = str_replace('{status_dnv}', 'disabled', $voting_buttons);
        }

        $voting_buttons = str_replace('{type}', 'post', $voting_buttons);
        $voting_buttons = str_replace('{id}', $get_products[$k]['post_id'], $voting_buttons);

        $votes = fc_get_voting_data('post',$get_products[$k]['post_id']);

        $voting_buttons = str_replace('{nbr_up}', $votes['upv'], $voting_buttons);
        $voting_buttons = str_replace('{nbr_dn}', $votes['dnv'], $voting_buttons);

        $this_entry = str_replace('{post_voting}', $voting_buttons, $this_entry);

    } else {
        $this_entry = str_replace('{post_voting}', '', $this_entry);
    }

    $this_entry = str_replace('{post_id}', $get_products[$k]['post_id'], $this_entry);

    $this_entry = str_replace('{post_author}', $get_products[$k]['post_author'], $this_entry);
    $this_entry = str_replace('{post_title}', $get_products[$k]['post_title'], $this_entry);
    $this_entry = str_replace('{post_teaser}', $post_teaser, $this_entry);
    $this_entry = str_replace('{post_img_src}', $first_post_image, $this_entry);
    $this_entry = str_replace("{post_cats}", $cat_str, $this_entry);

    /* dates */
    $this_entry = str_replace('{post_releasedate}', $post_releasedate, $this_entry);
    $this_entry = str_replace("{post_releasedate_ts}", $get_products[$k]['post_releasedate'], $this_entry); /* timestring */
    $this_entry = str_replace("{post_releasedate}", $post_releasedate, $this_entry);
    $this_entry = str_replace("{post_lastedit}", $post_lastedit, $this_entry);
    $this_entry = str_replace("{post_lastedit_from}", $post_lastedit_from, $this_entry);




        if($get_products[$k]['post_product_tax'] == '1') {
            $tax = $fc_prefs['prefs_posts_products_default_tax'];
        } else if($get_products[$k]['post_product_tax'] == '2') {
            $tax = $fc_prefs['prefs_posts_products_tax_alt1'];
        } else {
            $tax = $fc_prefs['prefs_posts_products_tax_alt2'];
        }

        $post_product_price_addition = $get_products[$k]['post_product_price_addition'];
        if($post_product_price_addition == '') {
            $post_product_price_addition = 0;
        }

        $post_prices = fc_posts_calc_price($get_products[$k]['post_product_price_net'],$post_product_price_addition,$tax);
        $post_price_net = $post_prices['net'];
        $post_price_gross = $post_prices['gross'];

        $this_entry = str_replace("{post_price_gross}", $post_price_gross, $this_entry);
        $this_entry = str_replace("{post_price_net}", $post_price_net, $this_entry);
        $this_entry = str_replace("{post_price_tax}", $tax, $this_entry);
        $this_entry = str_replace("{post_currency}", $get_products[$k]['post_product_currency'], $this_entry);
        $this_entry = str_replace("{post_product_amount}", $get_products[$k]['post_product_amount'], $this_entry);
        $this_entry = str_replace("{post_product_unit}", $get_products[$k]['post_product_unit'], $this_entry);
        $this_entry = str_replace("{post_product_price_label}", $get_products[$k]['post_product_price_label'], $this_entry);
        $this_entry = str_replace("{price_tag_label_gross}", $lang['price_tag_label_gross'], $this_entry);
        $this_entry = str_replace("{price_tag_label_net}", $lang['price_tag_label_net'], $this_entry);

        $this_entry = str_replace("{read_more_text}", $lang['btn_open_product'], $this_entry);
        $this_entry = str_replace("{btn_add_to_cart}", $tpl_btn_add_to_cart, $this_entry);
        $this_entry = str_replace('{post_id}', $get_products[$k]['post_id'], $this_entry);


    $this_entry = str_replace("{read_more_text}", $lang['btn_read_more'], $this_entry);
    $this_entry = str_replace('{post_href}', $post_href, $this_entry);


    $form_action = '/'.$fct_slug.$mod_slug;
    $this_entry = str_replace("{form_action}", $form_action, $this_entry);

    if($get_products[$k]['post_status'] == '2') {
        $draft_msg = '<div style="background:#aaa;color:#fff;padding:5px;margin-top:-5px;margin-left:-5px;margin-bottom:5px;display:inline-block"><small>'.$lang['post_is_draft'].'</small></div>';
        $this_entry  = '<div style="opacity:0.75;border: 1px dotted #aaa; padding: 5px;margin-bottom:15px;">'.$draft_msg.$this_entry.'</div>';
    }



    $posts_list .= $this_entry;

}

$page_content = $tpl_list_index;
$page_content = str_replace('{pagination}', $tpl_pagination, $page_content);
$page_content = str_replace('{post_list}', $posts_list, $page_content);
$page_content = str_replace("{post_cnt}", $cnt_filter_posts, $page_content);
$page_content = str_replace("{lang_entries}", $lang['label_entries'], $page_content);
$page_content = str_replace("{lang_entries_total}", $lang['label_entries_total'], $page_content);
$page_content = str_replace("{post_start_nbr}", $show_start, $page_content);
$page_content = str_replace("{post_end_nbr}", $show_end, $page_content);

if($display_mode == 'list_posts_category') {
    $category_message = str_replace('{categorie}', $selected_category_title, $lang['posts_category_filter']);
    $page_content = str_replace("{category_filter}", $category_message, $page_content);
} else {
    $page_content = str_replace("{category_filter}", '', $page_content);
}


$modul_content = $page_content.$debug_string;
