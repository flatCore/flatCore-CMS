<?php
//error_reporting(E_ALL ^E_NOTICE);
//prohibit unauthorized access
require 'core/access.php';




/* delete post */

if((isset($_POST['delete_id'])) && is_numeric($_POST['delete_id'])) {
	
	$del_id = (int) $_POST['delete_id'];
	
	/* first get the post it's data and check the type */
	$this_post_data = fc_get_post_data($del_id);

	if($this_post_data['post_type'] == 'g') {	
		/* it's a gallery, we have to delete the images too */
		$year = date('Y',$this_post_data['post_date']);
		fc_remove_gallery($del_id,$year);
	}
	
	$delete = $db_posts->delete("fc_posts", [
		"post_id" => $del_id
	]);	
	
	if($delete->rowCount() > 0) {
		echo '<div class="alert alert-success">'.$lang['msg_post_deleted'].'</div>';
	}
}




/* remove fixed */

if(is_numeric($_REQUEST['rfixed'])) {

	$change_id = (int) $_REQUEST['rfixed'];	
	$db_posts->update("fc_posts", [
		"post_fixed" => "2"
	],[
		"post_id" => $change_id
	]);	
}

/* set fixed */

if(is_numeric($_REQUEST['sfixed'])) {

	$change_id = (int) $_REQUEST['sfixed'];
	$db_posts->update("fc_posts", [
		"post_fixed" => "1"
	],[
		"post_id" => $change_id
	]);	
	
}



/* change priority */

if(isset($_POST['post_priority'])) {
	
	$change_id = (int) $_POST['prio_id'];
	$db_posts->update("fc_posts", [
		"post_priority" => $_POST['post_priority']
	],[
		"post_id" => $change_id
	]);	
	
}






// defaults
$posts_start = 0;
$posts_limit = 25;
$posts_order = 'id';
$posts_direction = 'DESC';
$posts_filter = array();

$arr_status = array('2','1');
$arr_types = array('m','i','v','l','e','g','p');
$arr_lang = get_all_languages();
$arr_categories = fc_get_categories();



/* default: check all languages */
if(!isset($_SESSION['checked_lang_string'])) {	
	foreach($arr_lang as $langstring) {
		$checked_lang_string .= "$langstring[lang_folder]-";
	}
	$_SESSION['checked_lang_string'] = "$checked_lang_string";
}

/* change status of $_GET['switchLang'] */
if($_GET['switchLang']) {
	if(strpos("$_SESSION[checked_lang_string]", "$_GET[switchLang]") !== false) {
		$checked_lang_string = str_replace("$_GET[switchLang]-", '', $_SESSION['checked_lang_string']);
	} else {
		$checked_lang_string = $_SESSION['checked_lang_string'] . "$_GET[switchLang]-";
	}
	$_SESSION['checked_lang_string'] = "$checked_lang_string";
}

/* filter buttons for languages */
$lang_btn_group = '<div class="btn-group">';
for($i=0;$i<count($arr_lang);$i++) {
	$lang_desc = $arr_lang[$i]['lang_desc'];
	$lang_folder = $arr_lang[$i]['lang_folder'];
	
	$this_btn_status = '';
	if(strpos("$_SESSION[checked_lang_string]", "$lang_folder") !== false) {
		$this_btn_status = 'active';
	}
	
	$lang_btn_group .= '<a href="acp.php?tn=posts&switchLang='.$lang_folder.'" class="btn btn-sm btn-fc '.$this_btn_status.'">'.$lang_folder.'</a>';
}
$lang_btn_group .= '</div>';






