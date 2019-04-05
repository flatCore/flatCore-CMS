<?php

//prohibit unauthorized access
require 'core/access.php';


$all_mods = get_all_moduls();
$cnt_mods = count($all_mods);

if($cnt_mods > 0) {
	echo '<hr class="shadow">';
	
	echo '<div class="card-columns custom-columns">';
	
	for($i=0;$i<$cnt_mods;$i++) {
		$modFolder = $all_mods[$i]['folder'];
	
		$mod_info_file = "../modules/$modFolder/info.inc.php";
		
		$poster_img = '';
		if(is_file("../modules/$modFolder/backend/poster.jpg")) {
			$poster_img = '<a href="acp.php?tn=moduls&sub='.$modFolder.'&a=start"><img src="../modules/'.$modFolder.'/backend/poster.jpg" class="card-img-top"></a>';
		}
		
		if(is_file("$mod_info_file")) {
			
			unset($mod,$modnav);
			include $mod_info_file;
			
			$mod_id = 'id_'.clean_filename($mod['name']);
						
			echo '<div class="card">';
			echo '<div class="card-header p-1"><strong>'.$mod['name'].'</strong> <span class="badge badge-dark float-right">v'.$mod['version'].'</span></div>';
			echo $poster_img;
						
			echo '<div class="list-group list-group-flush">';
			
			foreach($modnav as $nav) {
				echo '<a class="list-group-item list-group-item-ghost p-1 px-2" href="acp.php?tn=moduls&sub='.$modFolder.'&a='.$nav['file'].'">'.$nav['link'].'</a>';
			}
			
			echo '</div>';			
			echo '</div>';		
			
		}
		
		
		
	
	}
	echo '</div>';


}
?>