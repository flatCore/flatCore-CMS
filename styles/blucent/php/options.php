<?php

/* blucent options */

if(defined('FC_SOURCE') && FC_SOURCE === 'frontend') {

 /**
  * for example manipulate page values
  * $page_title = 'Extended title by blucent options file: ' . $page_title;
  * $smarty->assign('page_title', "$page_title");
  */
  
  if(is_file('./content/images/teaser_background_image.png')) {
	  $smarty->assign('teaser_background_image', " style='background-image: url(../content/images/teaser_background_image.png)' ");
  }
 
  if(is_file('./content/images/teaser_background_image_'.$page_id.'.png')) {
	  $smarty->assign('teaser_background_image', " style='background-image: url(../content/images/teaser_background_image_$page_id.png)' ");
  }
 
} elseif(defined('FC_SOURCE') && FC_SOURCE == 'backend') {

	/**
	 * blucent options for acp > system > layout & design
	 *
	 */

	$readme = file_get_contents('../styles/blucent/readme.html');
	echo $readme;
	
}

?>