<?php
//error_reporting(E_ALL ^E_NOTICE);


$post_data = fc_get_post_data($get_post_id);

$post_images = explode("<->", $post_data['post_images']);


$post_releasedate = date('Y-m-d H:i',$post_data['post_releasedate']);
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
		$cat_href = '/'.$fct_slug.$cats['cat_name_clean'];
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
} else if($fc_prefs['prefs_posts_default_banner'] == "without_image") {
	$first_post_image = '';
} else {
	$first_post_image = "/$img_path/" . $fc_prefs['prefs_posts_default_banner'];
}

$hits = (int) $post_data['hits'];
$hits++;



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

$this_entry = str_replace("{post_id}", $post_data['post_id'], $this_entry);
$this_entry = str_replace("{post_author}", $post_data['post_author'], $this_entry);
$this_entry = str_replace('{post_title}', $post_data['post_title'], $this_entry);
$this_entry = str_replace('{post_teaser}', $post_teaser, $this_entry);
$this_entry = str_replace('{post_text}', $post_text, $this_entry);
$this_entry = str_replace("{post_type}", $post_data['post_type'], $this_entry);
$this_entry = str_replace('{post_img_src}', $first_post_image, $this_entry);

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

/* products */
if($post_data['post_product_tax'] == '1') {
	$tax = $fc_prefs['prefs_posts_products_default_tax'];
} else if($post_data['post_product_tax'] == '2') {
	$tax = $fc_prefs['prefs_posts_products_tax_alt1'];
} else {
	$tax = $fc_prefs['prefs_posts_products_tax_alt2'];
}

$post_product_price_net = str_replace('.', '', $post_data['post_product_price_net']);
$post_product_price_net = str_replace(',', '.', $post_product_price_net);

$post_price_gross = $post_product_price_net*($tax+100)/100;
$post_price_gross = fc_post_print_currency($post_price_gross);
$post_price_net = fc_post_print_currency($post_product_price_net);
$this_entry = str_replace("{post_price_gross}", $post_price_gross, $this_entry);
$this_entry = str_replace("{post_price_net}", $post_price_net, $this_entry);
$this_entry = str_replace("{post_price_tax}", $tax, $this_entry);
$this_entry = str_replace("{post_currency}", $post_data['post_product_currency'], $this_entry);
$this_entry = str_replace("{post_product_unit}", $post_data['post_product_unit'], $this_entry);
$this_entry = str_replace("{post_product_amount}", $post_data['post_product_amount'], $this_entry);
$this_entry = str_replace("{post_product_price_label}", $post_data['post_product_price_label'], $this_entry);

if($post_data['posts_product_textlib_content'] != 'no_snippet') {
	$textlib_content = get_textlib($post_data['posts_product_textlib_content'],$languagePack);
	$this_entry = str_replace("{post_snippet_text}", $textlib_content, $this_entry);
} else {
	$this_entry = str_replace("{post_snippet_text}", '', $this_entry);
}

if($post_data['posts_product_textlib_price'] != 'no_snippet') {
	$textlib_price = get_textlib($post_data['posts_product_textlib_price'],$languagePack);
	$this_entry = str_replace("{post_snippet_price}", $textlib_price, $this_entry);
} else {
	$this_entry = str_replace("{post_snippet_price}", '', $this_entry);
}

$this_entry = str_replace("{post_thumbnails}", $thumbnails_str, $this_entry);


$page_contents['page_title'] = $post_data['post_title'];
$page_contents['page_meta_description'] = substr(strip_tags($post_teaser),0,160);
$page_contents['page_meta_keywords'] = $post_data['post_tags'];
$page_contents['page_thumbnail'] = '/'.$img_path.'/'.basename($first_post_image);

$modul_content = $this_entry.$debug_string;

?>