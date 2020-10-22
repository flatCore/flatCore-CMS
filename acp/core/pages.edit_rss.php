<?php

//prohibit unauthorized access
require("core/access.php");

echo '<h3>RSS <small>Feed</small></h3>';

//$dbh = new PDO("sqlite:".CONTENT_DB);


if($_REQUEST['delete'] != "") {
	$delete = (int) $_REQUEST['delete'];	
	$db_content->delete("fc_feeds", [
		"feed_id" => $delete
	]);
}

$prefs = get_preferences();

$rssItems = $db_content->select("fc_feeds", "*", [
	"ORDER" => [
		"feed_time" => "DESC"
	]
]);

$cnt_rssItems = count($rssItems);

$ts_now = time();

for($i=0;$i<$cnt_rssItems;$i++) {

	$feed_time = $rssItems[$i]['feed_time'];
	$feed_date = date("d.m.Y H:i:s",$feed_time);
	$feed_id = $rssItems[$i]['feed_id'];
	$feed_title = stripslashes($rssItems[$i]['feed_title']);
	$feed_text = stripslashes($rssItems[$i]['feed_text']);
	$feed_url = $rssItems[$i]['feed_url'];
	
	$ts_release = $feed_time + $prefs['prefs_rss_time_offset'];
	$ts_diff = $ts_release-$ts_now;
	
	$days = floor($ts_diff / 86400);
	$hrs = ($ts_diff / 3600) % 24;
	$mins = ($ts_diff / 60) % 60;
	$secs = ($ts_diff) % 60;
	
	if($hrs < 10) { $hrs = '0'.$hrs; }
	if($mins < 10) { $mins = '0'.$mins; }
	if($secs < 10) { $secs = '0'.$secs; }
	
	$ts_diff_string = " <small>| Release in $days Days | $hrs:$mins:$secs</small>";
	$style_string = 'style="opacity:0.7";';

	if($ts_diff <= 0) {
		$ts_diff = 0;
		$ts_diff_string = "";
		$style_string = '';
	}

	
	echo '<div class="card mb-1">';
	echo '<div class="card-header">';
	echo $feed_date. ' ' .$ts_diff_string;
	echo '</div>';
	echo '<div class="card-body">';
	echo '<h3>'.$feed_title.'</h3>';
	echo $feed_text;
	echo '<p><a href="'.$feed_url.'" target="_blank">'.$feed_url.'</a></p>';
	echo '<hr>';
	echo "<a class='btn btn-fc text-danger' href='acp.php?tn=pages&sub=rss&delete=$feed_id' onclick=\"return confirm('$lang[confirm_delete_data]')\">$lang[delete]</a>";

	echo '</div>';
	echo '</div>';
	


} // eo $i




if($cnt_rssItems < 1) {
	echo"<div class='alert alert-info'>";
	echo"<p>No entries</p>";
	echo"</div>";
}



?>