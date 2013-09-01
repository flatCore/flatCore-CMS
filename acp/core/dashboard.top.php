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
$cnt_admin = 0;


for($i=0;$i<$cnt_user;$i++) {

	if($user_result[$i][user_verified] == "verified"){
		$cnt_verified++;
	}
	
	if($user_result[$i][user_verified] == "paused"){
		$cnt_paused++;
	}
	
	if($user_result[$i][user_class] == "administrator"){
		$cnt_admin++;
	}
	
	
	if($i < 5) {
		$user_registerdate = @date("d.m.Y",$user_result[$i][user_registerdate]);
		$user_id = $user_result[$i][user_id];
		$user_nick = $user_result[$i][user_nick];
		$user_name = $user_result[$i][user_firstname] . " " . $result[$i][user_lastname];
	
	if($user_result[$i][user_class] == "deleted"){
		$user_nick = "<strike>$user_nick</strike>";
	}
	
	$user_latest5 .=  "$user_registerdate » <a href='acp.php?tn=user&sub=edit&edituser=$user_id' title='$lang[edit_user]'>$user_nick</a> $user_name<br />"; 
	}



} // eol $i




/* get latest info from pages database */

$dbh = new PDO("sqlite:".CONTENT_DB);

$sql = "SELECT page_id, page_linkname, page_title, page_sort, page_lastedit, page_lastedit_from, page_status 
		FROM fc_pages
		ORDER BY page_lastedit DESC";

unset($result);

   foreach ($dbh->query($sql) as $row) {
     $result[] = $row;
   }

$cnt_result = count($result);

$dbh = null;

for($i=0;$i<$cnt_result;$i++) {

	$page_id = $result[$i][page_id];
	
	if($result[$i][page_status] == "public"){
		$cnt_public++;
	}
	
	if($result[$i][page_status] == "draft"){
		$cnt_draft++;
	}

	
	if($i < 5) {
		$last_edit = @date("d.m.Y",$result[$i][page_lastedit]);
		$page_linkname = $result[$i][page_linkname];
		$page_title = first_words($result[$i][page_title],4);
	
		$top5pages .=  "$last_edit » <a href='acp.php?tn=pages&sub=edit&editpage=$page_id' title='$lang[page_edit]'>$page_linkname</a> $page_title<br />"; 
	}


} // eol $i







echo'<div class="row-fluid">';

echo"<div class='span4'>";
echo '<fieldset style="min-height:140px;">';
echo"<legend>$lang[db_user]</legend>";

echo"<p class='loud'><b>$cnt_user</b> Benutzer gesamt</p>
			<ul class='unstyled'>
				<li><span class='label label-success'>$cnt_verified</span> $lang[f_user_select_verified]</li>
				<li><span class='label label-important'>$cnt_paused</span> $lang[f_user_select_paused]</li>
				<li><span class='label'>$cnt_admin</span> $lang[f_administrators]</li>
			</ul>";
echo '</fieldset>';
echo"</div>";


echo"<div class='span4'>";
echo '<fieldset style="min-height:140px;">';
echo"<legend>zuletzt registriert</legend>";
echo"$user_latest5";
echo '</fieldset>';
echo"</div>";


echo"<div class='span4'>";
echo '<fieldset style="min-height:140px;">';
echo"<legend>zuletzt aktualisiert</legend>";
echo"$top5pages";
echo '</fieldset>';
echo"</div>";






echo'</div>';



?>