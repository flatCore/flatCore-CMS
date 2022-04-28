<?php
error_reporting(E_ALL ^ E_NOTICE);

// get the posting-page by 'type_of_use' and $languagePack
$target_page = $db_content->select("fc_pages", "page_permalink", [
    "AND" => [
        "page_type_of_use" => "display_product",
        "page_language" => $page_contents['page_language']
    ]
]);

if ($target_page[0] == '') {
    $target_page[0] = $fct_slug;
}


$sql_start = ($products_start * $products_limit) - $products_limit;
if ($sql_start < 0) {
    $sql_start = 0;
}

$get_products = fc_get_products($sql_start, $products_limit, $products_filter);
$cnt_filter_products = $get_products[0]['cnt_products_match'];
$cnt_get_products = count($get_products);

$nextPage = $products_start + $products_limit;
$prevPage = $products_start - $products_limit;
$cnt_pages = ceil($cnt_filter_products / $products_limit);

if ($cnt_pages > 1) {
    $show_pagination = true;
    $pagination = array();

    for ($i = 0; $i < $cnt_pages; $i++) {

        $active_class = '';
        $set_start = $i + 1;

        if ($i == 0 && $products_start < 1) {
            $set_start = 1;
            $active_class = 'active';
        }

        if ($set_start == $products_start) {
            $active_class = 'active';
            $current_page = $set_start;
        }

        $pagination_link = fc_set_pagination_query($display_mode, $set_start);

        $pagination[] = array(
            "href" => $pagination_link,
            "nbr" => $set_start,
            "active_class" => $active_class
        );
    }

    $pag_start = $current_page - 4;

    if ($pag_start < 0) {
        $pag_start = 0;
    }
    $pagination = array_slice($pagination, $pag_start, 5);

    $nextstart = $products_start + 1;
    $prevstart = $products_start - 1;

    $older_link_query = fc_set_pagination_query($display_mode, $nextstart);
    $newer_link_query = fc_set_pagination_query($display_mode, $prevstart);

    if ($prevstart < 1) {
        $prevstart = 1;
        $newer_link_query = '#';
    }

    if ($nextstart > $cnt_pages) {
        $older_link_query = '#';
    }

    $smarty->assign('pag_prev_href', $newer_link_query);
    $smarty->assign('pag_next_href', $older_link_query);

} else {
    $show_pagination = false;
}


$show_start = $sql_start + 1;
$show_end = $show_start + ($products_limit - 1);

if ($show_end > $cnt_filter_products) {
    $show_end = $cnt_filter_products;
}

//eol pagination

