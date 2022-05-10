<?php
error_reporting(E_ALL ^E_NOTICE);


$post_data = fc_get_post_data($get_post_id);

$hits = (int) $post_data['hits'];
fc_increase_posts_hits($get_product_id);

$post_teaser = htmlspecialchars_decode($post_data['post_teaser']);
$post_text = htmlspecialchars_decode($post_data['post_text']);

$post_images = explode("<->", $post_data['post_images']);


$post_releasedate_str = date("$prefs_dateformat $prefs_timeformat",$post_data['post_releasedate']);
$post_releasedate_year = date('Y',$post_data['post_releasedate']);
$post_releasedate_month = date('m',$post_data['post_releasedate']);
$post_releasedate_day = date('d',$post_data['post_releasedate']);
$post_releasedate_time = date('H:i:s',$post_data['post_releasedate']);

$post_lastedit = date('Y-m-d H:i',$post_data['lastedit']);
$post_lastedit_from = $post_data['post_lastedit_from'];

/* categories */
$cat_links_array = explode('<->',$post_data['post_categories']);

foreach($all_categories as $cats) {

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




if($post_data['post_type'] == 'g') {

	$gallery_dir = 'content/galleries/'.$entrydate_year.'/gallery'.$post_data['post_id'].'/';
	$fp = $gallery_dir.'*_tmb.jpg';
	$thumbs_array = glob("$fp");
	arsort($thumbs_array);
	$cnt_thumbs_array = count($thumbs_array);
    $gallery_thumbs = array();
	if($cnt_thumbs_array > 0) {

		$x = 0;
		foreach($thumbs_array as $tmb) {
			$x++;
			$tmb_src = '/'.$tmb;
            $img_src = str_replace('_tmb','_img',$tmb_src);
            $gallery_thumbs[] = array(
                "tmb_src" => $tmb_src,
                "img_src" => $img_src
            );
		}
	}

} else if($post_data['post_type'] == 'v') {
	$vURL = parse_url($post_data['post_video_url']);
	parse_str($vURL['query'],$video); //$video['v'] -> youtube video id
    $smarty->assign('video_id', $video['v']);
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


$this_entry = str_replace('{post_img_src}', $first_post_image, $this_entry);
$this_entry = str_replace('{post_img_caption}', $post_image_data['media_text'], $this_entry);

$this_entry = str_replace("{post_source}", $post_data['post_source'], $this_entry);


$this_entry = str_replace("{post_releasedate_ts}", $post_data['post_releasedate'], $this_entry); /* timestring */
$this_entry = str_replace("{post_releasedate}", $post_releasedate, $this_entry);
$this_entry = str_replace("{post_releasedate_year}", $post_releasedate_year, $this_entry);
$this_entry = str_replace("{post_releasedate_month}", $post_releasedate_month, $this_entry);
$this_entry = str_replace("{post_releasedate_day}", $post_releasedate_day, $this_entry);
$this_entry = str_replace("{post_releasedate_time}", $post_releasedate_time, $this_entry);




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



$form_action = '/'.$fct_slug.$mod_slug;

$redirect = '?goto='.$post_data['post_id'];
$smarty->assign('post_external_link', $post_data['post_link']);
$smarty->assign('post_external_redirect', $redirect);

$post_teaser = htmlspecialchars_decode($post_data['post_teaser']);
$post_text = htmlspecialchars_decode($post_data['post_text']);

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


$smarty->assign('post_id', $get_post_id);
$smarty->assign('post_type', $post_data['post_type']);

$smarty->assign('post_title', $post_data['post_title']);
$smarty->assign('post_teaser', $post_teaser);
$smarty->assign('post_text', $post_text);

$smarty->assign('post_author', $post_data['post_author']);
$smarty->assign('post_releasedate_str', $post_releasedate_str);

$smarty->assign('votes_status_up', $post_data['votes_status_up']);
$smarty->assign('votes_status_dn', $post_data['votes_status_dn']);
$smarty->assign('votes_up', $post_data['votes_up']);
$smarty->assign('votes_dn', $post_data['votes_dn']);
$smarty->assign('show_voting', $show_voting);

$smarty->assign('post_tmb_src', $first_post_image);
$smarty->assign('gallery_thumbs', $gallery_thumbs);

$smarty->assign('form_action', $form_action);
$smarty->assign('btn_download', $lang['btn_download']);

$posts_page = $smarty->fetch("posts-display.tpl", $cache_id);
$smarty->assign('page_content', $posts_page, true);