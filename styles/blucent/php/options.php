<?php

/* blucent options */

if(defined('FC_SOURCE') && FC_SOURCE === 'frontend') {

 /**
  * for example manipulate page values
  * $page_title = 'Extended title by blucent options file: ' . $page_title;
  * $smarty->assign('page_title', "$page_title");
  */
 
} elseif(defined('FC_SOURCE') && FC_SOURCE == 'backend') {

	/**
	 * blucent options for acp > system > layout & design
	 *
	 */
	
	echo '<p class="alert alert-info">This Style has no more options.</p>';
	$readme = file_get_contents('../styles/blucent/readme.html');
	echo $readme;
	
}

?>