/* default: check all types */
if(!isset($_SESSION['checked_type_string'])) {		
	$_SESSION['checked_type_string'] = 'm-i-v-l-e-g-p';
}
/* change status of selected types */
if($_GET['type']) {
	if(strpos("$_SESSION[checked_type_string]", "$_GET[type]") !== false) {
		$checked_type_string = str_replace("$_GET[type]", '', $_SESSION['checked_type_string']);
	} else {
		$checked_type_string = $_SESSION['checked_type_string'] . '-' . $_GET['type'];
	}
	$checked_type_string = str_replace('--', '-', $checked_type_string);
	$_SESSION['checked_type_string'] = "$checked_type_string";
}
/* default: check all status types */
if(!isset($_SESSION['checked_status_string'])) {		
	$_SESSION['checked_status_string'] = '1-2';
}
/* change status types */
if($_GET['status']) {
	if(strpos("$_SESSION[checked_status_string]", "$_GET[status]") !== false) {
		$checked_status_string = str_replace("$_GET[status]", '', $_SESSION['checked_status_string']);
	} else {
		$checked_status_string = $_SESSION['checked_status_string'] . '-' . $_GET['status'];
	}
	$checked_status_string = str_replace('--', '-', $checked_status_string);
	$_SESSION['checked_status_string'] = "$checked_status_string";
}





/* default: check all categories */
if(!isset($_SESSION['checked_cat_string'])) {	
	$_SESSION['checked_cat_string'] = 'all';
}
/* filter by categories */
if($_GET['cat']) {
	$_SESSION['checked_cat_string'] = $_GET['cat'];
}

$cat_all_active = '';
$icon_all_toggle = $icon['circle_alt'];
if($_SESSION['checked_cat_string'] == 'all') {
	$cat_all_active = 'active';
	$icon_all_toggle = $icon['check_circle'];
}

$cat_btn_group = '<div class="card">';
$cat_btn_group .= '<div class="list-group list-group-flush">';
$cat_btn_group .= '<a href="acp.php?tn=posts&cat=all" class="list-group-item list-group-item-ghost p-1 px-2 '.$cat_all_active.'">'.$icon_all_toggle.' '.$lang['btn_all_categories'].'</a>';
foreach($arr_categories as $c) {
	$cat_active = '';
	$icon_toggle = $icon['circle_alt'];
	if(strpos($_SESSION['checked_cat_string'], $c['cat_id']) !== false) {
		$icon_toggle = $icon['check_circle'];
		$cat_active = 'active';
	}
	
	$cat_btn_group .= '<a href="acp.php?tn=posts&cat='.$c['cat_id'].'" class="list-group-item list-group-item-ghost p-1 px-2 '.$cat_active.'">'.$icon_toggle.' '.$c['cat_name'].'</a>';
}

$cat_btn_group .= '</div>';
$cat_btn_group .= '</div>';


if((isset($_GET['posts_start'])) && is_numeric($_GET['posts_start'])) {
	$posts_start = (int) $_GET['posts_start'];
}

if((isset($_POST['setPage'])) && is_numeric($_POST['setPage'])) {
	$posts_start = (int) $_POST['setPage'];
}


$posts_filter['languages'] = $_SESSION['checked_lang_string'];
$posts_filter['types'] = $_SESSION['checked_type_string'];
$posts_filter['status'] = $_SESSION['checked_status_string'];
$posts_filter['categories'] = $_SESSION['checked_cat_string'];


$get_posts = fc_get_post_entries($posts_start,$posts_limit,$posts_filter);
$cnt_filter_posts = $get_posts[0]['cnt_posts'];
$cnt_get_posts = count($get_posts);
$cnt_posts = fc_cnt_post_entries();

$nextPage = $posts_start+$posts_limit;
$prevPage = $posts_start-$posts_limit;
$cnt_pages = ceil($cnt_filter_posts / $posts_limit);

echo '<div class="row">';
echo '<div class="col-md-9">';

echo '<h4>' . sprintf($lang['label_show_entries'], $cnt_filter_posts, $cnt_posts['All']) .'</h4>';

