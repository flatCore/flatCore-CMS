<?php
	
//prohibit unauthorized access
require("core/access.php");


if(!is_file('../.htaccess')) {
	echo '<div class="alert alert-danger">'.$lang['alert_no_htaccess'].'</div>';
}

if(!is_dir('../'.FC_CONTENT_DIR.'/cache/cache/')) {
	mkdir('../'.FC_CONTENT_DIR.'/cache/cache/', 0777);
}

if(!is_dir('../'.FC_CONTENT_DIR.'/cache/templates_c/')) {
	mkdir('../'.FC_CONTENT_DIR.'/cache/templates_c/', 0777);
}

$writable_items = array(
	'../sitemap.xml',
	'../'.FC_CONTENT_DIR.'/',
	'../'.FC_CONTENT_DIR.'/avatars/',
	'../'.FC_CONTENT_DIR.'/cache/',
	'../'.FC_CONTENT_DIR.'/cache/cache/',
	'../'.FC_CONTENT_DIR.'/cache/templates_c/',
	'../'.FC_CONTENT_DIR.'/files/',
	'../'.FC_CONTENT_DIR.'/images/',
	'../'.FC_CONTENT_DIR.'/SQLite/',
	'../'.FC_CONTENT_DIR.'/SQLite/content.sqlite3',
	'../'.FC_CONTENT_DIR.'/SQLite/flatTracker.sqlite3',
	'../'.FC_CONTENT_DIR.'/SQLite/user.sqlite3'
);

foreach($writable_items as $f) {
	
	if(($f == '../sitemap.xml') AND ($fc_preferences['prefs_xml_sitemap'] == 'off')) {
		continue;
	}
	
	if(!is_writable($f)) {
		echo '<div class="alert alert-danger">'.$lang['alert_not_writable'].' <strong>'.$f.'</strong></div>';
	}

}

if($prefs_cms_domain == '') {
	echo '<div class="alert alert-danger">'.$lang['alert_prefs_cms_domain'].'</div>';
}

if($prefs_cms_base == '') {
	echo '<div class="alert alert-danger">'.$lang['alert_prefs_cms_base'].'</div>';
}


?>