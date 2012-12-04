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
     $rssItems[] = $row;
   }
}


$cnt_rssItems = count($rssItems);

$ts_now = time();

for($i=0;$i<$cnt_rssItems;$i++) {

	$ts_feed_time = $rssItems[$i]['feed_time'];
	$feed_time = date("d.m.Y H:i:s",$ts_feed_time);
	$feed_id = $rssItems[$i]['feed_id'];
	$feed_title = $rssItems[$i]['feed_title'];
	$feed_text = $rssItems[$i]['feed_text'];
	$feed_url = $rssItems[$i]['feed_url'];
	
	$ts_release = $ts_feed_time + (60*60); // 1 hour
	$ts_diff = $ts_release-$ts_now;
	
	$min = floor($ts_diff/60);
	$sek = $ts_diff - ($min * 60);
	
	$ts_diff_string = " | Release in: $min:$sek ";
	
	if($ts_diff <= 0) {
		$ts_diff = 0;
		$ts_diff_string = "";
	}
	
	echo'<div class="modul_list_items">';
	echo"<h4>$feed_time $ts_diff_string</h4>";
	echo"<h3>$feed_title</h3>";
	echo"$feed_text";
	echo"<p><a href='$feed_url' target='_blank'>$feed_url</a></p>";
	echo"<div class='formfooter'>";
	echo"<a class='btn btn-danger' href='$_SERVER[PHP_SELF]?tn=pages&sub=rss&delete=$feed_id' onclick=\"return confirm('$lang[confirm_delete_data]')\">$lang[delete]</a>";
	echo"</div>";
	echo"</div>";


} // eo $i




if($cnt_rssItems < 1) {
	echo"<div class='alert alert-error'>";
	echo"<p>No entries</p>";
	echo"</div>";
}



?>