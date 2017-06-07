<?php

//prohibit unauthorized access
require("core/access.php");


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

$set_status_filter = "user_verified = 'foobar' ";

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
$status_btn_group .= '<a href="acp.php?tn=user&sub=list&switch=statusVerified" class="btn btn-fc btn-sm '.$btn_status_verified.'"><span class="glyphicon glyphicon-ok"></span></a>';
$status_btn_group .= '<a href="acp.php?tn=user&sub=list&switch=statusWaiting" class="btn btn-fc btn-sm '.$btn_status_waiting.'"><span class="glyphicon glyphicon-time"></span></a>';
$status_btn_group .= '<a href="acp.php?tn=user&sub=list&switch=statusPaused" class="btn btn-fc btn-sm '.$btn_status_paused.'"><span class="glyphicon glyphicon-warning-sign"></span></a>';
$status_btn_group .= '<a href="acp.php?tn=user&sub=list&switch=statusDeleted" class="btn btn-fc btn-sm '.$btn_status_deleted.'"><span class="glyphicon glyphicon-ban-circle"></span></a>';
$status_btn_group .= '</div>';


$whereString = "WHERE user_nick != '' ";

if(!empty($_POST['findUser'])) {
	$findUser = strip_tags($_POST['findUser']);
	$search_user = "user_nick LIKE '%$findUser%' ";
}



if($set_status_filter != "") {
	$whereString .= " AND ($set_status_filter) ";
}

if($search_user != "") {
	$whereString .= " AND ($search_user) ";
}

unset($result);

$dbh = new PDO("sqlite:".USER_DB);

$sql = "SELECT user_id, user_nick, user_class, user_firstname, user_lastname, user_registerdate, user_verified, user_mail
    		FROM fc_user
    		$whereString
    		ORDER BY $order_by $way";

foreach ($dbh->query($sql) as $row) {
	$result[] = $row;
}

$cnt_result = count($result);

$dbh = null;

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


$pag_backlink = "<a class='btn btn-primary' href='$_SERVER[PHP_SELF]?tn=user&sub=list&start=$prev_start&sort=$_GET[sort]'>$lang[pagination_backward]</a>";


for($x=0;$x<$cnt_pages;$x++) {

	$page_start = $x*$loop;
	$page_nbr = $x+1;

	if($page_start == $start) {
		$aclass = "btn btn-primary active";
	} else {
		$aclass = "btn btn-primary";
	}

	$pag_string .= "<a class='$aclass' href='$_SERVER[PHP_SELF]?tn=user&sub=list&start=$page_start'>$page_nbr</a> ";
} //eol for $x


$pag_forwardlink = "<a class='btn btn-primary' href='$_SERVER[PHP_SELF]?tn=user&sub=list&start=$next_start&sort=$_GET[sort]'>$lang[pagination_forward]</a>";


echo '<div class="row">';
echo '<div class="col-md-5">';
echo "<form action='acp.php?tn=user' class='form-inline' method='POST'>";

echo '<div class="input-group">';
echo '<span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>';
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

echo"<table class='table table-condensed table-hover table-list' border='0' cellpadding='0' cellspacing='0'>";

echo"<thead><tr>
<th class='head' style='text-align:right;'><a class='darklink' href='$_SERVER[PHP_SELF]?tn=user&sub=list&sort=0&way=$set_way'>ID</a></th>
<th class='head'></th>
<th class='head'><a class='darklink' href='$_SERVER[PHP_SELF]?tn=user&sub=list&sort=1&way=$set_way'>$lang[h_username]</a></th>
<th class='head'><a class='darklink' href='$_SERVER[PHP_SELF]?tn=user&sub=list&sort=2&way=$set_way'>$lang[h_registerdate]</a></th>
<th class='head'><a class='darklink' href='$_SERVER[PHP_SELF]?tn=user&sub=list&sort=3&way=$set_way'>$lang[h_realname]</a></th>
<th class='head'><a class='darklink' href='$_SERVER[PHP_SELF]?tn=user&sub=list&sort=4&way=$set_way'>$lang[h_email]</a></th>
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
	
	$user_avatar = '<img src="images/avatar.png" class="img-circle avatar" width="50" height="50">';
	if(is_file("$user_avatar_path")) {
		$user_avatar = '<img src="'.$user_avatar_path.'" class="img-circle avatar" width="50" height="50">';
	}

	//show me in bold
	unset($td_class);
	if($user_nick == $_SESSION['user_nick']){
		$td_class = "bold";
	}

	//marking admins
	if($user_class == "administrator"){
		$admin_img = '<span style="color:#36a;"><span class="glyphicon glyphicon-user"></span></span>';
	} else {
		$admin_img = '<span class="glyphicon glyphicon-user"></span>';
	}

	//deleted user
	if($user_class == "deleted"){
		$user_nick = "<strike>$user_nick</strike>";
	}


	//status image
	switch ($user_verified) {
		case "waiting":
			$statusLabel = "label label-info center";
			$tr_class = 'info';
			break;
		case "paused":
			$statusLabel = "label label-warning center";
			$tr_class = 'warning';
			break;
		case "verified":
			$statusLabel = "alert alert-success center";
			$tr_class = 'success';
			break;
		case "":
			$tr_class = 'danger';
			$statusLabel = "label label-default center";
			break;
	}
	
	echo"
	<tr class='$tr_class'>
		<td class='$td_class' style='text-align:right;'>$user_id</td>
		<td>$user_avatar</td>
		<td class='$td_class'>$admin_img $user_nick</td>
		<td class='$td_class'>$show_registerdate</td>
		<td class='$td_class'>$user_firstname $user_lastname</td>
		<td class='$td_class'>$user_mail</td>
		<td class='$td_class'><a class='btn btn-default btn-sm' href='$_SERVER[PHP_SELF]?tn=user&sub=edit&edituser=$user_id'>$lang[edit]</a></td>
	</tr>";



} // eol for $i

echo"</table>";


echo '<div id="well well-sm"><p class="text-center">';
echo "$pag_backlink $pag_string $pag_forwardlink";
echo '</p></div>';


?>