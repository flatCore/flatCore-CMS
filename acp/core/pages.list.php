<?php

//prohibit unauthorized access
require 'core/access.php';

//$dbh = new PDO("sqlite:".CONTENT_DB);

unset($result);
/* $_SESSION[filter_string] was defined in inc.pages.php */
$sql = "SELECT page_id, page_thumbnail, page_language, page_linkname, page_title, page_meta_description, page_sort, page_lastedit, page_lastedit_from, page_status, page_template, page_modul, page_authorized_users, page_permalink, page_redirect, page_redirect_code, page_labels, page_psw
		FROM fc_pages
		$_SESSION[filter_string]
		ORDER BY page_language ASC, page_sort *1 ASC, LENGTH(page_sort), page_sort ASC, page_linkname ASC";

$result = $db_content->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$x=0;
foreach($result as $p) {
	$this_page_id = 'p'.$p['page_id'];
	$count_comments = $db_content->query("Select Count(*) FROM fc_comments WHERE comment_parent_id LIKE '$this_page_id' ")->fetch();
	$result[$x]['cnt_comments'] = $count_comments[0];
	$x++;
}

$cnt_result = count($result);


echo '<div class="app-container">';
echo '<div class="max-height-container">';

echo '<div class="row">';
echo '<div class="col-md-9">';

echo '<div class="row">';
echo '<div class="col-sm-6">';

/**
 * list all pages where page_sort != empty
 */

$btn_list_toggler = '<a id="toggleExpand" class="px-2">'.$icon['angle_down'].'</a>';


echo '<div class="card">';
echo '<div class="card-header">' . $lang['legend_structured_pages'] . ' '.$btn_list_toggler.'</div>';
echo '<div class="card-body">';
echo '<div class="scroll-box">';
echo '<div class="pages-list-container">';

$item_template = file_get_contents('templates/list-pages-item.tpl');

