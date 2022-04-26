<?php
//error_reporting(E_ALL ^E_NOTICE);

$post_data = fc_get_post_data($get_product_id);

$post_images = explode("<->", $post_data['post_images']);

$post_releasedate = date("$prefs_dateformat $prefs_timeformat",$post_data['post_releasedate']);
$post_releasedate_year = date('Y',$post_data['post_releasedate']);
$post_releasedate_month = date('m',$post_data['post_releasedate']);
$post_releasedate_day = date('d',$post_data['post_releasedate']);
$post_releasedate_time = date('H:i:s',$post_data['post_releasedate']);

$post_lastedit = date('Y-m-d H:i',$post_data['lastedit']);
$post_lastedit_from = $post_data['post_lastedit_from'];

/* categories */
$tpl_category_link = fc_load_posts_tpl($fc_template,'link-categories.tpl');
$cat_links_array = explode('<->',$post_data['post_categories']);

foreach($all_categories as $cats) {

    $link = $tpl_category_link;

    if(in_array($cats['cat_id'],$cat_links_array)) {
        $post_cats_string .= $cats['cat_name'] .' ';
        $cat_href = '/'.$fct_slug.$cats['cat_name_clean'].'/';
        $link = str_replace('{cat_href}', $cat_href, $link);
        $link = str_replace('{cat_name}', $cats['cat_name'], $link);
        $post_cats_btn .= $link;

    }
}


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

$hits = (int) $post_data['hits'];
fc_increase_posts_hits($get_product_id);


$this_entry = fc_load_posts_tpl($fc_template,'post-display-p.tpl');

$post_teaser = htmlspecialchars_decode($post_data['post_teaser']);
$post_text = htmlspecialchars_decode($post_data['post_text']);


