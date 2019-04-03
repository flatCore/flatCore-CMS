<?php

//prohibit unauthorized access
require 'core/access.php';


$all_mods = get_all_moduls();
$cnt_mods = count($all_mods);

if($cnt_mods > 0) {
	echo '<hr class="shadow">';
	
	echo '<div class="row">';
	for($i=0;$i<$cnt_mods;$i++) {
		$modFolder = $all_mods[$i]['folder'];
	
		$mod_info_file = "../modules/$modFolder/info.inc.php";
		
		
		
		if(is_file("$mod_info_file")) {
			
			unset($mod,$modnav);
			include $mod_info_file;
			
			$mod_id = 'id_'.clean_filename($mod['name']);
			
			echo '<div class="col-md m-1">';
			
			echo '<div class="card">';
			echo '<div class="card-header"><a data-toggle="collapse" href="#" data-target="#'.$mod_id.'">'.$icon['angle_down'].' '.$mod['name'].'</a> <span class="badge badge-dark float-right">v'.$mod['version'].'</span></div>';

			echo '<div class="card-body text-center p-1 collapse" id="'.$mod_id.'">';
			
			foreach($modnav as $nav) {
				echo '<a class="btn btn-dark btn-sm m-1" href="acp.php?tn=moduls&sub='.$modFolder.'&a='.$nav['file'].'">'.$nav['link'].'</a>';
			}
			
			
			echo '</div>';
			echo '</div>';
			
			echo '</div>';
			
			if($i % 2 == 0 && $i > 0) {
				echo '<div class="w-100"></div>';
			}
			
			
		}
		
		
		
	
	}
	echo '</div>';


}
?>