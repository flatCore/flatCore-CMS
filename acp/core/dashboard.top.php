<?php

//prohibit unauthorized access
require 'core/access.php';


/* get latest infos from user database */
$dbh = new PDO("sqlite:".USER_DB);

$sql = "SELECT user_id, user_nick, user_class, user_firstname, user_lastname, user_registerdate, user_verified, user_mail
					FROM fc_user
					ORDER BY user_id DESC ";

foreach ($dbh->query($sql) as $row) {
	$user_result[] = $row;
}

$cnt_user = count($user_result);

$dbh = null;

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
		$user_latest5 .= '<h6 class="mb-1">'.$user_nick.'</h6>';
		$user_latest5 .= '<small>'.$user_registerdate.'</small>';
		$user_latest5 .= '</div>';
		$user_latest5 .= '<small>'.$user_name.'</small>';
		$user_latest5 .= '</a>';
	}

}

$user_latest5 = '<div class="list-group list-group-flush">'.$user_latest5.'</div>';

$cnt_verified_per = round($cnt_verified*100/$cnt_user);
$cnt_paused_per = round($cnt_paused*100/$cnt_user);
$cnt_deleted_per = round($cnt_deleted*100/$cnt_user);
$cnt_waiting_per = round($cnt_waiting*100/$cnt_user);


/* get latest info from pages database */

$dbh = new PDO("sqlite:".CONTENT_DB);

$sql = "SELECT page_id, page_linkname, page_title, page_sort, page_lastedit, page_lastedit_from, page_status 
				FROM fc_pages ORDER BY page_lastedit DESC";

unset($allPages);

foreach ($dbh->query($sql) as $row) {
	$allPages[] = $row;
}

$cnt_pages = count($allPages);

$dbh = null;

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

		
		$top5pages .= '<a href="acp.php?tn=pages&sub=edit&editpage='.$allPages[$i]['page_id'].'" class="list-group-item list-group-item-ghost list-group-item-action flex-column align-items-start">';
		$top5pages .= '<div class="d-flex w-100 justify-content-between">';
		$top5pages .= '<h6 class="mb-1">'.$page_linkname.'</h6>';
		$top5pages .= '<small>'.$last_edit.'</small>';
		$top5pages .= '</div>';
		$top5pages .= '<small>'.$page_title.'</small>';
		$top5pages .= '</a>'; 
	}
	
} // eol $i

$top5pages = '<div class="list-group list-group-flush">'.$top5pages.'</div>';

$cnt_public_per = round($cnt_public*100/$cnt_pages);
$cnt_draft_per = round($cnt_draft*100/$cnt_pages);
$cnt_ghost_per = round($cnt_ghost*100/$cnt_pages);
$cnt_private_per = round($cnt_private*100/$cnt_pages);

echo'<div class="card-deck mb-1">';

echo '<div class="card">';
echo '<div class="card-header">'.$lang['db_user'].' <span class="badge badge-light float-right">'.$cnt_user.'</span></div>';
echo '<div class="card-body">';
echo '<div class="row">';
echo '<div class="col-lg-12">';

echo '<p class="mb-0">'.$lang['f_user_select_verified'].'</p>';
echo '<div class="progress mb-2">';
echo '<div class="progress-bar" role="progressbar" style="width: '.$cnt_verified_per.'%;" aria-valuenow="'.$cnt_verified_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_verified.'</div>';
echo '</div>';

echo '<p class="mb-0">'.$lang['f_user_select_waiting'].'</p>';
echo '<div class="progress mb-2">';
echo '<div class="progress-bar" role="progressbar" style="width: '.$cnt_waiting_per.'%;" aria-valuenow="'.$cnt_waiting_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_waiting.'</div>';
echo '</div>';

echo '<p class="mb-0">'.$lang['f_user_select_paused'].'</p>';
echo '<div class="progress mb-2">';
echo '<div class="progress-bar" role="progressbar" style="width: '.$cnt_paused_per.'%;" aria-valuenow="'.$cnt_paused_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_paused.'</div>';
echo '</div>';

echo '<p class="mb-0">'.$lang['f_user_select_deleted'].'</p>';
echo '<div class="progress mb-2">';
echo '<div class="progress-bar" role="progressbar" style="width: '.$cnt_deleted_per.'%;" aria-valuenow="'.$cnt_deleted_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_deleted.'</div>';
echo '</div>';
    



echo '</div>';

echo '</div>'; // row
echo '</div>';
echo '</div>';

echo '<div class="card">';
echo '<div class="card-header">'.$lang['h_latest_user'].'</div>';
echo $user_latest5;
echo '</div>';


echo '<div class="card">';
echo '<div class="card-header">'.$lang['tn_pages'].' <span class="badge badge-light float-right">'.$cnt_pages.'</span></div>';
echo '<div class="card-body equal" data-mh="panel-body-group">';
echo '<div class="row">';


echo '<div class="col-lg-12">';

echo '<p class="mb-0">'.$lang['f_page_status_puplic'].'</p>';
echo '<div class="progress mb-2">';
echo '<div class="progress-bar" role="progressbar" style="width: '.$cnt_public_per.'%;" aria-valuenow="'.$cnt_public_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_public.'</div>';
echo '</div>';

echo '<p class="mb-0">'.$lang['f_page_status_ghost'].'</p>';
echo '<div class="progress mb-2">';
echo '<div class="progress-bar" role="progressbar" style="width: '.$cnt_ghost_per.'%;" aria-valuenow="'.$cnt_ghost_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_ghost.'</div>';
echo '</div>';

echo '<p class="mb-0">'.$lang['f_page_status_private'].'</p>';
echo '<div class="progress mb-2">';
echo '<div class="progress-bar" role="progressbar" style="width: '.$cnt_private_per.'%;" aria-valuenow="'.$cnt_private_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_private.'</div>';
echo '</div>';

echo '<p class="mb-0">'.$lang['f_page_status_draft'].'</p>';
echo '<div class="progress mb-2">';
echo '<div class="progress-bar" role="progressbar" style="width: '.$cnt_draft_per.'%;" aria-valuenow="'.$cnt_draft_per.'" aria-valuemin="0" aria-valuemax="100">'.$cnt_draft.'</div>';
echo '</div>';


echo '</div>';

echo '</div>'; // row
echo '</div>';
echo '</div>';


echo '<div class="card">';
echo '<div class="card-header">'.$lang['h_last_edit'].'</div>';
echo $top5pages;
echo '</div>';


echo'</div>'; // row


?>