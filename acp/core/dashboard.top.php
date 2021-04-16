<?php

//prohibit unauthorized access
require 'core/access.php';

if(isset($_GET['a'])) {
	
	if($_GET['a'] == 'delete_cache') {
		fc_delete_smarty_cache('all');
	}
	if($_GET['a'] == 'update_index') {
		fc_update_bulk_page_index();
	}
	
}



$tpl_file = file_get_contents('templates/dashboard_top.tpl');

/* get latest infos from user database */

$user_result = $db_user->select("fc_user", ["user_id", "user_nick", "user_class", "user_firstname", "user_lastname", "user_registerdate", "user_verified", "user_mail"], [
	"ORDER" => ["user_id" => "DESC"]
]);


$cnt_user = count($user_result);

$cnt_verified = 0;
$cnt_paused = 0;
$cnt_deleted = 0;
$cnt_waiting = 0;
$cnt_admin = 0;
$cnt_public = 0;
$cnt_draft = 0;
$user_latest5 = '';
$top5pages = '';

for($i=0;$i<$cnt_user;$i++) {

	if($user_result[$i]['user_verified'] == "verified"){
		$cnt_verified++;
	}
	if($user_result[$i]['user_verified'] == "paused"){
		$cnt_paused++;
	}
	if($user_result[$i]['user_verified'] == "waiting"){
		$cnt_waiting++;
	}
	if($user_result[$i]['user_verified'] == ""){
		$cnt_deleted++;
	}
	if($user_result[$i]['user_class'] == "administrator"){
		$cnt_admin++;
	}
	
	if($i < 5) {
		$user_registerdate = @date("d.m.Y",$user_result[$i]['user_registerdate']);
		$user_id = $user_result[$i]['user_id'];
		$user_nick = $user_result[$i]['user_nick'];
		$user_name = $user_result[$i]['user_firstname'] . " " . $user_result[$i]['user_lastname'];
	
		if($user_result[$i]['user_class'] == "deleted"){
			$user_nick = "<strike>$user_nick</strike>";
		}
		$user_latest5 .= '<a href="acp.php?tn=user&sub=edit&edituser='.$user_id.'" class="list-group-item list-group-item-ghost list-group-item-action flex-column align-items-start">';
		$user_latest5 .= '<div class="d-flex w-100 justify-content-between">';
		$user_latest5 .= '<div>';
		$user_latest5 .= '<h6 class="mb-0">'.$user_nick.'</h6>';
		$user_latest5 .= '<small>'.$user_name.'</small>';
		$user_latest5 .= '</div>';
		$user_latest5 .= '<small>'.$user_registerdate.'</small>';
		$user_latest5 .= '</div>';
		$user_latest5 .= '</a>';
	}

}

$user_latest5 = '<div class="list-group list-group-flush">'.$user_latest5.'</div>';

$cnt_verified_per = round($cnt_verified*100/$cnt_user);
$cnt_paused_per = round($cnt_paused*100/$cnt_user);
$cnt_deleted_per = round($cnt_deleted*100/$cnt_user);
$cnt_waiting_per = round($cnt_waiting*100/$cnt_user);


$allPages = $db_content->select("fc_pages", ["page_id", "page_linkname", "page_title", "page_sort", "page_lastedit", "page_lastedit_from", "page_status"], [
	"ORDER" => ["page_lastedit" => "DESC"]
]);

$cnt_pages = count($allPages);
$cnt_public = 0;
$cnt_draft = 0;
$cnt_ghost = 0;
$cnt_private = 0;