if($cnt_filter_posts > 0) {

	echo '<table class="table table-sm table-hover">';
	
	echo '<thead><tr>';
	echo '<th>#</th>';
	echo '<th>'.$icon['star'].'</th>';
	echo '<th>'.$lang['label_priority'].'</th>';
	echo '<th nowrap>'.$lang['label_date'].'</th>';
	echo '<th>'.$lang['label_post_type'].'</th>';
	echo '<th></th>';
	echo '<th>'.$lang['label_post_title'].'</th>';
	echo '<th></th>';
	echo '</tr></thead>';
	
	for($i=0;$i<$cnt_get_posts;$i++) {
		
		$type_class = 'label-type label-'.$get_posts[$i]['post_type'];
		$icon_fixed = '';
		$draft_class = '';
		
		if($get_posts[$i]['post_fixed'] == '1') {
			$icon_fixed = '<a href="acp.php?tn=posts&a=start&rfixed='.$get_posts[$i]['post_id'].'">'.$icon['star'].'</a>';
		} else {
			$icon_fixed = '<a href="acp.php?tn=posts&a=start&sfixed='.$get_posts[$i]['post_id'].'">'.$icon['star_outline'].'</a>';
		}
		
		if($get_posts[$i]['status'] == 'draft') {
			$draft_class = 'item_is_draft';
		}
		
		/* trim teaser to $trim chars */
		$trim = 150;
		$teaser = strip_tags(htmlspecialchars_decode($get_posts[$i]['post_teaser']));
		if(strlen($teaser) > $trim) {
			$ellipses = ' <small><i>(...)</i></small>';
		  $last_space = strrpos(substr($teaser, 0, $trim), ' ');
		  if($last_space !== false) {
			  $trimmed_teaser = substr($teaser, 0, $last_space);
			} else {
				$trimmed_teaser = substr($teaser, 0, $trim);
			}
			$trimmed_teaser = $trimmed_teaser.$ellipses;
		} else {
			$trimmed_teaser = $teaser;
		}
		
		
		$post_image = explode("<->", $get_posts[$i]['post_images']);
		$show_thumb = '';
		if($post_image[1] != "") {
			$image_src = $post_image[1];
			/* older version of flatNews stored only basename of images */
			if(stripos($post_image[1],'/content/') === FALSE) {
				$image_src = "/$img_path/" . $post_image[1];
			}
		
			$show_thumb  = '<a data-toggle="popover" data-trigger="hover" data-html="true" data-content="<img src=\''.$image_src.'\'>">';
			$show_thumb .= '<div class="show-thumb" style="background-image: url('.$image_src.');">';
			$show_thumb .= '</div>';
		}
	
		
		$select_priority = '<select name="post_priority" class="form-control custom-select" onchange="this.form.submit()">';
		for($x=1;$x<11;$x++) {
			$option_add = '';
			$sel_prio = '';
			if($get_posts[$i]['post_priority'] == $x) {
				$sel_prio = 'selected';
			}
			$select_priority .= '<option value="'.$x.'" '.$sel_prio.'>'.$x.'</option>';
		}
		$select_priority .= '</select>';
		
		
		$prio_form  = '<form action="acp.php?tn=posts&a=start" method="POST">';
		$prio_form .= $select_priority;
		$prio_form .= '<input type="hidden" name="prio_id" value="'.$get_posts[$i]['post_id'].'">';
		$prio_form .= '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
		$prio_form .= '</form>';
		
		$published_date = '<span title="'.date('Y-m-d H:i:s',$get_posts[$i]['post_date']).'">E: '.date('Y-m-d',$get_posts[$i]['post_date']).'</span>';
		$release_date = '<span title="'.date('Y-m-d H:i:s',$get_posts[$i]['post_releasedate']).'">R: '.date('Y-m-d',$get_posts[$i]['post_releasedate']).'</span>';
		$lastedit_date = '';
		if($get_posts[$i]['post_lastedit'] != '') {
			$lastedit_date = '<span title="'.date('Y-m-d H:i:s',$get_posts[$i]['post_lastedit']).' ('.$get_posts[$i]['post_lastedit_from'].')">L: '.date('Y-m-d',$get_posts[$i]['post_lastedit']).'</span>';
		}
		
		$show_events_date = '';
		if($get_posts[$i]['post_type'] == 'e') {
			$show_events_date = '<div class="float-right small well well-sm">';
			$show_events_date .= date('Y-m-d',$get_posts[$i]['post_event_startdate']);
			$show_events_date .= '<br>';
			$show_events_date .= date('Y-m-d',$get_posts[$i]['post_event_enddate']);
			$show_events_date .= '</div>';
		}
		
		$show_items_price = '';
		if($get_posts[$i]['post_type'] == 'p') {
			
			$post_price_gross = $get_posts[$i]['post_product_price_net']*($get_posts[$i]['post_product_tax']+100)/100;
			$post_price_gross = fc_post_print_currency($post_price_gross);

			$show_items_price = '<div class="float-right small well well-sm">';
			$show_items_price .= $post_price_gross;
			$show_items_price .= '</div>';		
		}
		
		
		if($get_posts[$i]['post_type'] == 'm') {
			$show_type = '<span class="'.$type_class.'">'.$lang['post_type_message'].'</span>';
		} else if($get_posts[$i]['post_type'] == 'e') {
			$show_type = '<span class="'.$type_class.'">'.$lang['post_type_event'].'</span>';
		} else if($get_posts[$i]['post_type'] == 'i') {
			$show_type = '<span class="'.$type_class.'">'.$lang['post_type_image'].'</span>';
		} else if($get_posts[$i]['post_type'] == 'g') {
			$show_type = '<span class="'.$type_class.'">'.$lang['post_type_gallery'].'</span>';
		} else if($get_posts[$i]['post_type'] == 'v') {
			$show_type = '<span class="'.$type_class.'">'.$lang['post_type_video'].'</span>';
		} else if($get_posts[$i]['post_type'] == 'l') {
			$show_type = '<span class="'.$type_class.'">'.$lang['post_type_link'].'</span>';
		} else if($get_posts[$i]['post_type'] == 'p') {
			$show_type = '<span class="'.$type_class.'">'.$lang['post_type_product'].'</span>';
		} else if($get_posts[$i]['post_type'] == 'f') {
			$show_type = '<span class="'.$type_class.'">'.$lang['post_type_file'].'</span>';
		}
		
		
		
		echo '<tr class="'.$draft_class.'">';
		echo '<td>'.$get_posts[$i]['post_id'].'</td>';
		echo '<td>'.$icon_fixed.'</td>';
		echo '<td>'.$prio_form.'</td>';
		echo '<td nowrap><small>'.$published_date.'<br>'.$release_date.'<br>'.$lastedit_date.'</small></td>';
		echo '<td>'.$show_type.'</td>';
		echo '<td>'.$show_thumb.'</td>';
		echo '<td>'.$show_events_date.$show_items_price.'<h5 class="mb-0">'.$get_posts[$i]['post_title'].'</h5><small>'.$trimmed_teaser.'</small></td>';
		echo '<td style="min-width: 150px;">';
		echo '<nav class="nav justify-content-end">';
		echo '<a class="btn btn-fc btn-sm text-success mx-1" href="acp.php?tn=posts&sub=edit&post_id='.$get_posts[$i]['post_id'].'">'.$lang['edit'].'</a>';
		echo '<form class="form-inline" action="acp.php?tn=posts" method="POST"><button class="btn btn-danger btn-sm" type="submit" name="delete_id" value="'.$get_posts[$i]['post_id'].'">'.$icon['trash_alt'].'</button></form>';
		echo '</nav>';
		echo '</td>';
		echo '</tr>';

	}
	
	echo '</table>';

} else {
	echo '<div class="alert alert-info">'.$lang['msg_no_posts_to_show'].'</div>';
}
echo '</div>';
echo '<div class="col-md-3">';





