<?php
//error_reporting(E_ALL ^E_NOTICE);


$post_data = fc_get_post_data($get_post_id);

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


/* event dates */

$event_start_day = date('d',$post_data['post_event_startdate']);
$event_start_month = date('m',$post_data['post_event_startdate']);
$event_start_month_text = $lang["m$event_start_month"];
$event_start_year = date('Y',$post_data['post_event_startdate']);
$event_end_day = date('d',$post_data['post_event_enddate']);
$event_end_month = date('m',$post_data['post_event_enddate']);
$event_end_year = date('Y',$post_data['post_event_enddate']);

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
fc_increase_posts_hits($get_post_id);


if($post_data['post_type'] == 'm') {
	if($first_post_image != "") {
		$this_entry = fc_load_posts_tpl($fc_template,'post-display-m.tpl');
	} else {
		$this_entry = fc_load_posts_tpl($fc_template,'post-display-m-wo.tpl');
	}
} else if($post_data['post_type'] == 'i') {
	$this_entry = fc_load_posts_tpl($fc_template,'post-display-i.tpl');
} else if($post_data['post_type'] == 'g') {
	$this_entry = fc_load_posts_tpl($fc_template,'post-display-g.tpl');
	
	$gallery_dir = 'content/galleries/'.$entrydate_year.'/gallery'.$post_data['post_id'].'/';
	$fp = $gallery_dir.'*_tmb.jpg';
	$tmb_tpl = fc_load_posts_tpl($fc_template,'thumbnail.tpl');
	$thumbs_array = glob("$fp");
	arsort($thumbs_array);
	$cnt_thumbs_array = count($thumbs_array);
	if($cnt_thumbs_array > 0) {
		
		$first_post_image = "/" . str_replace('_tmb','_img',$thumbs_array[0]);
		
		$thumbnails_str = '';
		$x = 0;
		foreach($thumbs_array as $tmb) {
			$x++;
			$tmb_str = $tmb_tpl;
			$tmb_src = '/'.$tmb;
			$img_src = str_replace('_tmb','_img',$tmb_src);
			$tmb_str = str_replace('{tmb_src}', $tmb_src, $tmb_str);
			$tmb_str = str_replace('{img_src}', $img_src, $tmb_str);
			$thumbnails_str .= $tmb_str;
		}
	}

} else if($post_data['post_type'] == 'v') {
	$this_entry = fc_load_posts_tpl($fc_template,'post-display-v.tpl');
	$vURL = parse_url($post_data['post_video_url']);
	parse_str($vURL['query'],$video); //$video['v'] -> youtube video id
} else if($post_data['post_type'] == 'e') {
	$this_entry = fc_load_posts_tpl($fc_template,'post-display-e.tpl');
} else if($post_data['post_type'] == 'l') {
	$this_entry = fc_load_posts_tpl($fc_template,'post-display-l.tpl');
} else if($post_data['post_type'] == 'p') {
	$this_entry = fc_load_posts_tpl($fc_template,'post-display-p.tpl');
} else if($post_data['post_type'] == 'f') {
	$this_entry = fc_load_posts_tpl($fc_template,'post-display-f.tpl');
}

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