for($i=0;$i<$cnt_pages;$i++) {

	$page_id = $allPages[$i]['page_id'];
	
	if($allPages[$i]['page_status'] == "public"){
		$cnt_public++;
	}	
	if($allPages[$i]['page_status'] == "draft"){
		$cnt_draft++;
	}	
	if($allPages[$i]['page_status'] == "ghost"){
		$cnt_ghost++;
	}
	if($allPages[$i]['page_status'] == "private"){
		$cnt_private++;
	}

	if($i < 5) {
		$last_edit = @date("d.m.Y",$allPages[$i]['page_lastedit']);
		$page_linkname = $allPages[$i]['page_linkname'];
		$page_title = first_words($allPages[$i]['page_title'],4);
		
		$top5pages .= '<div class="list-group-item list-group-item-ghost list-group-item-action flex-column align-items-start">';
		$top5pages .= '<div class="d-flex w-100 justify-content-between">';
		$top5pages .= '<div>';
		$top5pages .= '<h6 class="mb-0">'.$page_linkname.' ';
		$top5pages .= '<small>('.$last_edit.')</small></h6>';
		$top5pages .= '<small>'.$page_title.'</small>';
		$top5pages .= '</div>';
		$top5pages .= '<form class="inline" action="?tn=pages&sub=edit" method="POST">';
		$top5pages .= '<button class="btn btn-fc btn-sm" name="editpage" value="'.$allPages[$i]['page_id'].'">'.$icon['edit'].'</button>';
		$top5pages .= '</form>';
		$top5pages .= '</div>';
		
		$top5pages .= '</div>';
		
	}
	
} // eol $i


$top5pages = '<div class="list-group list-group-flush">'.$top5pages.'</div>';


$cnt_public_per = round($cnt_public*100/$cnt_pages);
$cnt_draft_per = round($cnt_draft*100/$cnt_pages);
$cnt_ghost_per = round($cnt_ghost*100/$cnt_pages);
$cnt_private_per = round($cnt_private*100/$cnt_pages);




/* posts */

$allPosts = $db_posts->select("fc_posts", ["post_id", "post_title", "post_teaser", "post_type", "post_lastedit"], [
	"ORDER" => ["post_releasedate" => "DESC"]
]);

$cnt_posts = count($allPosts);

for($i=0;$i<$cnt_posts;$i++) {
	
	
	if($i < 5) {
		
		$last_edit = @date("d.m.Y",$allPosts[$i]['post_lastedit']);
		$post_teaser = first_words(strip_tags(html_entity_decode($allPosts[$i]['post_teaser'])),4);
		
		$top5posts .= '<div class="list-group-item list-group-item-ghost list-group-item-action flex-column align-items-start">';
		$top5posts .= '<div class="d-flex w-100 justify-content-between">';
		$top5posts .= '<div>';
		$top5posts .= '<h6 class="mb-0">'.$allPosts[$i]['post_title'].' ';
		$top5posts .= '<small>('.$last_edit.')</small></h6>';
		$top5posts .= '<small>'.$post_teaser.'</small>';
		$top5posts .= '</div>';
		$top5posts .= '<form class="inline" action="?tn=posts&sub=edit" method="POST">';
		$top5posts .= '<button class="btn btn-fc btn-sm" name="post_id" value="'.$allPosts[$i]['post_id'].'">'.$icon['edit'].'</button>';
		$top5posts .= '</form>';
		$top5posts .= '</div>';
		
		$top5posts .= '</div>';		
	}
	
}

if($cnt_posts < 1) {
	$top5posts = '<p class="p-3">'.$lang['msg_no_entries_so_far'].'</p>';
}



/* comments */

$allComments = $db_content->select("fc_comments", ["comment_id", "comment_author", "comment_text", "comment_lastedit"], [
	"ORDER" => ["comment_lastedit" => "DESC"]
]);

$cnt_comments = count($allComments);

