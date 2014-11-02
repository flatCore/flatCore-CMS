<?php

//prohibit unauthorized access
require("core/access.php");


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
		$user_latest5 .= '<dt>'.$user_registerdate.'</dt>';
		$user_latest5 .=  "<dd><a href='acp.php?tn=user&sub=edit&edituser=$user_id' title='$lang[edit_user]'>$user_nick</a> <small>$user_name</small></dd>"; 
	}

} // eol $i

$user_latest5 = '<dl class="dl-horizontal dl-dates">'.$user_latest5.'</dl>';


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
	
		$top5pages .= '<dt>'.$last_edit.'</dt>';
		$top5pages .= "<dd><a href='acp.php?tn=pages&sub=edit&editpage=$page_id' title='$lang[page_edit]'>$page_linkname</a> <small>$page_title</small></dd>"; 
	}
	
} // eol $i

$top5pages = '<dl class="dl-horizontal dl-dates">'.$top5pages.'</dl>';



echo'<div class="row equal">';
echo '<div class="col-md-3">';

echo '<div class="panel panel-default">';
echo '<div class="panel-heading">'.$lang['db_user'].' '. ($cnt_user).'</div>';
echo '<div class="panel-body" data-mh="panel-body-group">';
echo '<div class="row">';
echo '<div class="col-lg-6"><div class="canvas-holder"><canvas id="user-chart-area" width="30" height="30"/></canvas></div></div>';
echo '<div class="col-lg-6"><div id="user-chart-legend" class="hidden-sm"></div></div>';
echo '</div>'; // row
echo '</div>';
echo '</div>';

echo"</div>";
echo '<div class="col-md-3">';

echo '<div class="panel panel-default">';
echo '<div class="panel-heading">'.$lang['h_latest_user'].'</div>';
echo '<div class="panel-body" data-mh="panel-body-group">';
echo"$user_latest5";
echo '</div>';
echo '</div>';

echo"</div>";
echo '<div class="col-md-3">';

echo '<div class="panel panel-default">';
echo '<div class="panel-heading">'.$lang['tn_pages'].' '. ($cnt_pages).'</div>';
echo '<div class="panel-body" data-mh="panel-body-group">';
echo '<div class="row">';
echo '<div class="col-lg-6"><div class="canvas-holder"><canvas id="pages-chart-area" width="30" height="30"/></canvas></div></div>';
echo '<div class="col-lg-6"><div id="pages-chart-legend" vlass="hidden-sm"></div></div>';
echo '</div>'; // row
echo '</div>';
echo '</div>';

echo"</div>";
echo '<div class="col-md-3">';

echo '<div class="panel panel-default">';
echo '<div class="panel-heading">'.$lang['h_last_edit'].'</div>';
echo '<div class="panel-body" data-mh="panel-body-group">';
echo"$top5pages";
echo '</div>';
echo '</div>';

echo"</div>";

echo'</div>'; // row


$charts_script = file_get_contents('templates/script-dashboard-charts.tpl');

$charts_script = str_replace('{label_user_verified}', $lang['f_user_select_verified'], $charts_script);
$charts_script = str_replace('{label_user_waiting}', $lang['f_user_select_waiting'], $charts_script);
$charts_script = str_replace('{label_user_paused}', $lang['f_user_select_paused'], $charts_script);
$charts_script = str_replace('{label_user_deleted}', $lang['f_user_select_deleted'], $charts_script);

$charts_script = str_replace('{cnt_user_verified}', $cnt_verified, $charts_script);
$charts_script = str_replace('{cnt_user_waiting}', $cnt_waiting, $charts_script);
$charts_script = str_replace('{cnt_user_paused}', $cnt_paused, $charts_script);
$charts_script = str_replace('{cnt_user_deleted}', $cnt_deleted, $charts_script);

$charts_script = str_replace('{label_pages_public}', $lang['f_page_status_puplic'], $charts_script);
$charts_script = str_replace('{label_pages_draft}', $lang['f_page_status_draft'], $charts_script);
$charts_script = str_replace('{label_pages_ghost}', $lang['f_page_status_ghost'], $charts_script);
$charts_script = str_replace('{label_pages_private}', $lang['f_page_status_private'], $charts_script);

$charts_script = str_replace('{cnt_pages_public}', $cnt_public, $charts_script);
$charts_script = str_replace('{cnt_pages_draft}', $cnt_draft, $charts_script);
$charts_script = str_replace('{cnt_pages_ghost}', $cnt_ghost, $charts_script);
$charts_script = str_replace('{cnt_pages_private}', $cnt_private, $charts_script);


echo "$charts_script";


?>