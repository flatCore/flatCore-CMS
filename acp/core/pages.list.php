<?php

//prohibit unauthorized access
require 'core/access.php';

$dbh = new PDO("sqlite:".CONTENT_DB);

unset($result);
/* $_SESSION[filter_string] was defined in inc.pages.php */
$sql = "SELECT page_id, page_language, page_linkname, page_title, page_meta_description, page_sort, page_lastedit, page_lastedit_from, page_status, page_template, page_modul, page_authorized_users, page_permalink, page_redirect, page_redirect_code, page_labels, page_psw
		FROM fc_pages
		$_SESSION[filter_string]
		ORDER BY page_language ASC, page_sort ASC, page_linkname ASC";

$sth = $dbh->prepare($sql);
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);

$x=0;
foreach($result as $p) {
	$this_page_id = 'p'.$p['page_id'];
	$count_comments = $dbh->query("Select Count(*) FROM fc_comments WHERE comment_parent LIKE '$this_page_id' ")->fetch();
	$result[$x]['cnt_comments'] = $count_comments[0];
	$x++;
}

	
$dbh = null;
   
$cnt_result = count($result);
$result = fc_array_multisort($result, 'page_language', SORT_ASC, 'page_sort', SORT_ASC, SORT_NATURAL);


echo '<div class="app-container">';

echo '<div class="subHeader">';
echo '<div class="row">';
echo '<div class="col-md-3">';
echo '<fieldset class="mb-0">';
echo '<legend>Filter</legend>';
echo $kw_form;
if($btn_remove_keyword != '') {
	echo '<p style="padding-top:5px;">' . $btn_remove_keyword . '</p>';
}
echo '</fieldset>';
echo '</div>';
echo '<div class="col-md-7">';
echo '<fieldset class="mb-0">';
echo '<legend>'.$lang['f_page_status'].'/'.$lang['f_page_language'].'</legend>';
echo $status_btn_group . ' ' . $lang_btn_group;

echo '</fieldset>';
echo '</div>';

echo '<div class="col-md-2">';
echo '<fieldset class="mb-0">';
echo '<legend>Labels</legend>';
echo '<div class="float-right"><button id="toggleExpand" class="btn btn-link btn-sm">'.$icon['angle_down'].'</button></div>';
echo $label_btn;

echo '</fieldset>';
echo '</div>';
echo '</div>';

echo '</div>';



echo '<div class="max-height-container">';
echo '<div class="row">';
echo '<div class="col-sm-6">';

/**
 * list all pages where page_sort != empty
 */




echo '<fieldset>';
echo '<legend>' . $lang['legend_structured_pages'] . '</legend>';
echo '<div class="scroll-box">';
echo '<div class="pages-list-container">';

$item_template = file_get_contents('templates/list-pages-item.tpl');

