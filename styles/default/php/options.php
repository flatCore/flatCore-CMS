<?php

/* defaultTheme options */

if(defined('FC_SOURCE') && FC_SOURCE === 'frontend') {

 /**
  * for example manipulate page values
  * $page_title = 'Extended title by defaultTheme options file: ' . $page_title;
  * $smarty->assign('page_title', "$page_title");
  */
  
  $theme_options = fc_get_theme_options("default");
  
  $get_fb_key = array_search("theme_facebook", array_column($theme_options, 'theme_label'));
  $facebook_page = $theme_options[$get_fb_key]['theme_value'];
  
  $get_tw_key = array_search("theme_twitter", array_column($theme_options, 'theme_label'));
  $twitter_page = $theme_options[$get_tw_key]['theme_value'];
  
  $get_insta_key = array_search("theme_instagram", array_column($theme_options, 'theme_label'));
  $insta_page = $theme_options[$get_insta_key]['theme_value'];
  
  if($facebook_page != '') {
	  $fb_link = '<a class="p-1" href="https://www.facebook.com/'.$facebook_page.'"><i class="bi bi-facebook"></i></a> ';
	  $smarty->assign('fb_link', "$fb_link");
  }
  
  if($twitter_page != '') {
	  $tw_link = '<a class="p-1" href="https://twitter.com/'.$twitter_page.'"><i class="bi bi-twitter"></i></a> ';
	  $smarty->assign('tw_link', "$tw_link");
  }
  
  if($insta_page != '') {
	  $insta_link = '<a class="p-1" href="https://instagram.com/'.$insta_page.'"><i class="bi bi-instagram"></i></a> ';
	  $smarty->assign('insta_link', "$insta_link");
  }


 
} elseif(defined('FC_SOURCE') && FC_SOURCE == 'backend') {

	/**
	 * defaultTheme options for acp > system > layout & design
	 *
	 */

	 $theme_options = array(
		 "twitter" => "Twitter Account",
		 "facebook" => "Facebook Account",
		 "instagram" => "Instagram Account"
	 );
	
}

?>