/* vote up or down this post */
if($post_data['post_votings'] == 2 || $post_data['post_votings'] == 3) {

    $voter_data = false;
    $voting_buttons = fc_load_comments_tpl($fc_template,'vote.tpl');
    $voting_type = array("upv","dnv");
    if($post_data['post_votings'] == 2) {
        if($_SESSION['user_nick'] == '') {
            $voter_data = false;
        } else {
            $voter_data = fc_check_user_legitimacy($post_data['post_id'],$_SESSION['user_nick'],$voting_type);
        }
    }

    if($post_data['post_votings'] == 3) {
        if($_SESSION['user_nick'] == '') {
            $voter_name = fc_generate_anonymous_voter();
            $voter_data = fc_check_user_legitimacy($post_data['post_id'],$voter_name,$voting_type);
        } else {
            $voter_data = fc_check_user_legitimacy($post_data['post_id'],$_SESSION['user_nick'],$voting_type);
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
    $voting_buttons = str_replace('{id}', $post_data['post_id'], $voting_buttons);

    $votes = fc_get_voting_data('post',$post_data['post_id']);
    $voting_buttons = str_replace('{nbr_up}', $votes['upv'], $voting_buttons);
    $voting_buttons = str_replace('{nbr_dn}', $votes['dnv'], $voting_buttons);

    $this_entry = str_replace('{post_voting}', $voting_buttons, $this_entry);

} else {
    $this_entry = str_replace('{post_voting}', '', $this_entry);
}


$this_entry = str_replace("{post_id}", $post_data['post_id'], $this_entry);
$this_entry = str_replace("{post_author}", $post_data['post_author'], $this_entry);
$this_entry = str_replace('{post_title}', $post_data['post_title'], $this_entry);
$this_entry = str_replace('{post_teaser}', $post_teaser, $this_entry);
$this_entry = str_replace('{post_text}', $post_text, $this_entry);
$this_entry = str_replace("{post_type}", $post_data['post_type'], $this_entry);
$this_entry = str_replace('{post_img_src}', $first_post_image, $this_entry);
$this_entry = str_replace('{post_img_caption}', $post_image_data['media_text'], $this_entry);

$this_entry = str_replace("{post_source}", $post_data['post_source'], $this_entry);
$this_entry = str_replace("{post_product_manufacturer}", $post_data['post_product_manufacturer'], $this_entry);
$this_entry = str_replace("{post_product_supplier}", $post_data['post_product_supplier'], $this_entry);
$this_entry = str_replace("{post_product_number}", $post_data['post_product_number'], $this_entry);

$this_entry = str_replace("{post_releasedate_ts}", $post_data['post_releasedate'], $this_entry); /* timestring */
$this_entry = str_replace("{post_releasedate}", $post_releasedate, $this_entry);
$this_entry = str_replace("{post_releasedate_year}", $post_releasedate_year, $this_entry);
$this_entry = str_replace("{post_releasedate_month}", $post_releasedate_month, $this_entry);
$this_entry = str_replace("{post_releasedate_day}", $post_releasedate_day, $this_entry);
$this_entry = str_replace("{post_releasedate_time}", $post_releasedate_time, $this_entry);

$this_entry = str_replace("{post_lastedit}", $post_lastedit, $this_entry);
$this_entry = str_replace("{post_lastedit_from}", $post_lastedit_from, $this_entry);

$this_entry = str_replace("{post_cats}", $post_cats_btn, $this_entry);
$this_entry = str_replace("{post_cats_string}", $post_cats_string, $this_entry);
$this_entry = str_replace("{back_to_overview}", $lang['back_to_overview'], $this_entry);
$this_entry = str_replace("{back_link}", "/$fct_slug", $this_entry);


$form_action = '/'.$fct_slug.$mod_slug;
$this_entry = str_replace("{form_action}", $form_action, $this_entry);



/**
 * check if we show the shopping cart
 */
$tpl_btn_add_to_cart = '';
if($fc_prefs['prefs_posts_products_cart'] == 2 OR $fc_prefs['prefs_posts_products_cart'] == 3) {
    $tpl_btn_add_to_cart = fc_load_posts_tpl($fc_template,'btn-add-to-cart.tpl');
    $tpl_btn_add_to_cart = str_replace('{btn_add_to_cart}', $lang['btn_add_to_cart'], $tpl_btn_add_to_cart);
}
$this_entry = str_replace("{btn_add_to_cart}", $tpl_btn_add_to_cart, $this_entry);

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

if($post_data['post_product_price_net_s1'] != '') {

    $tpl_prices_list = fc_load_posts_tpl($fc_template,'prices_list.tpl');

    $table_discounts  = '';

    for($i=1;$i<6;$i++) {

        $table_discounts .= '<tr>';

        $price_key = 'post_product_price_net_s'.$i;
        $amount_key = 'post_product_amount_s'.$i;

        if($post_data[$price_key] == '') {
            continue;
        }

        $post_prices = fc_posts_calc_price($post_data[$price_key],$post_product_price_addition,$tax);
        $price_scaled[$i]['net'] = $post_prices['net'];
        $price_scaled[$i]['gross'] = $post_prices['gross'];

        $table_discounts .= '<td>'.$post_data[$amount_key].' {post_product_unit}</td>';
        $table_discounts .= '<td>'.$price_scaled[$i]['gross'].' {post_currency}</td>';
        $table_discounts .= '</tr>';
    }

    $tpl_prices_list = str_replace('{post_prices_discount}', $table_discounts, $tpl_prices_list);
    $tpl_prices_list = str_replace('{label_prices_discount}', $lang['label_prices_discount'], $tpl_prices_list);
    $this_entry = str_replace("{tpl_prices_discount}", $tpl_prices_list, $this_entry);

} else {
    $this_entry = str_replace("{tpl_prices_discount}", '', $this_entry);
}


$this_entry = str_replace("{post_prices_discount}", $table_discounts, $this_entry);
$this_entry = str_replace("{post_price_gross}", $post_price_gross, $this_entry);
$this_entry = str_replace("{post_price_net}", $post_price_net_calculated, $this_entry);
$this_entry = str_replace("{post_price_tax}", $tax, $this_entry);
$this_entry = str_replace("{post_currency}", $post_data['post_product_currency'], $this_entry);
$this_entry = str_replace("{post_product_unit}", $post_data['post_product_unit'], $this_entry);
$this_entry = str_replace("{post_product_amount}", $post_data['post_product_amount'], $this_entry);
$this_entry = str_replace("{post_product_price_label}", $post_data['post_product_price_label'], $this_entry);
$this_entry = str_replace("{price_tag_label_gross}", $lang['price_tag_label_gross'], $this_entry);
$this_entry = str_replace("{price_tag_label_net}", $lang['price_tag_label_net'], $this_entry);

if($post_data['post_product_textlib_content'] != 'no_snippet') {
    $textlib_content = get_textlib($post_data['post_product_textlib_content'],$languagePack);
    $this_entry = str_replace("{post_snippet_text}", $textlib_content, $this_entry);
} else {
    $this_entry = str_replace("{post_snippet_text}", '', $this_entry);
}

if($post_data['post_product_textlib_price'] != 'no_snippet') {
    $tpl_snippet_discounts = fc_load_posts_tpl($fc_template,'prices_snippet.tpl');
    $textlib_price = get_textlib($post_data['post_product_textlib_price'],$languagePack);
    $tpl_snippet_discounts = str_replace("{post_snippet_price}", $textlib_price, $tpl_snippet_discounts);
    $tpl_snippet_discounts = str_replace("{label_prices_snippet}", $lang['label_prices_snippet'], $tpl_snippet_discounts);
    $this_entry = str_replace("{tpl_snippet_price}", $tpl_snippet_discounts, $this_entry);
} else {
    $this_entry = str_replace("{tpl_snippet_price}", '', $this_entry);
}

$this_entry = str_replace("{post_thumbnails}", $thumbnails_str, $this_entry);

$this_entry = str_replace('{post_id}', $get_product_id, $this_entry);
$form_action = '/'.$fct_slug.$mod_slug;
$this_entry = str_replace("{form_action}", $form_action, $this_entry);


if($post_data['post_meta_title'] == '') {
    $post_data['post_meta_title'] = $post_data['post_title'];
}

if($post_data['post_meta_description'] == '') {
    $post_data['post_meta_description'] = substr(strip_tags($post_teaser),0,160);
}

$page_contents['page_title'] = $post_data['post_meta_title'];
$page_contents['page_meta_description'] = $post_data['post_meta_description'];
$page_contents['page_meta_keywords'] = $post_data['post_tags'];
$page_contents['page_thumbnail'] = $fc_base_url.$img_path.'/'.basename($first_post_image);

$modul_content = $this_entry.$debug_string;