for($i=0;$i<$cnt_result;$i++) {

	if($result[$i]['page_sort'] == "" || $result[$i]['page_sort'] == 'portal') {
		continue;
	}
	
	unset($show_redirect,$page_modul);

	$page_id = $result[$i]['page_id'];
	$page_sort = $result[$i]['page_sort'];
	$page_linkname = stripslashes($result[$i]['page_linkname']);
	$page_title = stripslashes($result[$i]['page_title']);
	$page_description = stripslashes($result[$i]['page_meta_description']);
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
	
	$last_edit = date("d.m.Y H:i:s",$page_lastedit) . " ($page_lastedit_from)";
	
	/* check for display edit button */
	
	if($_SESSION['acp_editpages'] == "allowed"){
		$edit_button = '<a class="btn btn-sm btn-fc w-100" href="acp.php?tn=pages&sub=edit&editpage='.$page_id.'" title="'.$lang['edit'].'">'.$icon['edit'].'</a>';
		$duplicate_button = '<a class="btn btn-sm btn-fc w-100" href="acp.php?tn=pages&sub=edit&editpage='.$page_id.'&duplicate=1" title="'.$lang['duplicate'].'">'.$icon['copy'].'</a>';
	} else {
		$edit_button = '';
		$duplicate_button = '';
	}
	
	$arr_checked_admins = explode(",",$page_authorized_users);
	if(in_array("$_SESSION[user_nick]", $arr_checked_admins)) {
		$edit_button = '<a class="btn btn-sm btn-fc w-100" href="acp.php?tn=pages&sub=edit&editpage='.$page_id.'" title="'.$lang['edit'].'">'.$icon['edit'].'</a>';
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
	
	$page_comments_link = '<a data-fancybox data-type="ajax" href="javascript:;" class="btn btn-sm btn-fc w-100" data-src="core/ajax.comments.php?pid='.$page_id.'">'.$icon['comments'].' <small>'. $page_cnt_comments.'</small></a>';
	
	
	$str = array(
		'{status-label}','{item-linkname}','{item-title}',
		'{item-mod}','{item-class}','{item-indent}','{edit-btn}','{duplicate-btn}',
		'{comment-btn}','{item-permalink}','{item-lastedit}','{item-pagesort}','{item-template}',
		'{item-redirect}','{frontend-link}','{item-description}','{item-lang}', '{page_labels}'
	);
	$rplc = array(
		$status_label,$page_linkname,$page_title,
		$show_mod,$item_class,$indent,$edit_button,$duplicate_button,
		$page_comments_link,$page_permalink,$last_edit,$page_sort, $show_template_name,
		$page_redirect,$frontend_link,$page_description,$page_language,$label
		);


	$this_template = str_replace($str, $rplc, $item_template);
	echo $this_template;
	

}


echo '</div>';
echo '</div>';
echo '</fieldset>';

echo '</div>';
echo '<div class="col-sm-6">';



/**
 * list all pages where
 * page_sort == empty
 * or page_sort == portal
 */

echo '<fieldset>';
echo '<legend>' . $lang['legend_unstructured_pages'] . '</legend>';
echo '<div class="scroll-box">';
echo '<div class="pages-list-container">';


for($i=0;$i<$cnt_result;$i++) {

	if($result[$i]['page_sort'] != "" && $result[$i]['page_sort'] != 'portal') {
		continue;
	}
	
	unset($show_redirect,$page_modul);
	$indent = 0;

	$page_id = $result[$i]['page_id'];
	$page_sort = $result[$i]['page_sort'];
	$page_linkname = stripslashes($result[$i]['page_linkname']);
	$page_title = stripslashes($result[$i]['page_title']);
	$page_description = stripslashes($result[$i]['page_meta_description']);
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
		
	if($page_template == "use_standard") {
		$show_template_name =  "$lang[use_standard]";
	} else {
		$show_template_name = "$page_template";
	}
	
	if($result[$i]['page_psw'] != '') {
		$page_title = $icon['lock'].' '.$page_title;
	}
	
	if($page_sort == 'portal') {
		$page_linkname = $icon['home'].' ' . $page_linkname;
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
	
	$last_edit = date("d.m.Y H:i:s",$page_lastedit) . " ($page_lastedit_from)";
	
	/* check for display edit button */
	
	if($_SESSION['acp_editpages'] == "allowed"){
		$edit_button = '<a class="btn btn-sm btn-fc w-100" href="acp.php?tn=pages&sub=edit&editpage='.$page_id.'" title="'.$lang['edit'].'">'.$icon['edit'].'</a>';
		$duplicate_button = '<a class="btn btn-sm btn-fc w-100" href="acp.php?tn=pages&sub=edit&editpage='.$page_id.'&duplicate=1" title="'.$lang['duplicate'].'">'.$icon['copy'].'</a>';
	} else {
		$edit_button = '';
		$duplicate_button = '';
	}
	
	$arr_checked_admins = explode(",",$page_authorized_users);
	if(in_array("$_SESSION[user_nick]", $arr_checked_admins)) {
		$edit_button = '<a class="btn btn-sm btn-fc w-100" href="acp.php?tn=pages&sub=edit&editpage='.$page_id.'" title="'.$lang['edit'].'">'.$icon['edit'].'</a>';
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

	$page_comments_link = '<a data-fancybox data-type="ajax" href="javascript:;" class="btn btn-sm btn-fc w-100" data-src="core/ajax.comments.php?pid='.$page_id.'">'.$icon['comments'].' <small>'. $page_cnt_comments.'</small></a>';
	
	$str = array(
		'{status-label}','{item-linkname}','{item-title}',
		'{item-mod}','{item-class}','{item-indent}','{edit-btn}','{duplicate-btn}',
		'{comment-btn}','{item-permalink}','{item-lastedit}','{item-pagesort}','{item-template}',
		'{item-redirect}','{frontend-link}','{item-description}','{item-lang}', '{page_labels}'
	);
	$rplc = array(
		$status_label,$page_linkname,$page_title,
		$show_mod,$item_class,$indent,$edit_button,$duplicate_button,
		$page_comments_link,$page_permalink,$last_edit,$page_sort, $show_template_name,
		$page_redirect,$frontend_link,$page_description,$page_language,$label
		);


	$this_template = str_replace($str, $rplc, $item_template);
	echo $this_template;
		

} // eol for $i

echo '</div>';
echo '</div>';
echo '</fieldset>';


echo '</div>';
echo '</div>';

echo '</div>'; // .max-height-container

echo '</div>'; // .app-container

?>
