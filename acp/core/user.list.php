<?php

//prohibit unauthorized access
require("core/access.php");


// sort by reference
switch ($_GET[sort]) {
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

if($_GET[way] == "up"){
	$way = "ASC";
	$set_way = "down";
} else {
	$way = "DESC";
	$set_way = "up";
}


/* FILTER BY SEARCH FORM */

if(!empty($_POST['findUser'])) {
	$findUser = strip_tags($_POST['findUser']);
	$whereString = "WHERE user_nick LIKE '%$findUser%' ";
} else {
	$whereString = "";
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
if(isset($_GET[start])) {
	$start = (int) $_GET[start];
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


$pag_backlink = "<a class='buttonLink' href='$_SERVER[PHP_SELF]?tn=user&sub=list&start=$prev_start&sort=$_GET[sort]'>$lang[pagination_backward]</a>";


for($x=0;$x<$cnt_pages;$x++) {

	$page_start = $x*$loop;
	$page_nbr = $x+1;

	if($page_start == $start) {
		$aclass = "buttonLink_sel";
	} else {
		$aclass = "buttonLink";
	}

	$pag_string .= "<a class='$aclass' href='$_SERVER[PHP_SELF]?tn=user&sub=list&start=$page_start'>$page_nbr</a> ";
} //eol for $x


$pag_forwardlink = "<a class='buttonLink' href='$_SERVER[PHP_SELF]?tn=user&sub=list&start=$next_start&sort=$_GET[sort]'>$lang[pagination_forward]</a>";


//print the list

echo"<table class='table-list' border='0' cellpadding='0' cellspacing='0'>";

echo"<tr>
<td class='head' style='text-align:right;'><a class='darklink' href='$_SERVER[PHP_SELF]?tn=user&sub=list&sort=0&way=$set_way'>ID</a></td>
<td class='head'><a class='darklink' href='$_SERVER[PHP_SELF]?tn=user&sub=list&sort=1&way=$set_way'>$lang[h_username]</a></td>
<td class='head'><a class='darklink' href='$_SERVER[PHP_SELF]?tn=user&sub=list&sort=2&way=$set_way'>$lang[h_registerdate]</a></td>
<td class='head'><a class='darklink' href='$_SERVER[PHP_SELF]?tn=user&sub=list&sort=3&way=$set_way'>$lang[h_realname]</a></td>
<td class='head'><a class='darklink' href='$_SERVER[PHP_SELF]?tn=user&sub=list&sort=4&way=$set_way'>$lang[h_email]</a></td>
<td class='head'>$lang[h_action]</td>
</tr>";

for($i=$start;$i<$end;$i++) {

	$user_id = $result[$i][user_id];
	$user_nick = $result[$i][user_nick];
	$user_class = $result[$i][user_class];
	$user_mail = $result[$i][user_mail];
	$user_registerdate = $result[$i][user_registerdate];
	$user_firstname = $result[$i][user_firstname];
	$user_lastname = $result[$i][user_lastname];
	$user_verified = $result[$i][user_verified];
	$user_groups = $result[$i][user_groups];
	$show_registerdate = @date("d.m.Y",$user_registerdate);

	//show me in bold
	unset($td_class);
	if($user_nick == "$_SESSION[user_nick]"){
		$td_class = "bold";
	}

	//marking admins
	if($user_class == "administrator"){
		$admin_img = '<span style="color:#369;"><span class="glyphicon glyphicon-user"></span></span>';
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
			break;
		case "paused":
			$statusLabel = "label label-important center";
			break;
		case "verified":
			$statusLabel = "label label-success center";
			break;
	}
	
	echo"
	<tr>
		<td class='$td_class'><p class='$statusLabel'>$user_id</p></td>
		<td class='$td_class'>$admin_img $user_nick</td>
		<td class='$td_class'>$show_registerdate</td>
		<td class='$td_class'>$user_firstname $user_lastname</td>
		<td class='$td_class'>$user_mail</td>
		<td class='$td_class'><a class='btn btn-default btn-sm' href='$_SERVER[PHP_SELF]?tn=user&sub=edit&edituser=$user_id'>$lang[edit]</a></td>
	</tr>";



} // eol for $i

echo"</table>";


echo"<div id='pagina'><p>";
echo"$pag_backlink $pag_string $pag_forwardlink";
echo"</p></div>";


?>