echo '<button class="btn btn-block btn-success dropdown-toggle" type="button" data-toggle="collapse" data-target="#collapseNew">'.$lang['label_new_post'].'</button>';

echo '<div class="collapse" id="collapseNew">';

echo '<div class="list-group list-group-flush">';
echo '<div class="list-group-item list-group-item-ghost"><a href="?tn=posts&sub=edit&new=m"><span class="color-message">'.$icon['plus'].'</span> '.$lang['post_type_message'].'</a></div>';
echo '<div class="list-group-item list-group-item-ghost"><a href="?tn=posts&sub=edit&new=e"><span class="color-event">'.$icon['plus'].'</span> '.$lang['post_type_event'].'</a></div>';
echo '<div class="list-group-item list-group-item-ghost"><a href="?tn=posts&sub=edit&new=i"><span class="color-image">'.$icon['plus'].'</span> '.$lang['post_type_image'].'</a></div>';
echo '<div class="list-group-item list-group-item-ghost"><a href="?tn=posts&sub=edit&new=g"><span class="color-gallery">'.$icon['plus'].'</span> '.$lang['post_type_gallery'].'</a></div>';
echo '<div class="list-group-item list-group-item-ghost"><a href="?tn=posts&sub=edit&new=v"><span class="color-video">'.$icon['plus'].'</span> '.$lang['post_type_video'].'</a></div>';
echo '<div class="list-group-item list-group-item-ghost"><a href="?tn=posts&sub=edit&new=l"><span class="color-link">'.$icon['plus'].'</span> '.$lang['post_type_link'].'</a></div>';
echo '<div class="list-group-item list-group-item-ghost"><a href="?tn=posts&sub=edit&new=p"><span class="color-product">'.$icon['plus'].'</span> '.$lang['post_type_product'].'</a></div>';
echo '<div class="list-group-item list-group-item-ghost"><a href="?tn=posts&sub=edit&new=f"><span class="color-file">'.$icon['plus'].'</span> '.$lang['post_type_file'].'</a></div>';
echo '</div>';
echo '</div>';

