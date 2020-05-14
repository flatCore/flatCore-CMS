<?php

if(FC_SOURCE == 'frontend') {
	include $languagePack.'/dict-frontend.php';
	$extend_lf = FC_CONTENT_DIR.'/plugins/lang_'.$languagePack.'.php';
} elseif(FC_SOURCE == 'backend') {
	include 'en/dict-backend.php';
	if($languagePack != 'en') {
		include $languagePack.'/dict-backend.php';
	}
	$extend_lf = '../' . FC_CONTENT_DIR.'/plugins/lang_'.$languagePack.'.php';
} else {
	die();
}

if(is_file($extend_lf)) {
	include $extend_lf;
}

?>