<?php

//prohibit unauthorized access
require("core/access.php");

$dbh = new PDO("sqlite:".CONTENT_DB);



if($_REQUEST[delete] != "") {
	$delete = (int) $_REQUEST[delete];
	$sql = "DELETE FROM fc_feeds WHERE feed_id = $delete";
	$cnt_changes = $dbh->exec($sql);
}




$sql = "SELECT * FROM fc_feeds ORDER BY feed_time DESC";

if($dbh) {
   foreach($dbh->query($sql) as $row) {
     $result[] = $row;
   }
}


$cnt_result = count($result);


for($i=0;$i<$cnt_result;$i++) {

	$feed_time = date("d.m.Y H:i:s",$result[$i]['feed_time']);
	$feed_id = $result[$i]['feed_id'];
	$feed_title = $result[$i]['feed_title'];
	$feed_text = $result[$i]['feed_text'];
	$feed_url = $result[$i]['feed_url'];
	
	
	echo'<div class="modul_list_items">' . $feed_time;
	echo"<h3>$feed_title</h3>";
	echo"$feed_text";
	echo"<a href='$feed_url' target='_blank'>$feed_url</a>";
	echo"<div class='formfooter'>";
	echo"<a class='btn btn-danger' href='$_SERVER[PHP_SELF]?tn=pages&sub=rss&delete=$feed_id' onclick=\"return confirm('$lang[confirm_delete_data]')\">$lang[delete]</a>";
	echo"</div>";
	echo"</div>";


} // eo $i




if($cnt_result < 1) {
	echo"<div class='alert alert-error'>";
	echo"<p>No entries</p>";
	echo"</div>";
}























?>