for($i=0;$i<$cnt_comments;$i++) {
	
	if($i < 5) {

		$last_edit = @date("d.m.Y",$allComments[$i]['comment_lastedit']);
		$comment_text = first_words(strip_tags(html_entity_decode($allComments[$i]['comment_text'])),4);
		
		$top5comments .= '<div class="list-group-item list-group-item-ghost list-group-item-action flex-column align-items-start">';
		$top5comments .= '<div class="d-flex w-100 justify-content-between">';
		$top5comments .= '<div>';
		$top5comments .= '<h6 class="mb-0">'.$allComments[$i]['comment_author'].' ';
		$top5comments .= '<small>('.$last_edit.')</small></h6>';
		$top5comments .= '<small>'.$comment_text.'</small>';
		$top5comments .= '</div>';
		$top5comments .= '<form class="inline" action="?tn=comments&sub=list#comid'.$allComments[$i]['comment_id'].'" method="POST">';
		$top5comments .= '<button class="btn btn-fc btn-sm" name="editid" value="'.$allComments[$i]['comment_id'].'">'.$icon['edit'].'</button>';
		$top5comments .= '</form>';
		$top5comments .= '</div>';
		$top5comments .= '</div>';
		
	}
	
}

if($cnt_comments < 1) {
	$top5comments = '<p class="p-3">'.$lang['msg_no_entries_so_far'].'</p>';
}


$user_stats .= '<p class="mb-0 text-muted small">'.$lang['f_user_select_verified'].'</p>';
$user_stats .= '<div class="progress mb-2">';
$user_stats .= '<div class="progress-bar" role="progressbar" style="width: '.$cnt_verified_per.'%;" aria-valuenow="'.$cnt_verified_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_verified.'</div>';
$user_stats .= '</div>';

$user_stats .= '<p class="mb-0 text-muted small">'.$lang['f_user_select_waiting'].'</p>';
$user_stats .= '<div class="progress mb-2">';
$user_stats .= '<div class="progress-bar" role="progressbar" style="width: '.$cnt_waiting_per.'%;" aria-valuenow="'.$cnt_waiting_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_waiting.'</div>';
$user_stats .= '</div>';

$user_stats .= '<p class="mb-0 text-muted small">'.$lang['f_user_select_paused'].'</p>';
$user_stats .= '<div class="progress mb-2">';
$user_stats .= '<div class="progress-bar" role="progressbar" style="width: '.$cnt_paused_per.'%;" aria-valuenow="'.$cnt_paused_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_paused.'</div>';
$user_stats .= '</div>';

$user_stats .= '<p class="mb-0 text-muted small">'.$lang['f_user_select_deleted'].'</p>';
$user_stats .= '<div class="progress mb-2">';
$user_stats .= '<div class="progress-bar" role="progressbar" style="width: '.$cnt_deleted_per.'%;" aria-valuenow="'.$cnt_deleted_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_deleted.'</div>';
$user_stats .= '</div>';
    


$pages_stats .= '<p class="mb-0 text-muted small">'.$lang['f_page_status_puplic'].'</p>';
$pages_stats .=  '<div class="progress mb-2">';
$pages_stats .=  '<div class="progress-bar" role="progressbar" style="width: '.$cnt_public_per.'%;" aria-valuenow="'.$cnt_public_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_public.'</div>';
$pages_stats .=  '</div>';

$pages_stats .=  '<p class="mb-0 text-muted small">'.$lang['f_page_status_ghost'].'</p>';
$pages_stats .=  '<div class="progress mb-2">';
$pages_stats .=  '<div class="progress-bar" role="progressbar" style="width: '.$cnt_ghost_per.'%;" aria-valuenow="'.$cnt_ghost_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_ghost.'</div>';
$pages_stats .=  '</div>';

$pages_stats .=  '<p class="mb-0 text-muted small">'.$lang['f_page_status_private'].'</p>';
$pages_stats .=  '<div class="progress mb-2">';
$pages_stats .=  '<div class="progress-bar" role="progressbar" style="width: '.$cnt_private_per.'%;" aria-valuenow="'.$cnt_private_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_private.'</div>';
$pages_stats .=  '</div>';