for($i=0;$i<$cnt_result;$i++) {

	if($result[$i]['page_sort'] == "") {
		continue;
	}
	
	unset($show_redirect,$page_modul);

	$page_id = $result[$i]['page_id'];
	$page_sort = $result[$i]['page_sort'];
	$page_linkname = $result[$i]['page_linkname'];
	$page_title = $result[$i]['page_title'];
	$page_description = $result[$i]['page_meta_description'];
	$page_status = $result[$i]['page_status'];
	$page_lastedit = $result[$i]['page_lastedit'];
	$page_lastedit_from = $result[$i]['page_lastedit_from'];
	$page_template = $result[$i]['page_template'];
	$page_authorized_users = $result[$i]['page_authorized_users'];
	$page_language = $result[$i]['page_language'];
	$page_permalink = $result[$i]['page_permalink'];
	$page_redirect = $result[$i]['page_redirect'];
	$page_redirect_code = $result[$i]['page_redirect_code'];
	$page_modul = $result[$i]['page_modul'];
	$page_cnt_comments = $result[$i]['cnt_comments'];
	$page_labels = explode(',',$result[$i]['page_labels']);
	$page_thumbs = explode('<->',$result[$i]['page_thumbnail']);
	
	$page_thumb_src = 'images/fc-logo.svg';
	if($page_thumbs[0] != '') {
		$page_thumb_src = $page_thumbs[0];
	}
	
	$page_lang_thumb = '<img src="/lib/lang/'.$page_language.'/flag.png" width="15" title="'.$page_language.'" alt="'.$page_language.'">';
	
	if($page_template == "use_standard") {
		$show_template_name =  $lang['use_standard'];
	} else {
		$show_template_name = $page_template;
	}
	
	if($result[$i]['page_psw'] != '') {
		$page_title = $icon['lock'].' '.$page_title;
	}
	
	if(strlen($page_description) > 100) {
		$page_description = substr($page_description, 0, 100) .' <small>(&hellip;)</small>';
	}
	
	if($page_description == '') {
		$page_description = '<span class="text-danger">'.$icon['exclamation_triangle'].' '.$lang['alert_no_page_description'].'</span>';
	}
	
	if($page_title == '') {
		$page_title = '<span class="text-danger">'.$icon['exclamation_triangle'].' '.$lang['alert_no_page_title'].'</span>';
	}
	
	if($page_sort == 'portal') {
		$page_linkname = $icon['home'].' ' . $page_linkname;
	}
	
	$points_of_page = substr_count($page_sort, '.');
	$indent = ($points_of_page)*10 . 'px';
	$pi = get_page_impression($page_id);
	
	if($page_status == "public") {
		$btn = 'ghost-btn-public';
		$item_class = 'page-list-item-public';
		$status_label = $lang['f_page_status_puplic'];
	} elseif($page_status == "ghost") {
		$btn = 'ghost-btn-ghost';
		$item_class = 'page-list-item-ghost';
		$status_label = $lang['f_page_status_ghost'];
	} elseif($page_status == "private") {
		$btn = 'ghost-btn-private';
		$item_class = 'page-list-item-private';
		$status_label = $lang['f_page_status_private'];
	} elseif($page_status == "draft") {
		$btn = 'ghost-btn-draft';
		$item_class = 'page-list-item-draft';
		$status_label = $lang['f_page_status_draft'];
	}
	
	if($page_redirect != '') {
		$page_redirect = $icon['long_arrow_alt_right'].' '.$page_redirect;
		$item_class .= ' page-list-item-redirect';
	}
	
	$last_edit = fc_format_datetime($page_lastedit) . " ($page_lastedit_from)";
	
	/* check for display edit button */
	
	if($_SESSION['acp_editpages'] == "allowed"){
		$edit_button = '<button class="btn btn-sm btn-fc w-100" name="editpage" value="'.$page_id.'" title="'.$lang['edit'].'">'.$icon['edit'].'</button>';
		$duplicate_button = '<button class="btn btn-sm btn-fc w-100" name="duplicate" value="'.$page_id.'" title="'.$lang['duplicate'].'">'.$icon['copy'].'</button>';
	} else {
		$edit_button = '';
		$duplicate_button = '';
	}
	
	$info_button = '<a href="#" class="btn btn-sm btn-fc w-100 page-info-btn" data-bs-target="pageInfoModal" data-id="'.$page_id.'" data-token="'.$_SESSION['token'].'" title="'.$lang['info'].'">'.$icon['info_circle'].'</a>';
	
	$arr_checked_admins = explode(",",$page_authorized_users);
	if(in_array("$_SESSION[user_nick]", $arr_checked_admins)) {
		$edit_button = '<button class="btn btn-sm btn-fc w-100" name="editpage" value="'.$page_id.'" title="'.$lang['edit'].'">'.$icon['edit'].'</button>';
	}
	
	$label = '';
	if($result[$i]['page_labels'] != '') {
		foreach($page_labels as $page_label) {
			
			foreach($fc_labels as $l) {
				if($page_label == $l['label_id']) {
					$label_color = $l['label_color'];
					$label_title = $l['label_title'];
				}
			}
			
			$label .= '<span class="label-dot" style="background-color:'.$label_color.';" title="'.$label_title.'"></span>';
		}
	}
	
	if($fc_mod_rewrite == "permalink") {
		$frontend_link = "../$page_permalink";
	} else {
		$frontend_link = "../index.php?p=$page_id";
	}
	
	$show_mod = '';
	if($page_modul != '') {
		$page_modul_title = substr($page_modul, 0,-4);
		$show_mod = ' <small>'.$icon['cog'].' '.$page_modul_title.'</small><br>';
	}
	
	if($page_redirect != '') {
		if($_SESSION['checked_redirect'] != "checked") {
			continue;
		}
	}
	
	$page_comments_link = '';
	
	
	$str = array(
		'{status-label}','{item-linkname}','{item-title}','{item-tmb-src}',
		'{item-mod}','{item-class}','{item-indent}','{edit-btn}','{duplicate-btn}','{info-btn}',
		'{comment-btn}','{item-permalink}','{item-lastedit}','{item-pagesort}','{item-template}',
		'{item-redirect}','{frontend-link}','{item-description}','{item-lang}', '{page_labels}','{item-pi}','{hidden_csrf_tokken}'
	);
	$rplc = array(
		$status_label,$page_linkname,$page_title,$page_thumb_src,
		$show_mod,$item_class,$indent,$edit_button,$duplicate_button,$info_button,
		$page_comments_link,$page_permalink,$last_edit,$page_sort, $show_template_name,
		$page_redirect,$frontend_link,$page_description,$page_lang_thumb,$label,$pi,$hidden_csrf_token
		);


	$this_template = str_replace($str, $rplc, $item_template);
	echo $this_template;
	

}


