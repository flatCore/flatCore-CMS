<div id="liveBox">
	<?php
		
		$helpURL = 'https://flatcore.org/docs/en/';
		
		if($languagePack == 'de') {
			$helpURL = 'https://flatcore.org/docs/de/';	
		}
		
		echo '<a class="fancybox-docs" href="'.$helpURL.'"><span class="glyphicon glyphicon-question-sign"></span> '.$lang['show_help'].'</a>';
		
	?>		
		
	<a href='../'><span class="glyphicon glyphicon-home"></span>  <?php echo $lang['back_to_page']; ?></a>
	<hr>
	<a href='../index.php?goto=logout'><span class="glyphicon glyphicon-off"></span> <?php echo $lang['logout']; ?></a>
</div>