echo '<hr>';

echo '<div class="row">';
echo '<div class="col-md-2">';
if($prevPage < 0) {
	echo '<a class="btn btn-fc btn-block disabled" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>';
} else {
	echo '<a class="btn btn-fc btn-block" href="acp.php?tn=posts&posts_start='.$prevPage.'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>';
}

echo '</div>';
echo '<div class="col-md-8">';
echo '<form action="acp.php?tn=posts" method="POST">';
echo '<select class="form-control custom-select" name="setPage" onchange="this.form.submit()">';
for($i=0;$i<$cnt_pages;$i++) {
	$x = $i+1;
	$thisPage = ($x*$posts_limit)-$posts_limit;
	$sel = '';
	if($thisPage == $posts_start) {
		$sel = 'selected';
	}
	echo '<option value="'.$thisPage.'" '.$sel.'>'.$x.' ('.$thisPage.')</option>';
}
echo '</select>';
echo '</form>';
echo '</div>';
echo '<div class="col-md-2">';
if($nextPage < ($cnt_filter_posts-$posts_limit)+$posts_limit) {
	echo '<a class="btn btn-fc btn-block" href="acp.php?tn=posts&posts_start='.$nextPage.'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>';
} else {
	echo '<a class="btn btn-fc btn-block disabled" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>';
}
echo '</div>';
echo '</div>';

echo '<fieldset class="mt-4">';
echo '<legend>'.$icon['filter'].' Filter</legend>';

/* Filter Options */
echo '<div class="card mt-1">';
echo '<div class="card-header p-1 px-2">'.$lang['label_language'].'</div>';
echo '<div class="list-group list-group-flush">';
echo $lang_btn_group;
echo '</div>';
echo '</div>';

echo '<div class="card mt-2">';
echo '<div class="card-header p-1 px-2">'.$lang['label_post_type'].'</div>';

/* type filter */
echo '<div class="list-group list-group-flush">';
if(strpos("$_SESSION[checked_type_string]", "m") !== false) {
	$class = 'list-group-item list-group-item-ghost p-1 px-2 active';
	$icon_toggle = $icon['check_circle'];
} else {
	$class = 'list-group-item list-group-item-ghost p-1 px-2';
	$icon_toggle = $icon['circle_alt'];
}

echo '<a href="acp.php?tn=posts&type=m" class="'.$class.'">'.$icon_toggle.' '.$lang['post_type_message'].'</a>';

if(strpos("$_SESSION[checked_type_string]", "e") !== false) {
	$class = 'list-group-item list-group-item-ghost p-1 px-2 active';
	$icon_toggle = $icon['check_circle'];
} else {
	$class = 'list-group-item list-group-item-ghost p-1 px-2';
	$icon_toggle = $icon['circle_alt'];
}

echo '<a href="acp.php?tn=posts&type=e" class="'.$class.'">'.$icon_toggle.' '.$lang['post_type_event'].'</a>';

if(strpos("$_SESSION[checked_type_string]", "i") !== false) {
	$class = 'list-group-item list-group-item-ghost p-1 px-2 active';
	$icon_toggle = $icon['check_circle'];
} else {
	$class = 'list-group-item list-group-item-ghost p-1 px-2';
	$icon_toggle = $icon['circle_alt'];
}