echo '</div>';
echo '</div>';
echo '</div>'; // card-body
echo '</div>'; // card

echo '</div>';
echo '<div class="col-sm-6">';



/**
 * list all pages where
 * page_sort == empty
 * or page_sort == portal
 */

echo '<div class="card">';
echo '<div class="card-header">'.$lang['legend_unstructured_pages'].'</div>';
echo '<div class="card-body">';

echo '<div class="scroll-box">';
echo '<div class="pages-list-container">';


for($i=0;$i<$cnt_result;$i++) {

	if($result[$i]['page_sort'] != "" OR $result[$i]['page_sort'] == 'portal') {
		continue;
	}
	
	unset($show_redirect,$page_modul);
	$indent = 0;

	$page_id = $result[$i]['page_id'];
	$page_sort = $result[$i]['page_sort'];
	$page_linkname = $result[$i]['page_linkname'];
	$page_title = $result[$i]['page_title'];
	$page_description = $result[$i]['page_meta_description'];
	$page_status = $result[$i]['page_status'];
	$page_lastedit = $result[$i]['page_lastedit'];
	$page_lastedit_from = $result[$i]['page_lastedit_from'];
	$page_template = $result[$i]['page_template'];
	$page_authorized_users = $result[$i]['page_authorized_users'];
	$page_language = $result[$i]['page_language'];
	$page_permalink = $result[$i]['page_permalink'];
	$page_redirect = $result[$i]['page_redirect'];
	$page_modul = $result[$i]['page_modul'];
	$page_cnt_comments = $result[$i]['cnt_comments'];
	$page_labels = explode(',',$result[$i]['page_labels']);
	$page_thumbs = explode('<->',$result[$i]['page_thumbnail']);

	$page_thumb_src = 'images/fc-logo.svg';
	if($page_thumbs[0] != '') {
		$page_thumb_src = $page_thumbs[0];
	}
	
	$page_lang_thumb = '<img src="/lib/lang/'.$page_language.'/flag.png" width="15" title="'.$page_language.'" alt="'.$page_language.'">';
		
	if($page_template == "use_standard") {
		$show_template_name =  "$lang[use_standard]";
	} else {
		$show_template_name = "$page_template";
	}
	
	if($result[$i]['page_psw'] != '') {
		$page_title = $icon['lock'].' '.$page_title;
	}
	
	if(strlen($page_description) > 100) {
		$page_description = substr($page_description, 0, 100) .' <small>(&hellip;)</small>';
	}
	
	if($page_description == '') {
		$page_description = '<span class="text-danger">'.$icon['exclamation_triangle'].' '.$lang['alert_no_page_description'].'</span>';
	}
	
	if($page_title == '') {
		$page_title = '<span class="text-danger">'.$icon['exclamation_triangle'].' '.$lang['alert_no_page_title'].'</span>';
	}
	
	$hits_id = $page_id;	
	if($page_sort == "portal") {
		$hits_id = "portal_$page_language";
	}
	
	$pi = get_page_impression($hits_id);
	
	if($page_status == "public") {
		$btn = 'ghost-btn-public';
		$item_class = 'page-list-item-public';
		$status_label = $lang['f_page_status_puplic'];
	} elseif($page_status == "ghost") {
		$btn = 'ghost-btn-ghost';
		$item_class = 'page-list-item-ghost';
		$status_label = $lang['f_page_status_ghost'];
	} elseif($page_status == "private") {
		$btn = 'ghost-btn-private';
		$item_class = 'page-list-item-private';
		$status_label = $lang['f_page_status_private'];
	} elseif($page_status == "draft") {
		$btn = 'ghost-btn-draft';
		$item_class = 'page-list-item-draft';
		$status_label = $lang['f_page_status_draft'];
	}
	
	if($page_redirect != '') {
		$page_redirect = $icon['long_arrow_alt_right'].' '.$page_redirect;
		$item_class .= ' page-list-item-redirect';
	}
	
	$last_edit = fc_format_datetime($page_lastedit) . " ($page_lastedit_from)";
	
	/* check for display edit button */
	
	if($_SESSION['acp_editpages'] == "allowed"){
		$edit_button = '<button class="btn btn-sm btn-fc w-100" name="editpage" value="'.$page_id.'" title="'.$lang['edit'].'">'.$icon['edit'].'</button>';
		$duplicate_button = '<button class="btn btn-sm btn-fc w-100" name="duplicate" value="'.$page_id.'" title="'.$lang['duplicate'].'">'.$icon['copy'].'</button>';
	} else {
		$edit_button = '';
		$duplicate_button = '';
	}
	
	$info_button = '<a href="#" class="btn btn-sm btn-fc w-100 page-info-btn" data-bs-target="pageInfoModal" data-id="'.$page_id.'" data-token="'.$_SESSION['token'].'" title="'.$lang['info'].'">'.$icon['info_circle'].'</a>';
	
	$arr_checked_admins = explode(",",$page_authorized_users);
	if(in_array("$_SESSION[user_nick]", $arr_checked_admins)) {
		$edit_button = '<button class="btn btn-sm btn-fc w-100" name="editpage" value="'.$page_id.'" title="'.$lang['edit'].'">'.$icon['edit'].'</button>';
	}
	
	$label = '';
	if($result[$i]['page_labels'] != '') {
		foreach($page_labels as $page_label) {
			
			foreach($fc_labels as $l) {
				if($page_label == $l['label_id']) {
					$label_color = $l['label_color'];
					$label_title = $l['label_title'];
				}
			}
			
			$label .= '<span class="label-dot" style="background-color:'.$label_color.';" title="'.$label_title.'"></span>';
		}
	}
	
	if($fc_mod_rewrite == "permalink") {
		$frontend_link = "../$page_permalink";
	} else {
		$frontend_link = "../index.php?p=$page_id";
	}
	
	$show_mod = '';
	if($page_modul != '') {
		$page_modul_title = substr($page_modul, 0,-4);
		$show_mod = ' <small>'.$icon['cog'].' '.$page_modul_title.'</small><br>';
	}
	
	if($page_redirect != '') {
		if($_SESSION['checked_redirect'] != "checked") {
			continue;
		}
	}

	$page_comments_link = '';
	
	$str = array(
		'{status-label}','{item-linkname}','{item-title}','{item-tmb-src}',
		'{item-mod}','{item-class}','{item-indent}','{edit-btn}','{duplicate-btn}','{info-btn}',
		'{comment-btn}','{item-permalink}','{item-lastedit}','{item-pagesort}','{item-template}',
		'{item-redirect}','{frontend-link}','{item-description}','{item-lang}', '{page_labels}','{item-pi}','{hidden_csrf_tokken}'
	);
	$rplc = array(
		$status_label,$page_linkname,$page_title,$page_thumb_src,
		$show_mod,$item_class,$indent,$edit_button,$duplicate_button,$info_button,
		$page_comments_link,$page_permalink,$last_edit,$page_sort, $show_template_name,
		$page_redirect,$frontend_link,$page_description,$page_lang_thumb,$label,$pi,$hidden_csrf_token
		);


	$this_template = str_replace($str, $rplc, $item_template);
	echo $this_template;
		

} // eol for $i

echo '</div>';
echo '</div>';

echo '</div>'; // card-body
echo '</div>'; // card


echo '</div>';
echo '</div>';

echo '</div>';
echo '<div class="col-md-3">';

echo '<a href="?tn=pages&sub=new#position" class="btn btn-success w-100">'.$icon['plus'].' '.$lang['new_page'].'</a><hr>';

/* sidebar */

echo '<div class="card">';
echo '<div class="card-header">FILTER</div>';
echo '<div class="card-body">';

echo $kw_form;

if($btn_remove_keyword != '') {
	echo '<div class="d-inline">';
	echo '<p style="padding-top:5px;">' . $btn_remove_keyword . '</p>';
	echo '</div><hr>';
}

echo $nav_btn_group;


echo '</div>'; // card-body
echo '</div>'; // card

/* end of sidebar */

echo '</div>';
echo '</div>';

echo '</div>'; // .max-height-container

echo '</div>'; // .app-container


/* modal for page-info */


echo '<div class="modal fade" id="pageInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
 
     <!-- Modal content-->
     <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">'.$icon['info_circle'].'</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
 
      </div>
     </div>
    </div>
   </div>';

?>
