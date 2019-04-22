<?php

/* defaultTheme options */

if(defined('FC_SOURCE') && FC_SOURCE === 'frontend') {

 /**
  * for example manipulate page values
  * $page_title = 'Extended title by defaultTheme options file: ' . $page_title;
  * $smarty->assign('page_title', "$page_title");
  */
  

 
} elseif(defined('FC_SOURCE') && FC_SOURCE == 'backend') {

	/**
	 * defaultTheme options for acp > system > layout & design
	 *
	 */

	$readme = file_get_contents('../styles/default/readme.html');
	echo $readme;
	
}

?>