echo '<a href="acp.php?tn=posts&type=i" class="'.$class.'">'.$icon_toggle.' '.$lang['post_type_image'].'</a>';

if(strpos("$_SESSION[checked_type_string]", "g") !== false) {
	$class = 'list-group-item list-group-item-ghost p-1 px-2 active';
	$icon_toggle = $icon['check_circle'];
} else {
	$class = 'list-group-item list-group-item-ghost p-1 px-2';
	$icon_toggle = $icon['circle_alt'];
}

echo '<a href="acp.php?tn=posts&type=g" class="'.$class.'">'.$icon_toggle.' '.$lang['post_type_gallery'].'</a>';

if(strpos("$_SESSION[checked_type_string]", "v") !== false) {
	$class = 'list-group-item list-group-item-ghost p-1 px-2 active';
	$icon_toggle = $icon['check_circle'];
} else {
	$class = 'list-group-item list-group-item-ghost p-1 px-2';
	$icon_toggle = $icon['circle_alt'];
}

echo '<a href="acp.php?tn=posts&type=v" class="'.$class.'">'.$icon_toggle.' '.$lang['post_type_video'].'</a>';

if(strpos("$_SESSION[checked_type_string]", "l") !== false) {
	$class = 'list-group-item list-group-item-ghost p-1 px-2 active';
	$icon_toggle = $icon['check_circle'];
} else {
	$class = 'list-group-item list-group-item-ghost p-1 px-2';
	$icon_toggle = $icon['circle_alt'];
}

echo '<a href="acp.php?tn=posts&type=l" class="'.$class.'">'.$icon_toggle.' '.$lang['post_type_link'].'</a>';


if(strpos("$_SESSION[checked_type_string]", "p") !== false) {
	$class = 'list-group-item list-group-item-ghost p-1 px-2 active';
	$icon_toggle = $icon['check_circle'];
} else {
	$class = 'list-group-item list-group-item-ghost p-1 px-2';
	$icon_toggle = $icon['circle_alt'];
}

echo '<a href="acp.php?tn=posts&type=p" class="'.$class.'">'.$icon_toggle.' '.$lang['post_type_product'].'</a>';

if(strpos("$_SESSION[checked_type_string]", "f") !== false) {
	$class = 'list-group-item list-group-item-ghost p-1 px-2 active';
	$icon_toggle = $icon['check_circle'];
} else {
	$class = 'list-group-item list-group-item-ghost p-1 px-2';
	$icon_toggle = $icon['circle_alt'];
}

echo '<a href="acp.php?tn=posts&type=f" class="'.$class.'">'.$icon_toggle.' '.$lang['post_type_file'].'</a>';

echo '</div>';
echo '</div>';

echo '<div class="card mt-2">';
echo '<div class="card-header p-1 px-2">'.$lang['label_status'].'</div>';

/* status filter */
echo '<div class="btn-group d-flex">';
if(strpos("$_SESSION[checked_status_string]", "2") !== false) {
	$icon_toggle = $icon['check_circle'];
	echo '<a href="acp.php?tn=posts&status=2" class="btn btn-sm btn-fc active w-100">'.$icon_toggle.' '.$lang['status_draft'].'</a>';
} else {
	$icon_toggle = $icon['circle_alt'];
	echo '<a href="acp.php?tn=posts&status=2" class="btn btn-sm btn-fc w-100">'.$icon_toggle.' '.$lang['status_draft'].'</a>';
}
if(strpos("$_SESSION[checked_status_string]", "1") !== false) {
	$icon_toggle = $icon['check_circle'];
	echo '<a href="acp.php?tn=posts&status=1" class="btn btn-sm btn-fc active w-100">'.$icon_toggle.' '.$lang['status_public'].'</a>';
} else {
	$icon_toggle = $icon['circle_alt'];
	echo '<a href="acp.php?tn=posts&status=1" class="btn btn-sm btn-fc w-100">'.$icon_toggle.' '.$lang['status_public'].'</a>';
}
echo '</div>';


echo '</div>';

echo '<div class="card mt-2">';
echo '<div class="card-header p-1 px-2">'.$lang['label_categories'].'</div>';

echo $cat_btn_group;

echo '</div>';

echo '</fieldset>';

echo '</div>';
echo '</div>';


?>