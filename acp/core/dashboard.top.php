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
	
		$top5pages .= '<dt>'.$last_edit.'</dt>';
		$top5pages .= "<dd><a href='acp.php?tn=pages&sub=edit&editpage=$page_id' title='$lang[page_edit]'>$page_linkname</a> <small>$page_title</small></dd>"; 
	}
	
} // eol $i

$top5pages = '<dl class="dl-horizontal dl-dates">'.$top5pages.'</dl>';

$cnt_public_per = round($cnt_public*100/$cnt_pages);
$cnt_draft_per = round($cnt_draft*100/$cnt_pages);
$cnt_ghost_per = round($cnt_ghost*100/$cnt_pages);
$cnt_private_per = round($cnt_private*100/$cnt_pages);

echo'<div class="row">';
echo '<div class="hidden-xs col-sm-4 col-md-3">';

echo '<div class="panel panel-default">';
echo '<div class="panel-heading">'.$lang['db_user'].' <span class="badge pull-right">'.$cnt_user.'</span></div>';
echo '<div class="panel-body equal" data-mh="panel-body-group">';
echo '<div class="row">';
echo '<div class="col-lg-12">';


    
echo '<div class="charts">';
echo '<div class="chart">';

echo '<ul class="chart-hor">';
echo '<li>
        	<div class="chart_bar chart_bar_verified" data-skill="'.$cnt_verified_per.'" title="'.$lang['f_user_select_verified'].'"></div>
        	<span class="chart_label">'.$lang['f_user_select_verified'].' ('.$cnt_verified.')</span>
      </li>';
echo '<li>
        	<div class="chart_bar chart_bar_queue" data-skill="'.$cnt_waiting_per.'" title="'.$lang['f_user_select_waiting'].'"></div>
        	<span class="chart_label">'.$lang['f_user_select_waiting'].' ('.$cnt_waiting.')</span>
      </li>';
echo '<li>
        	<div class="chart_bar chart_bar_banned" data-skill="'.$cnt_paused_per.'" title="'.$lang['f_user_select_paused'].'"></div>
        	<span class="chart_label">'.$lang['f_user_select_paused'].' ('.$cnt_paused.')</span>
        </li>';
echo '<li>
        	<div class="chart_bar chart_bar_deleted" data-skill="'.$cnt_deleted_per.'" title="'.$lang['f_user_select_deleted'].'"></div>
        	<span class="chart_label">'.$lang['f_user_select_deleted'].' ('.$cnt_deleted.')</span>
        </li>';
echo '</ul>';
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>'; // row
echo '</div>';
echo '</div>';

echo"</div>";
echo '<div class="col-xs-12 col-sm-8 col-md-3">';

echo '<div class="panel panel-default">';
echo '<div class="panel-heading">'.$lang['h_latest_user'].'</div>';
echo '<div class="panel-body equal" data-mh="panel-body-group">';
echo"$user_latest5";
echo '</div>';
echo '</div>';

echo"</div>";
echo '<div class="hidden-xs col-sm-4 col-md-3">';

echo '<div class="panel panel-default">';
echo '<div class="panel-heading">'.$lang['tn_pages'].' <span class="badge pull-right">'.$cnt_pages.'</span></div>';
echo '<div class="panel-body equal" data-mh="panel-body-group">';
echo '<div class="row">';


echo '<div class="col-lg-12">';


    
echo '<div class="charts">';
echo '<div class="chart">';

echo '<ul class="chart-hor">';
echo '<li>
        	<div class="chart_bar chart_bar_public" data-skill="'.$cnt_public_per.'" title="'.$lang['f_page_status_puplic'].'"></div>
        	<span class="chart_label">'.$lang['f_page_status_puplic'].' ('.$cnt_public.')</span>
      </li>';
echo '<li>
        	<div class="chart_bar chart_bar_ghost" data-skill="'.$cnt_ghost_per.'" title="'.$lang['f_page_status_ghost'].'"></div>
        	<span class="chart_label">'.$lang['f_page_status_ghost'].' ('.$cnt_ghost.')</span>
        </li>';
echo '<li>
        	<div class="chart_bar chart_bar_private" data-skill="'.$cnt_private_per.'" title="'.$lang['f_page_status_private'].'"></div>
        	<span class="chart_label">'.$lang['f_page_status_private'].' ('.$cnt_private.')</span>
        </li>';
echo '<li>
        	<div class="chart_bar chart_bar_draft" data-skill="'.$cnt_draft_per.'" title="'.$lang['f_page_status_draft'].'"></div>
        	<span class="chart_label">'.$lang['f_page_status_draft'].' ('.$cnt_draft.')</span>        	
      </li>';
echo '</ul>';
echo '</div>';
echo '</div>';

echo '</div>';


echo '</div>'; // row
echo '</div>';
echo '</div>';

echo"</div>";
echo '<div class="col-xs-12 col-sm-8 col-md-3">';

echo '<div class="panel panel-default">';
echo '<div class="panel-heading">'.$lang['h_last_edit'].'</div>';
echo '<div class="panel-body equal" data-mh="panel-body-group">';
echo"$top5pages";
echo '</div>';
echo '</div>';

echo"</div>";

echo'</div>'; // row


?>