/* show guestlist */
if($post_data['post_event_guestlist'] == 2 OR $post_data['post_event_guestlist'] == 3) {
	
	$guestlist = fc_load_comments_tpl($fc_template,'guestlist.tpl');
	
	if($post_data['post_event_guestlist'] == 2 AND $_SESSION['user_nick'] == '') {
		/* only registered user can confirm */
		$guestlist = str_replace('{disabled}', 'disabled', $guestlist);
	} else {
		$guestlist = str_replace('{disabled}', '', $guestlist);
	}
	
	if($post_data['post_event_guestlist_limit'] != '') {
		$guestlist = str_replace("{label_nbr_total_available}", $lang['guestlist_label_nbr_total_available'], $guestlist);
		$guestlist = str_replace("{nbr_available_total}", $post_data['post_event_guestlist_limit'], $guestlist);
	} else {
		$guestlist = str_replace("{label_nbr_total_available}", '', $guestlist);
		$guestlist = str_replace("{nbr_available_total}", '', $guestlist);		
	}
	
	if($post_data['post_event_guestlist_public_nbr'] == 2) {
		$cnt_commitments = fc_get_event_confirmation_data($post_data['post_id']);
		$guestlist = str_replace("{label_nbr_commitments}", $lang['guestlist_label_nbr_commitments'], $guestlist);
		$guestlist = str_replace("{nbr_commitments}", $cnt_commitments['evc'], $guestlist);
	} else {
		$guestlist = str_replace("{label_nbr_commitments}", '', $guestlist);
		$guestlist = str_replace("{nbr_commitments}", '', $guestlist);
	}
	
	
	$this_entry = str_replace("{post_guestlist}", $guestlist, $this_entry);
	$this_entry = str_replace('{id}', $post_data['post_id'], $this_entry);
	$this_entry = str_replace("{label_guestlist}", $lang['label_guestlist'], $this_entry);
	$this_entry = str_replace("{description_guestlist}", $lang['guestlist_description'], $this_entry);
	$this_entry = str_replace("{sign_guestlist}", $lang['btn_guestlist_sign'], $this_entry);	
} else {
	$this_entry = str_replace("{post_guestlist}", '', $this_entry);
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
$this_entry = str_replace("{event_start_day}", $event_start_day, $this_entry);
$this_entry = str_replace("{event_start_month}", $event_start_month, $this_entry);
$this_entry = str_replace("{event_start_month_text}", $event_start_month_text, $this_entry);
$this_entry = str_replace("{event_start_year}", $event_start_year, $this_entry);
$this_entry = str_replace("{event_end_day}", $event_end_day, $this_entry);
$this_entry = str_replace("{event_end_month}", $event_end_month, $this_entry);
$this_entry = str_replace("{event_end_year}", $event_end_year, $this_entry);
$this_entry = str_replace("{post_tpl_event_hotline}", $tpl_hotline, $this_entry);
$this_entry = str_replace("{post_event_hotline}", $post_data['post_event_hotline'], $this_entry);
$this_entry = str_replace("{post_event_price_note}", $post_data['post_event_price_note'], $this_entry);
$this_entry = str_replace("{post_tpl_event_prices}", $price_list, $this_entry);

$this_entry = str_replace("{video_id}", $video['v'], $this_entry);

$this_entry = str_replace("{post_external_link}", $post_data['post_link'], $this_entry);
$redirect = '?goto='.$post_data['post_id'];
$this_entry = str_replace("{post_external_redirect}", $redirect, $this_entry);


$this_entry = str_replace("{post_cats}", $post_cats_btn, $this_entry);
$this_entry = str_replace("{post_cats_string}", $post_cats_string, $this_entry);
$this_entry = str_replace("{back_to_overview}", $lang['back_to_overview'], $this_entry);
$this_entry = str_replace("{back_link}", "/$fct_slug", $this_entry);

/* file */
$this_entry = str_replace("{lang_download}", $lang['btn_download'], $this_entry);
$this_entry = str_replace("{post_file_version}", $post_data['post_file_version'], $this_entry);
$this_entry = str_replace("{post_file_license}", $post_data['post_file_license'], $this_entry);
$filepath = str_replace('../','/',$post_data['post_file_attachment']);
$this_entry = str_replace("{post_file_attachment}", $filepath, $this_entry);
$this_entry = str_replace("{post_file_attachment_external}", $post_data['post_file_attachment_external'], $this_entry);

$form_action = '/'.$fct_slug.$mod_slug;
$this_entry = str_replace("{form_action}", $form_action, $this_entry);



/* products */
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


$page_contents['page_title'] = $post_data['post_title'];
$page_contents['page_meta_description'] = substr(strip_tags($post_teaser),0,160);
$page_contents['page_meta_keywords'] = $post_data['post_tags'];
$page_contents['page_thumbnail'] = $fc_base_url.$img_path.'/'.basename($first_post_image);

$modul_content = $this_entry.$debug_string;

?>