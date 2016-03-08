<div id="liveBox">
	<?php
		
		$helpURL = 'http://docs.flatcore.org/en/';
		
		if($languagePack == 'de') {
			$helpURL = 'http://docs.flatcore.org/de/';	
		}
		
		echo '<a class="fancybox-docs" href="'.$helpURL.'"><span class="glyphicon glyphicon-question-sign"></span> '.$lang['show_help'].'</a>';
		
	?>		
		
	<a href='../'><span class="glyphicon glyphicon-home"></span>  <?php echo $lang['back_to_page']; ?></a>
	<hr>
	<a href='../index.php?goto=logout'><span class="glyphicon glyphicon-off"></span> <?php echo $lang['logout']; ?></a>
</div>