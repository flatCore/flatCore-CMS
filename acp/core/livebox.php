<div class="sidebar-footer">
	<?php
		
		$helpURL = 'https://flatcore.org/documentation/';
		
		if($languagePack == 'de') {
			$helpURL = 'https://flatcore.org/de/dokumentation/';	
		}
		
		echo '<a target="_blank" href="'.$helpURL.'">'.$icon['question'].' '.$lang['show_help'].'</a>';
		
		echo '<a href="../">'.$icon['home'].' '.$lang['back_to_page'].'</a>';
		echo '<a href="../index.php?goto=logout">'.$icon['sign_out_alt'].' '.$lang['logout'].'</a>';
		
	?>
</div>