$posts_list = '';
foreach ($get_products as $k => $post) {

    /* build data for template */

    $get_products[$k]['product_title'] = $get_products[$k]['post_title'];
    $get_products[$k]['product_teaser'] = htmlspecialchars_decode($get_products[$k]['post_teaser']);
    $get_products[$k]['product_text'] = htmlspecialchars_decode($get_products[$k]['post_text']);

    /* post images */
    $first_post_image = '';
    $post_images = explode("<->", $get_products[$k]['post_images']);
    if ($post_images[1] != "") {
        $get_products[$k]['product_img_src'] = '/' . $img_path . '/' . str_replace('../content/images/', '', $post_images[1]);
    } else if ($fc_prefs['prefs_posts_default_banner'] == "without_image") {
        $get_products[$k]['product_img_src'] = '';
    } else {
        $get_products[$k]['product_img_src'] = "/$img_path/" . $fc_prefs['prefs_posts_default_banner'];
    }

    $post_filename = basename($get_products[$k]['post_slug']);
    $get_products[$k]['product_href'] = FC_INC_DIR . "/" . $target_page[0] . "$post_filename-" . $get_products[$k]['post_id'] . ".html";


    $post_releasedate = date($prefs_dateformat, $get_products[$k]['post_releasedate']);
    $post_releasedate_year = date('Y', $get_products[$k]['post_releasedate']);
    $post_releasedate_month = date('m', $get_products[$k]['post_releasedate']);
    $post_releasedate_day = date('d', $get_products[$k]['post_releasedate']);
    $post_releasedate_time = date($prefs_timeformat, $get_products[$k]['post_releasedate']);

    $get_products[$k]['product_releasedate'] = $post_releasedate;

    /* entry date */
    $entrydate_year = date('Y', $get_products[$k]['post_date']);


    /* product categories */
    $post_categories = explode('<->', $get_products[$k]['post_categories']);
    $category = array();
    foreach ($all_categories as $cats) {
        if (in_array($cats['cat_id'], $post_categories)) {
            $cat_href = '/' . $fct_slug . $cats['cat_name_clean'] . '/';
            $category[] = array(
                "cat_href" => $cat_href,
                "cat_title" => $cats['cat_name']
            );
        }
    }
    $get_products[$k]['product_categories'] = $category;

    /* vote up or down this product */
    if ($get_products[$k]['post_votings'] == 2 || $get_products[$k]['post_votings'] == 3) {
        $get_products[$k]['show_voting'] = true;
        $voter_data = false;
        $voting_type = array("upv", "dnv");
        if ($get_products[$k]['post_votings'] == 2) {
            if ($_SESSION['user_nick'] == '') {
                $voter_data = false;
            } else {
                $voter_data = fc_check_user_legitimacy($get_products[$k]['post_id'], $_SESSION['user_nick'], $voting_type);
            }
        }

        if ($get_products[$k]['post_votings'] == 3) {
            if ($_SESSION['user_nick'] == '') {
                $voter_name = fc_generate_anonymous_voter();
                $voter_data = fc_check_user_legitimacy($get_products[$k]['post_id'], $voter_name, $voting_type);
            } else {
                $voter_data = fc_check_user_legitimacy($get_products[$k]['post_id'], $_SESSION['user_nick'], $voting_type);
            }
        }

        if ($voter_data == true) {
            // user can vote
            $get_products[$k]['votes_status_up'] = '';
            $get_products[$k]['votes_status_dn'] = '';
        } else {
            $get_products[$k]['votes_status_up'] = 'disabled';
            $get_products[$k]['votes_status_dn'] = 'disabled';
        }


        $votes = fc_get_voting_data('post', $get_products[$k]['post_id']);

        $get_products[$k]['votes_up'] = (int) $votes['upv'];
        $get_products[$k]['votes_dn'] = (int) $votes['dnv'];

    } else {
        $get_products[$k]['show_voting'] = false;
    }





    if ($get_products[$k]['post_product_tax'] == '1') {
        $tax = $fc_prefs['prefs_posts_products_default_tax'];
    } else if ($get_products[$k]['post_product_tax'] == '2') {
        $tax = $fc_prefs['prefs_posts_products_tax_alt1'];
    } else {
        $tax = $fc_prefs['prefs_posts_products_tax_alt2'];
    }

    $post_product_price_addition = $get_products[$k]['post_product_price_addition'];
    if ($post_product_price_addition == '') {
        $post_product_price_addition = 0;
    }

    $post_prices = fc_posts_calc_price($get_products[$k]['post_product_price_net'], $post_product_price_addition, $tax);
    $post_price_net = $post_prices['net'];
    $post_price_gross = $post_prices['gross'];


    $get_products[$k]['product_price_gross'] = $post_price_gross;
    $get_products[$k]['product_price_net'] = $post_price_net;
    $get_products[$k]['product_price_tax'] = $tax;
    $get_products[$k]['product_currency'] = $get_products[$k]['post_product_currency'];
    $get_products[$k]['product_amount'] = $get_products[$k]['post_product_amount'];
    $get_products[$k]['product_unit'] = $get_products[$k]['post_product_unit'];
    $get_products[$k]['product_price_label'] = $get_products[$k]['post_product_price_label'];
    $get_products[$k]['product_id'] = $get_products[$k]['post_id'];

    $get_products[$k]['product_author'] = $get_products[$k]['post_author'];

    /* show shopping cart button */
    if ($fc_prefs['prefs_posts_products_cart'] == 2 or $fc_prefs['prefs_posts_products_cart'] == 3) {
        $show_cart_btn = true;
    }

    if ($get_products[$k]['post_status'] == '2') {
        $get_products[$k]['draft_message'] = '<div class="alert alert-draft"><small>' . $lang['post_is_draft'] . '</small></div>';
    }

}

/* check if we hide the shopping cart button */
if($fc_prefs['prefs_posts_products_cart'] == 2 OR $fc_prefs['prefs_posts_products_cart'] == 3) {
    $show_shopping_cart = true;
    if($fc_prefs['prefs_posts_products_cart'] == 2 && $_SESSION['user_nick'] == '') {
        $show_shopping_cart = false;
    }
} else {
    $show_shopping_cart = false;
}

if ($display_mode == 'list_posts_category') {
    $category_message = str_replace('{categorie}', $selected_category_title, $lang['posts_category_filter']);
    $page_content = str_replace("{category_filter}", $category_message, $page_content);
} else {
    $page_content = str_replace("{category_filter}", '', $page_content);
}

$form_action = '/' . $fct_slug . $mod_slug;
$smarty->assign('form_action', $form_action);
$smarty->assign('product_cnt', $cnt_filter_products);
$smarty->assign('products', $get_products);

$smarty->assign('pagination', $pagination);

$smarty->assign('show_shopping_cart', $show_shopping_cart);
$smarty->assign('btn_add_to_cart', $lang['btn_add_to_cart']);
$smarty->assign('btn_read_more', $lang['btn_open_product']);

$products_page = $smarty->fetch("products-list.tpl", $cache_id);
$smarty->assign('page_content', $products_page, true);