$pages_stats .=  '<p class="mb-0 text-muted small">'.$lang['f_page_status_draft'].'</p>';
$pages_stats .=  '<div class="progress mb-2">';
$pages_stats .=  '<div class="progress-bar" role="progressbar" style="width: '.$cnt_draft_per.'%;" aria-valuenow="'.$cnt_draft_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_draft.'</div>';
$pages_stats .=  '</div>';




$tpl_file = str_replace('{pages_list}', $top5pages, $tpl_file);
$tpl_file = str_replace('{posts_list}', $top5posts, $tpl_file);
$tpl_file = str_replace('{comments_list}', $top5comments, $tpl_file);
$tpl_file = str_replace('{user_list}', $user_latest5, $tpl_file);
$tpl_file = str_replace('{pages_stats}', $pages_stats, $tpl_file);
$tpl_file = str_replace('{user_stats}', $user_stats, $tpl_file);

$tpl_file = str_replace('{tab_pages}', $lang['tn_pages'], $tpl_file);
$tpl_file = str_replace('{tab_pages_stats}', $lang['h_status'], $tpl_file);

$tpl_file = str_replace('{tab_posts}', $lang['tn_posts'], $tpl_file);
$tpl_file = str_replace('{tab_comments}', $lang['tn_comments'], $tpl_file);

$tpl_file = str_replace('{tab_user}', $lang['tn_usermanagement'], $tpl_file);
$tpl_file = str_replace('{tab_user_stats}', $lang['h_status'], $tpl_file);

$btn_page_overview = '<a href="acp.php?tn=pages" class="btn btn-fc btn-sm w-100">'.$icon['sitemap'].'</a>';
$btn_new_page = '<a href="acp.php?tn=pages&sub=new" class="btn btn-fc btn-sm w-100">'.$icon['plus'].' '.$lang['new'].'</a>';
$btn_update_index = '<a href="acp.php?tn=dashboard&a=update_index" class="btn btn-fc btn-sm w-100">'.$icon['sync_alt'].' Index</a>';
$btn_delete_cache = '<a href="acp.php?tn=dashboard&a=delete_cache" class="btn btn-fc btn-sm w-100">'.$icon['trash_alt'].' Cache</a>';

$btn_post_overview = '<a href="acp.php?tn=posts" class="btn btn-fc btn-sm w-100">'.$lang['tn_posts'].'</a>';
$btn_new_post = '<a href="acp.php?tn=posts&sub=edit" class="btn btn-fc btn-sm w-100">'.$icon['plus'].' '.$lang['new'].'</a>';
$btn_comments_overview = '<a href="acp.php?tn=comments" class="btn btn-fc btn-sm w-100">'.$lang['tn_comments'].'</a>';

$btn_user_overview = '<a href="acp.php?tn=user" class="btn btn-fc btn-sm w-100">'.$lang['list_user'].'</a>';
$btn_new_user = '<a href="acp.php?tn=user&sub=new" class="btn btn-fc btn-sm w-100">'.$icon['plus'].' '.$lang['new_user'].'</a>';

$tpl_file = str_replace('{btn_page_overview}', $btn_page_overview, $tpl_file);
$tpl_file = str_replace('{btn_new_page}', $btn_new_page, $tpl_file);
$tpl_file = str_replace('{btn_update_index}', $btn_update_index, $tpl_file);
$tpl_file = str_replace('{btn_delete_cache}', $btn_delete_cache, $tpl_file);

$tpl_file = str_replace('{btn_post_overview}', $btn_post_overview, $tpl_file);
$tpl_file = str_replace('{btn_new_post}', $btn_new_post, $tpl_file);
$tpl_file = str_replace('{btn_comments_overview}', $btn_comments_overview, $tpl_file);

$tpl_file = str_replace('{btn_user_overview}', $btn_user_overview, $tpl_file);
$tpl_file = str_replace('{btn_new_user}', $btn_new_user, $tpl_file);

echo $tpl_file;


?>