<?php
	
session_start();
error_reporting(0);

require '../../lib/Medoo.php';
use Medoo\Medoo;

require '../../config.php';


if(is_file('../../config_database.php')) {
	include '../../config_database.php';
	$db_type = 'mysql';
	
	$database = new Medoo([
		'type' => 'mysql',
		'database' => "$database_name",
		'host' => "$database_host",
		'username' => "$database_user",
		'password' => "$database_psw",
		'charset' => 'utf8',
		'port' => $database_port,
		'prefix' => DB_PREFIX
	]);
	
	$db_content = $database;
	$db_user = $database;
	$db_statistics = $database;	
	
	
	
} else {
	$db_type = 'sqlite';
	
	if(isset($fc_content_files) && is_array($fc_content_files)) {
		/* switch database file $fc_db_content */
		include 'core/contentSwitch.php';
	}
	
	
	define("CONTENT_DB", "$fc_db_content");

	$db_content = new Medoo([
		'type' => 'sqlite',
		'database' => CONTENT_DB
	]);
	
	
}

require_once 'access.php';
require_once 'functions.php';
require '../../lib/lang/'.$_SESSION['lang'].'/dict-backend.php';

$set_lang = $_SESSION['lang'];
if(isset($_REQUEST['set_lang'])) {
	$set_lang = $_REQUEST['set_lang'];
}


if(isset($_POST['pageid'])){
   $page_id = (int) $_POST['pageid'];
}

$page_data = $db_content->get("fc_pages", "*", [
		"page_id" => "$page_id"
	]);
	

echo '<table class="table table-sm">';
echo '<tr><td class="text-end">ID</td><td><code>'.fc_return_clean_value($page_data['page_id']).'</code></td></tr>';
echo '<tr><td class="text-end">Hash</td><td><code>'.fc_return_clean_value($page_data['page_hash']).'</code></td></tr>';
echo '<tr><td class="text-end">'.$lang['f_page_classes'].'</td><td><code>'.fc_return_clean_value($page_data['page_classes']).'</code></td></tr>';

echo '<tr><td class="text-end">'.$lang['f_page_title'].'</td><td><code>'.fc_return_clean_value($page_data['page_title']).'</code></td></tr>';
echo '<tr><td class="text-end">'.$lang['f_meta_description'].'</td><td><code>'.fc_return_clean_value($page_data['page_meta_description']).'</code></td></tr>';
echo '<tr><td class="text-end">'.$lang['f_meta_keywords'].'</td><td><code>'.fc_return_clean_value($page_data['page_meta_keywords']).'</code></td></tr>';
if($page_data['page_thumbnail'] != '') {
	echo '<tr><td class="text-end">'.$lang['page_thumbnail'].'</td>';
	echo '<td>';
	$thumbs = explode('<->', $page_data['page_thumbnail']);
	echo'<img src="'.$thumbs[0].'" style="max-width:100px;height:auto;">';
	echo '</td>';
}


echo '<tr><td class="text-end">'.$lang['f_meta_robots'].'</td><td><code>'.fc_return_clean_value($page_data['page_meta_robots']).'</code></td></tr>';
echo '<tr><td class="text-end">'.$lang['f_page_status'].'</td><td><code>'.fc_return_clean_value($page_data['page_status']).'</code></td></tr>';

echo '<tr><td class="text-end">'.$lang['f_page_linkname'].'</td><td><code>'.fc_return_clean_value($page_data['page_linkname']).'</code></td></tr>';
echo '<tr><td class="text-end">'.$lang['f_page_permalink'].'</td><td><code>'.fc_return_clean_value($page_data['page_permalink']).'</code></td></tr>';
echo '<tr><td class="text-end">'.$lang['f_page_permalink_short'].'</td><td><code>'.fc_return_clean_value($page_data['page_permalink_short']).'</code></td></tr>';
echo '<tr><td class="text-end">'.$lang['h_page_hits'].'</td><td><code>'.fc_return_clean_value($page_data['page_permalink_short_cnt']).'</code></td></tr>';

if($page_data['page_redirect'] != '') {
	echo '<tr><td class="text-end">'.$lang['f_page_redirect'].'</td><td><code>'.fc_return_clean_value($page_data['page_redirect']).' ['.$page_data['page_redirect_code'].']</code></td></tr>';
}

if($page_data['page_funnel_uri'] != '') {
	echo '<tr><td class="text-end">'.$lang['f_page_funnel_uri'].'</td>';
	echo'<td>';
	$funnels = explode(',', $page_data['page_funnel_uri']);
	foreach($funnels as $funnel) {
		echo '<code>'.fc_return_clean_value($funnel).'</code><br>';
	}
	echo '</td>';
}


echo '<tr><td class="text-end">'.$lang['tab_content'].'</td><td><code>'.fc_return_clean_value(first_words($page_data['page_content'],50)).'</code></td></tr>';
echo '<tr><td class="text-end">'.$lang['tab_extracontent'].'</td><td><code>'.fc_return_clean_value(first_words($page_data['page_extracontent'],50)).'</code></td></tr>';



echo '</table>';

echo '<hr>';
echo '<div class="btn-group d-flex justify-content-end">';
if($_SESSION['acp_editpages'] == "allowed"){
	echo '<form action="?tn=pages&sub=edit" method="POST">';
	echo '<button class="btn btn-sm btn-fc ms-auto" name="editpage" value="'.$page_data['page_id'].'" title="'.$lang['edit'].'">'.$lang['edit'].'</button>';
	echo '<button type="button" class="btn btn-sm btn-fc" data-bs-dismiss="modal">Close</button>';
	echo $hidden_csrf_token;
	echo '</form>';
} else {
	echo '<button type="button" class="btn btn-sm btn-fc" data-bs-dismiss="modal">Close</button>';
}
echo '</div>';


exit;
?>