<?php

//prohibit unauthorized access
require 'core/access.php';

$sort = (int) $_GET['sort'];

// sort by reference
switch ($_GET['sort']) {
case "1":
    $order_by = "user_nick";
    break;
case "2":
    $order_by = "user_registerdate";
    break;
case "3":
    $order_by = "user_lastname";
    break;
case "4":
    $order_by = "user_mail";
    break;
case "5":
    $order_by = "user_verified";
    break;
default:
	$order_by = "user_id";
}

/* sort up or down */

if($_GET['way'] == "up"){
	$way = "ASC";
	$set_way = "down";
} else {
	$way = "DESC";
	$set_way = "up";
}

/* switch user status */

if(isset($_GET['switch'])) {
	$_SESSION['set_user_status'] = true;
}

if($_SESSION['checked_verified'] == '' AND $_SESSION['checked_waiting'] == '' AND $_SESSION['checked_paused'] == '' AND $_SESSION['set_user_status'] == false) {
	$_SESSION['checked_verified'] = 'checked';
}


if($_GET['switch'] == 'statusWaiting' AND $_SESSION['checked_waiting'] == '') {
	$_SESSION['checked_waiting'] = "checked";
} elseif($_GET['switch'] == 'statusWaiting' AND $_SESSION['checked_waiting'] == 'checked') {
	$_SESSION['checked_waiting'] = "";
}

if($_GET['switch'] == 'statusPaused' && $_SESSION['checked_paused'] == 'checked') {
	$_SESSION['checked_paused'] = "";
} elseif($_GET['switch'] == 'statusPaused' && $_SESSION['checked_paused'] == '') {
	$_SESSION['checked_paused'] = "checked";
}

if($_GET['switch'] == 'statusVerified' && $_SESSION['checked_verified'] == 'checked') {
	$_SESSION['checked_verified'] = "";
} elseif($_GET['switch'] == 'statusVerified' && $_SESSION['checked_verified'] == '') {
	$_SESSION['checked_verified'] = "checked";
}

if($_GET['switch'] == 'statusDeleted' && $_SESSION['checked_deleted'] == 'checked') {
	$_SESSION['checked_deleted'] = "";
} elseif($_GET['switch'] == 'statusDeleted' && $_SESSION['checked_deleted'] == '') {
	$_SESSION['checked_deleted'] = "checked";
}

$set_status_filter = "user_id != NULL ";

if($_SESSION['checked_waiting'] == "checked") {
	$set_status_filter .= "OR user_verified = 'waiting' ";
	$btn_status_waiting = 'active';
}

if($_SESSION['checked_paused'] == "checked") {
	$set_status_filter .= "OR user_verified = 'paused' ";
	$btn_status_paused = 'active';
}

if($_SESSION['checked_verified'] == "checked") {
	$set_status_filter .= "OR user_verified = 'verified' ";
	$btn_status_verified = 'active';
}

if($_SESSION['checked_deleted'] == "checked") {
	$set_status_filter .= "OR user_verified = '' ";
	$btn_status_deleted = 'active';
}



$status_btn_group  = '<div class="btn-group">';
$status_btn_group .= '<a href="acp.php?tn=user&sub=list&switch=statusVerified" class="btn btn-fc '.$btn_status_verified.'">'.$icon['user_check'].'</span></a>';
$status_btn_group .= '<a href="acp.php?tn=user&sub=list&switch=statusWaiting" class="btn btn-fc '.$btn_status_waiting.'">'.$icon['user_clock'].'</a>';
$status_btn_group .= '<a href="acp.php?tn=user&sub=list&switch=statusPaused" class="btn btn-fc '.$btn_status_paused.'">'.$icon['user_lock'].'</a>';
$status_btn_group .= '<a href="acp.php?tn=user&sub=list&switch=statusDeleted" class="btn btn-fc '.$btn_status_deleted.'">'.$icon['user_slash'].'</a>';
$status_btn_group .= '</div>';


$whereString = "WHERE user_nick != '' ";

if(!empty($_POST['findUser'])) {
	$find_user = "%".strip_tags($_POST['findUser'])."%";
	$search_user = "user_nick LIKE '$find_user' ";
}



if($set_status_filter != "") {
	$whereString .= " AND ($set_status_filter) ";
}

if($search_user != "") {
	$whereString .= " AND ($search_user) ";
}

unset($result);

$sql = "SELECT user_id, user_nick, user_class, user_firstname, user_lastname, user_registerdate, user_verified, user_mail
    		FROM fc_user
    		$whereString
    		ORDER BY $order_by $way";
    		

$result = $db_user->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$cnt_result = count($result);

// number to show
$loop = 20;


$start = 0;
if(isset($_GET['start'])) {
	$start = (int) $_GET['start'];
}

$cnt_pages = ceil($cnt_result/$loop);

if($start<0) {
	$start = 0;
}

$end = $start+$loop;

if($end > $cnt_result) {
	$end = $cnt_result;
}


//next step
$next_start = $end;
$prev_start = $start-$loop;



if($start>($cnt_result-$loop)) {
	$next_start = $start;
}

if($prev_start <= 0){
	$prev_start = 0;
}


$pag_backlink = "<a class='btn btn-fc' href='acp.php?tn=user&sub=list&start=$prev_start&sort=$sort'>$lang[pagination_backward]</a>";


for($x=0;$x<$cnt_pages;$x++) {

	$page_start = $x*$loop;
	$page_nbr = $x+1;

	if($page_start == $start) {
		$aclass = "btn btn-fc active";
	} else {
		$aclass = "btn btn-fc";
	}

	$pag_string .= "<a class='$aclass' href='acp.php?tn=user&sub=list&start=$page_start'>$page_nbr</a> ";
}


$pag_forwardlink = "<a class='btn btn-fc' href='acp.php?tn=user&sub=list&start=$next_start&sort=$sort'>$lang[pagination_forward]</a>";


echo '<div class="row">';
echo '<div class="col-md-5">';
echo "<form action='acp.php?tn=user' class='form-inline' method='POST'>";

echo '<div class="input-group">';
echo '<div class="input-group-prepend">';
echo '<span class="input-group-text">'.$icon['search'].'</span>';
echo '</div>';
echo '<input type="text" name="findUser" class="form-control" placeholder="Filter">';
echo '</div>';
echo "</form>";

echo '</div>';
echo '<div class="col-md-7">';
echo '<div style="float:right;">';
echo $status_btn_group;
echo '</div>';
echo '<div class="clearfix"></div>';
echo '</div>';
echo '</div><br>';



//print the list

echo"<table class='table table-hover table-striped table-sm'>";

echo"<thead><tr>
<th class='head' style='text-align:right;'><a class='darklink' href='acp.php?tn=user&sub=list&sort=0&way=$set_way'>ID</a></th>
<th class='head'></th>
<th class='head'><a class='' href='acp.php?tn=user&sub=list&sort=1&way=$set_way'>$lang[h_username]</a></th>
<th class='head'><a class='' href='acp.php?tn=user&sub=list&sort=2&way=$set_way'>$lang[h_registerdate]</a></th>
<th class='head'><a class='' href='acp.php?tn=user&sub=list&sort=3&way=$set_way'>$lang[h_realname]</a></th>
<th class='head'><a class='' href='acp.php?tn=user&sub=list&sort=4&way=$set_way'>$lang[h_email]</a></th>
<th class='head'>$lang[h_action]</th>
</tr></thead>";

for($i=$start;$i<$end;$i++) {

	$user_id = $result[$i]['user_id'];
	$user_nick = $result[$i]['user_nick'];
	$user_avatar_path = '../'. FC_CONTENT_DIR . '/avatars/' . md5($user_nick) . '.png';
	$user_class = $result[$i]['user_class'];
	$user_mail = $result[$i]['user_mail'];
	$user_registerdate = $result[$i]['user_registerdate'];
	$user_firstname = $result[$i]['user_firstname'];
	$user_lastname = $result[$i]['user_lastname'];
	$user_verified = $result[$i]['user_verified'];
	$user_groups = $result[$i]['user_groups'];
	$show_registerdate = @date("d.m.Y",$user_registerdate);
	
	$user_avatar = '<img src="images/avatar.png" class="rounded-circle avatar" width="50" height="50">';
	if(is_file("$user_avatar_path")) {
		$user_avatar = '<img src="'.$user_avatar_path.'" class="rounded-circle avatar" width="50" height="50">';
	}

	//show me in bold
	unset($td_class);
	if($user_nick == $_SESSION['user_nick']){
		$td_class = "bold";
	}

	//marking admins
	if($user_class == "administrator"){
		$admin_img = '<span style="color:#36a;">'.$icon['user'].'</span>';
	} else {
		$admin_img = $icon['user'];
	}

	//deleted user
	if($user_class == "deleted"){
		$user_nick = "<strike>$user_nick</strike>";
	}


	//status image
	switch ($user_verified) {
		case "waiting":
			$statusLabel = "label label-info center";
			$bg_class = 'table-info';
			$label = 'badge badge-pill badge-info';
			break;
		case "paused":
			$statusLabel = "label label-warning center";
			$label = 'badge badge-pill badge-warning';
			$bg_class = 'table-warning';
			break;
		case "verified":
			$statusLabel = "alert alert-success center";
			$bg_class = 'table-success';
			$label = 'badge badge-pill badge-success';
			break;
		case "":
			$bg_class = 'table-danger';
			$statusLabel = "label label-default center";
			$label = 'badge badge-pill badge-danger';
			break;
	}
	
	echo '<tr class="'.$tr_class.'">';
	echo '<td class="'.$td_class.'" style="text-align:right;">'.$user_id.'</td>';
	echo '<td>'.$user_avatar.'</td>';
	echo '<td class="lead '.$td_class.'">'.$admin_img.' <span class="'.$label.'">'.$user_nick.'</span></td>';
	echo '<td class="'.$td_class.'">'.$show_registerdate.'</td>';
	echo '<td class="'.$td_class.'">'.$user_firstname.' '.$user_lastname.'</td>';
	echo '<td class="'.$td_class.'">'.$user_mail.'</td>';
	echo '<td class="'.$td_class.'"><a class="btn btn-sm btn-fc btn-block" href="acp.php?tn=user&sub=edit&edituser='.$user_id.'">'.$icon['edit'].' '.$lang['edit'].'</a></td>';
	echo '</tr>';




} // eol for $i

echo"</table>";


echo '<div id="well well-sm"><p class="text-center">';
echo "$pag_backlink $pag_string $pag_forwardlink";
echo '</p></div>';


?>
